<?php

namespace Anomaly\FilesFieldType\Http\Controller;

use Illuminate\Support\Arr;
use Anomaly\FilesFieldType\Support\ConfigCache;
use Anomaly\FilesModule\File\FileSanitizer;
use Anomaly\FilesModule\File\FileUploader;
use Anomaly\FilesFieldType\Support\AllowedFolders;
use Anomaly\FilesFieldType\Table\UploadTableBuilder;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\Streams\Platform\Support\Authorizer;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;

/**
 * Class UploadController
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class UploadController extends AdminController
{

    /**
     * Return the uploader.
     *
     * @param UploadTableBuilder $table
     * @param $folder
     * @param $key
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function index(UploadTableBuilder $table, Authorizer $authorizer, $folder, $key)
    {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        /* @var FolderInterface $folder */
        $folder = dispatch_sync(new GetFolder($folder));

        if (!$folder || !AllowedFolders::permits($config, $folder->getId())) {
            abort(404);
        }

        $allowed = array_intersect(
            Arr::get($config, 'allowed_types', []),
            $folder->getAllowedTypes()
        );

        return $this->view->make(
            'anomaly.field_type.files::upload/index',
            [
                'allowed' => $allowed ?: $folder->getAllowedTypes(),
                'table'   => $table->setAllowedFolders(AllowedFolders::ids($config))->make()->getTable(),
                'folder'  => $folder,
                'config'  => $config,
                'key'     => $key,
            ]
        );
    }

    /**
     * Upload a file.
     *
     * @param  FileUploader $uploader
     * @param  FolderRepositoryInterface $folders
     * @param  Authorizer $authorizer
     * @param  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(
        FileUploader $uploader,
        FolderRepositoryInterface $folders,
        Authorizer $authorizer,
        $key
    ) {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        if (!$file = $this->request->file('upload')) {
            return $this->response->json(['message' => 'No file was uploaded.'], 422);
        }

        if (!$folder = $folders->find($this->request->get('folder'))) {
            return $this->response->json(['message' => 'The folder could not be found.'], 404);
        }

        if (!AllowedFolders::permits($config, $folder->getId())) {
            return $this->response->json(['message' => 'That folder is not allowed for this field.'], 403);
        }

        /*
         * The uploader validates against the folder. The field's
         * own allowed types are narrower and it cannot see them,
         * so they are applied here. An empty list is unrestricted.
         */
        if ($types = array_filter((array)Arr::get($config, 'allowed_types', []))) {

            if (!in_array($this->extension($file), array_map('strtolower', $types), true)) {
                return $this->response->json(
                    ['message' => 'That file type is not allowed for this field.'],
                    422
                );
            }
        }

        try {
            $entry = $uploader->upload($file, $folder);
        } catch (\Exception $e) {
            return $this->response->json(['message' => $e->getMessage()], 422);
        }

        return $this->response->json($entry->getAttributes());
    }

    /**
     * Return the recently uploaded files.
     *
     * @param  UploadTableBuilder $table
     * @param  $key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recent(UploadTableBuilder $table, Authorizer $authorizer, $key)
    {
        $this->authorizeWrite($authorizer);

        return $table
            ->setAllowedFolders(AllowedFolders::ids($this->config($key)))
            ->setUploaded(array_filter(explode(',', $this->request->get('uploaded'))))
            ->render();
    }


    /**
     * Return the extension the uploader will store the file under.
     *
     * Derived through FileSanitizer so this and FileUploader agree
     * on a name like "x.png.pdf".
     *
     * @param  UploadedFile $file
     * @return string
     */
    protected function extension($file)
    {
        return strtolower(
            pathinfo(FileSanitizer::clean($file->getClientOriginalName()), PATHINFO_EXTENSION)
        );
    }

    /**
     * Refuse a caller who may not write files.
     *
     * @param Authorizer $authorizer
     */
    protected function authorizeWrite(Authorizer $authorizer)
    {
        if (!$authorizer->authorize('anomaly.module.files::files.write')) {
            abort(403);
        }
    }

    /**
     * Return the configuration the key stands for.
     *
     * @param  string $key
     * @return array
     */
    protected function config($key)
    {
        if (!$config = ConfigCache::get($key)) {
            abort(404);
        }

        return (array)$config;
    }
}

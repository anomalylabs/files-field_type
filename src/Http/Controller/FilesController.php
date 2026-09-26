<?php namespace Anomaly\FilesFieldType\Http\Controller;

use Illuminate\Support\Arr;
use Anomaly\FilesFieldType\Support\ConfigCache;
use Anomaly\FilesFieldType\Support\AllowedFolders;
use Anomaly\FilesFieldType\Table\FileTableBuilder;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesFieldType\Table\ValueTableBuilder;
use Anomaly\Streams\Platform\Support\Authorizer;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;

/**
 * Class FilesController
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class FilesController extends AdminController
{

    /**
     * Return an index of existing files.
     *
     * @param FileTableBuilder $table
     * @param                  $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(FileTableBuilder $table, $key)
    {
        return $table->setConfig($this->config($key))->render();
    }

    /**
     * Return a list of folders to choose from.
     *
     * Choosing a folder only leads to the uploader, so it
     * takes the same permission the upload itself does.
     *
     * @param FolderRepositoryInterface $folders
     * @param Authorizer                $authorizer
     * @param                           $key
     *
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function choose(FolderRepositoryInterface $folders, Authorizer $authorizer, $key)
    {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        $allowed = [];

        foreach (Arr::get($config, 'folders', []) as $identifier) {

            /* @var FolderInterface $folder */
            if ($folder = dispatch_sync(new GetFolder($identifier))) {
                $allowed[] = $folder;
            }
        }

        if (!$allowed) {
            $allowed = $folders->all();
        }

        return $this->view->make(
            'anomaly.field_type.files::choose',
            [
                'key'     => $key,
                'folders' => $allowed,
            ]
        );
    }

    /**
     * Return a table of selected files.
     *
     * @param ValueTableBuilder $table
     * @param                   $key
     *
     * @return null|string
     */
    public function selected(ValueTableBuilder $table, $key)
    {
        return $table
            ->setAllowedFolders(AllowedFolders::ids($this->config($key)))
            ->setUploaded(array_filter(explode(',', $this->request->get('uploaded'))))
            ->make()
            ->getTableContent();
    }

    /**
     * Check if a file exists.
     *
     * Only the uploader asks this, so it takes the same
     * permission the upload itself does.
     *
     * @param FileRepositoryInterface $files
     * @param Authorizer              $authorizer
     * @param                         $folder
     * @param                         $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(FileRepositoryInterface $files, Authorizer $authorizer, $folder, $key)
    {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        $success = true;
        $exists = false;

        /* @var FolderInterface|null $folder */
        $folder = dispatch_sync(new GetFolder($folder));

        if (!$folder || !AllowedFolders::permits($config, $folder->getId())) {
            abort(404);
        }

        if ($file = $files->findByNameAndFolder($this->request->get('file'), $folder)) {
            $exists = true;
        }

        return $this->response->json(compact('success', 'exists'));
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
     * An unresolvable key means the caller was never handed
     * this configuration, so nothing is served for it.
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

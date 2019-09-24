<?php namespace Anomaly\FilesFieldType\Http\Controller;

use Anomaly\FilesFieldType\Table\FileTableBuilder;
use Anomaly\FilesFieldType\Table\UploadTableBuilder;
use Anomaly\FilesModule\File\FileUploader;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

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
     * @return \Illuminate\View\View
     */
    public function index(UploadTableBuilder $table, $folder)
    {
        /* @var FolderInterface $folder */
        $folder = dispatch_now(new GetFolder($folder));

        $config = cache('files-field_type::' . request('key'), []);

        $allowed = array_intersect(array_get($config, 'allowed_types', []), $folder->getAllowedTypes());

        return view(
            'anomaly.field_type.files::upload/index',
            [
                'allowed' => $allowed ?: $folder->getAllowedTypes(),
                'table'   => $table->make()->getTable(),
                'folder'  => $folder,
                'config'  => $config,
            ]
        );
    }

    /**
     * Upload a file.
     *
     * @param  FileUploader $uploader
     * @param  FolderRepositoryInterface $folders
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(FileUploader $uploader, FolderRepositoryInterface $folders)
    {
        if ($file = $uploader->upload($this->request->file('upload'), $folders->find(request('folder')))) {
            return $this->response->json($file->getAttributes());
        }

        return $this->response->json(['message' => 'There was a problem uploading the file.'], 500);
    }

    /**
     * Return the recently uploaded files.
     *
     * @param  FileTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recent(UploadTableBuilder $table)
    {
        return $table->setUploaded(array_filter(explode(',', request('uploaded'))))->render();
    }
}

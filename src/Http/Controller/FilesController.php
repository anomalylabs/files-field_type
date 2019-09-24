<?php namespace Anomaly\FilesFieldType\Http\Controller;

use Anomaly\FilesFieldType\Table\FileTableBuilder;
use Anomaly\FilesFieldType\Table\ValueTableBuilder;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

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
     * @param  FileTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(FileTableBuilder $table)
    {
        return $table->setConfig(cache('files-field_type::' . request()->route('key'), []))->render();
    }

    /**
     * Return a list of folders to choose from.
     *
     * @param  FolderRepositoryInterface $folders
     * @return \Illuminate\View\View
     */
    public function choose(FolderRepositoryInterface $folders)
    {
        $allowed = [];

        $config = cache('files-field_type::' . ($key = request()->route('key')), []);

        foreach (array_get($config, 'folders', []) as $identifier) {

            /* @var FolderInterface $folder */
            if ($folder = dispatch_now(new GetFolder($identifier))) {
                $allowed[] = $folder;
            }
        }

        if (!$allowed) {
            $allowed = $folders->all();
        }

        return view(
            'anomaly.field_type.files::choose',
            [
                'key'     => $key,
                'folders' => $allowed,
            ]
        );
    }

    /**
     * Return a table of select items.
     *
     * @param  ValueTableBuilder $table
     * @return null|string
     */
    public function selected(ValueTableBuilder $table)
    {
        return $table->setUploaded(explode(',', request('uploaded')))->make()->getTableContent();
    }

    /**
     * Check if a file exists.
     *
     * @param FileRepositoryInterface $files
     * @param                         $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(FileRepositoryInterface $files, $folder)
    {
        $success = true;
        $exists  = false;

        /* @var FolderInterface|null $folder */
        $folder = dispatch_now(new GetFolder($folder));

        if ($folder && $file = $files->findByNameAndFolder(request('file'), $folder)) {
            $exists = true;
        }

        return $this->response->json(compact('success', 'exists'));
    }
}

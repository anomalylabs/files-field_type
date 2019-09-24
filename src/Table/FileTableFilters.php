<?php namespace Anomaly\FilesFieldType\Table;

use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;

/**
 * Class FileTableFilters
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class FileTableFilters
{


    /**
     * @param FileTableBuilder $builder
     * @param FolderRepositoryInterface $folders
     * @param Repository $cache
     * @param Request $request
     */
    public function handle(
        FileTableBuilder $builder,
        FolderRepositoryInterface $folders,
        Repository $cache,
        Request $request
    ) {
        $allowed = [];

        $config = $cache->get('files-field_type::' . $request->route('key'), []);

        foreach (array_get($config, 'folders', []) as $identifier) {

            /* @var FolderInterface $folder */
            if ($folder = dispatch_now(new GetFolder($identifier))) {
                $allowed[$folder->getId()] = $folder->getName();
            }
        }

        if (!$allowed) {
            $allowed = $folders->all()->pluck('name', 'id')->all();
        }

        $builder
            ->setFilters(
                [
                    'search' => [
                        'columns' => [
                            'name',
                            'keywords',
                            'mime_type',
                        ],
                    ],
                    'folder' => [
                        'exact'   => true,
                        'filter'  => 'select',
                        'options' => $allowed,
                        'enabled' => (count($allowed) !== 1),
                    ],
                ]
            );
    }
}

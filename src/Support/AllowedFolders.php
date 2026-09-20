<?php namespace Anomaly\FilesFieldType\Support;

use Anomaly\FilesModule\Folder\Command\GetFolder;
use Illuminate\Support\Arr;

/**
 * Class AllowedFolders
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class AllowedFolders
{

    /**
     * Return the folder IDs a field's configuration permits.
     *
     * Configured folders may be IDs or slugs, so slugs are
     * resolved. An empty result means the field names no
     * folders and is therefore unrestricted.
     *
     * @param  array $config
     * @return array
     */
    public static function ids(array $config)
    {
        return array_values(
            array_filter(
                array_map(
                    function ($folder) {

                        if (is_numeric($folder)) {
                            return (int)$folder;
                        }

                        if (is_string($folder) && $folder = dispatch_sync(new GetFolder($folder))) {
                            return $folder->getId();
                        }

                        return null;
                    },
                    (array)Arr::get($config, 'folders', [])
                )
            )
        );
    }

    /**
     * Return whether a folder ID is permitted by the configuration.
     *
     * @param  array $config
     * @param  mixed $folder
     * @return bool
     */
    public static function permits(array $config, $folder)
    {
        if (!$allowed = self::ids($config)) {
            return true;
        }

        return in_array((int)$folder, $allowed, true);
    }
}

<?php namespace Anomaly\FilesFieldType\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Class ConfigCache
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ConfigCache
{

    /**
     * Cache a field configuration for the picker and return its key.
     *
     * The key is an HMAC of the configuration under the application
     * key, so identical configurations share an entry and a key can
     * only be obtained from a rendered field.
     *
     * @param  array $config
     * @return string
     */
    public static function put(array $config)
    {
        $key = hash_hmac('sha256', json_encode($config, JSON_PARTIAL_OUTPUT_ON_ERROR), config('app.key'));

        Cache::put(self::name($key), $config, 60 * 60 * 24);

        return $key;
    }

    /**
     * Return the configuration a key stands for.
     *
     * @param  string $key
     * @return array|null
     */
    public static function get($key)
    {
        return Cache::get(self::name($key));
    }

    /**
     * Return the cache entry name for a key.
     *
     * @param  string $key
     * @return string
     */
    protected static function name($key)
    {
        return 'anomaly.field_type.files::config.' . $key;
    }
}

<?php namespace Anomaly\FilesFieldType;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class FilesFieldTypeServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class FilesFieldTypeServiceProvider extends AddonServiceProvider
{

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'admin/files-field_type/index/{key}'           => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@index',
        ],
        'admin/files-field_type/choose/{key}'          => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@choose',
        ],
        'admin/files-field_type/selected/{key}'        => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@selected',
        ],
        'admin/files-field_type/exists/{folder}/{key}' => [
            'verb' => 'post',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@exists',
        ],
        'admin/files-field_type/upload/{folder}/{key}' => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@index',
        ],
        'admin/files-field_type/handle/{key}'          => [
            'verb' => 'post',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@upload',
        ],
        'admin/files-field_type/recent/{key}'          => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@recent',
        ],
    ];
}

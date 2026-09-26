<?php namespace Anomaly\FilesFieldType;

use Anomaly\FilesFieldType\Listener\GuardFieldSlug;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Ui\Form\Event\FormWasBuilt;

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
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/choose/{key}'          => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@choose',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/selected/{key}'        => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@selected',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/exists/{folder}/{key}' => [
            'verb' => 'post',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\FilesController@exists',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/upload/{folder}/{key}' => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@index',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/handle/{key}'          => [
            'verb' => 'post',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@upload',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/files-field_type/recent/{key}'          => [
            'verb' => 'get',
            'uses' => 'Anomaly\FilesFieldType\Http\Controller\UploadController@recent',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
    ];

    /**
     * The addon event listeners.
     *
     * @var array
     */
    protected $listeners = [
        FormWasBuilt::class => [
            GuardFieldSlug::class,
        ],
    ];
}

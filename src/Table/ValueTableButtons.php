<?php namespace Anomaly\FilesFieldType\Table;

/**
 * Class ValueTableButtons
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class ValueTableButtons
{
    /**
     * @param ValueTableBuilder $builder
     */
    public function handle(ValueTableBuilder $builder)
    {
        $builder->setButtons([
            'edit'   => [
                'href' => 'admin/files/edit/{entry.id}',
            ],
            'view' => [
                'href' => function (FileInterface $entry) {
                    return $entry->getPresenter()->viewPath();
                }
            ],
            'download' => [
                'type' => 'primary',
                'icon' => 'fa fa-download',
                'text' => 'streams::button.download',
                'href' => function (FileInterface $entry) {
                    return $entry->getPresenter()->downloadPath();
                }
            ],
            'remove' => [
                'data-dismiss' => 'file',
                'data-file'    => 'entry.id',
            ],
        ]);
    }
}

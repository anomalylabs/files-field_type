<?php namespace Anomaly\FilesFieldType\Support\Config;

use Anomaly\CheckboxesFieldType\CheckboxesFieldType;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;

class FoldersOptions
{

    /**
     * Handle folders
     *
     * @param      CheckboxesFieldType        $fieldType  The field type
     * @param      FolderRepositoryInterface  $folders    The folders
     */
    public function handle(
        CheckboxesFieldType $fieldType,
        FolderRepositoryInterface $folders
    )
    {
        $fieldType->setOptions(
            $folders->all()->pluck('name', 'id')->toArray()
        );
    }
}

<?php namespace Anomaly\FilesFieldType\Listener;

use Anomaly\FilesFieldType\FilesFieldType;
use Anomaly\FilesFieldType\Validation\ValidateSlug;
use Anomaly\Streams\Platform\Field\Form\FieldFormBuilder;
use Anomaly\Streams\Platform\Ui\Form\Event\FormWasBuilt;

/**
 * Class GuardFieldSlug
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GuardFieldSlug
{

    /**
     * Add the slug rule to the field form.
     *
     * @param FormWasBuilt $event
     */
    public function handle(FormWasBuilt $event)
    {
        $builder = $event->getBuilder();

        if (!$builder instanceof FieldFormBuilder) {
            return;
        }

        if (!$this->isFiles($builder)) {
            return;
        }

        if (!$slug = $builder->getFormField('slug')) {
            return;
        }

        $slug->mergeRules(['valid_files_slug']);

        $slug->mergeValidators(
            [
                'valid_files_slug' => [
                    'handler' => ValidateSlug::class,
                    'message' => 'anomaly.field_type.files::validation.slug_collision',
                ],
            ]
        );
    }

    /**
     * Return whether the form is creating or editing a files field.
     *
     * @param  FieldFormBuilder $builder
     * @return bool
     */
    protected function isFiles(FieldFormBuilder $builder)
    {
        if ($builder->getFieldType() instanceof FilesFieldType) {
            return true;
        }

        $entry = $builder->getFormEntry();

        return $entry && $entry->type === 'anomaly.field_type.files';
    }
}

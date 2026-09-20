<?php namespace Anomaly\FilesFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeSchema;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class FilesFieldTypeSchema
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class FilesFieldTypeSchema extends FieldTypeSchema
{

    /**
     * The pivot table's own columns.
     *
     * @var array
     */
    protected $signature = [
        'entry_id',
        'file_id',
        'sort_order',
    ];

    /**
     * Add the field type's pivot table.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function addColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $table = $table->getTable() . '_' . $this->fieldType->getField();

        $this->guard($table);

        $this->schema->dropIfExists($table);

        $this->schema->create(
            $table,
            function (Blueprint $table) {

                $table->increments('id');
                $table->integer('entry_id');
                $table->integer('file_id');
                $table->integer('sort_order')->nullable();

                $table->unique(
                    ['entry_id', 'file_id'],
                    md5($table->getTable() . '_' . $this->fieldType->getField() . '-unique-files')
                );
            }
        );
    }

    /**
     * Rename the pivot table.
     *
     * @param Blueprint $table
     * @param FieldType $from
     */
    public function renameColumn(Blueprint $table, FieldType $from)
    {
        $to = $table->getTable() . '_' . $this->fieldType->getField();

        $this->guard($to);

        $this->schema->rename($table->getTable() . '_' . $from->getField(), $to);
    }

    /**
     * Drop the pivot table.
     *
     * @param Blueprint $table
     */
    public function dropColumn(Blueprint $table)
    {
        $table = $table->getTable() . '_' . $this->fieldType->getField();

        $this->guard($table);

        $this->schema->dropIfExists($table);
    }

    /**
     * Refuse to touch a table that is not a files pivot.
     *
     * @param string $table
     * @throws \RuntimeException
     */
    protected function guard($table)
    {
        if (!$this->schema->hasTable($table)) {
            return;
        }

        if ($this->schema->hasColumns($table, $this->signature)) {
            return;
        }

        throw new \RuntimeException(
            "The table [{$table}] already exists and is not a files pivot table. "
            . "Rename the [{$this->fieldType->getField()}] field to avoid the collision."
        );
    }
}

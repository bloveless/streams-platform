<?php namespace Anomaly\Streams\Platform\Ui\Table\Component\Header\Guesser;

use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

/**
 * Class HeadingsGuesser
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Table\Component\Button\Guesser
 */
class HeadingsGuesser
{

    /**
     * Guess the field for a column.
     *
     * @param TableBuilder $builder
     */
    public function guess(TableBuilder $builder)
    {
        $columns = [];

        $stream = $builder->getTableStream();

        foreach ($builder->getColumns() as $column) {

            /**
             * If the heading is false then the intent was
             * to not have heading text at all.
             */
            if ($column['heading'] === false) {

                $columns[] = $column;

                continue;
            }

            /**
             * No stream means we can't
             * really do much here.
             */
            if (!$stream instanceof StreamInterface) {

                $columns[] = $column;

                continue;
            }

            /**
             * If the heading matches a field
             * with dot format then reduce it.
             */
            if (preg_match("/^entry.([a-zA-Z\\_]+)./", $column['heading'], $match)) {
                $column['heading'] = $match[1];
            }

            $field = $stream->getField($column['heading']);

            /**
             * No field means we still do not have
             * anything to do here.
             */
            if (!$field instanceof FieldInterface) {

                $columns[] = $column;

                continue;
            }

            /**
             * Use the name from the field.
             */
            if ($name = $field->getName()) {
                $column['heading'] = $name;
            }

            $columns[] = $column;
        }

        $builder->setColumns($columns);
    }
}
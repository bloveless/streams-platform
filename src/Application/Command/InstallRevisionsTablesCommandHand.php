<?php namespace Anomaly\Streams\Platform\Application\Command;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class InstallRevisionsTablesCommandHandler
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Application\Command
 */
class InstallRevisionsTablesCommandHandler
{

    /**
     * The database object.
     *
     * @var mixed
     */
    protected $db;

    /**
     * The schema builder.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new InstallRevisionsTablesCommandHandler instance.
     */
    public function __construct()
    {
        $this->db     = app('db');
        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Handle the command.
     */
    public function handle()
    {
        $this->createApplicationsTable();
    }

    /**
     * Create the revisions table.
     */
    protected function createApplicationsTable()
    {
        $this->schema->dropIfExists('revisions');

        $this->schema->create(
            'revisions',
            function (Blueprint $table) {

                $table->increments('id');
                $table->string('revisionable_type');
                $table->integer('revisionable_id');
                $table->integer('user_id')->nullable();
                $table->string('key');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->timestamps();

                $table->index(['revisionable_id', 'revisionable_type']);
            }
        );
    }
}
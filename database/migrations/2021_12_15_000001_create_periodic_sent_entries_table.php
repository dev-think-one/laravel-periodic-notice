<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodicSentEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('periodic-notice.tables.periodic_sent_entries'), function (Blueprint $table) {
            \PeriodicNotice\Helpers\MigrationHelper::defaultColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('periodic-notice.tables.periodic_sent_entries'));
    }
}

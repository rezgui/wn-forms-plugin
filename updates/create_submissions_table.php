<?php

/** @noinspection AutoloadingIssuesInspection */

namespace Gromit\Forms\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('gromit_forms_submissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('form_id');
            $table->foreign('form_id')
                ->references('id')
                ->on('gromit_forms_forms')
                ->onDelete('cascade');

            $table->text('data');
            $table->text('request_data')->nullable();
            $table->text('user_data')->nullable();

            $table->dateTime('viewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gromit_forms_submissions');
    }
}

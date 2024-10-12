<?php

namespace Gromit\Forms\Updates;

use Gromit\Forms\Models\Form;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

class UpdateButtonsConfig extends Migration
{
    public function up()
    {
        Schema::table('gromit_forms_forms', function (Blueprint $table) {
            $table->text('buttons_config')->nullable(true)->change();
        });
    }

    public function down()
    {

    }
}

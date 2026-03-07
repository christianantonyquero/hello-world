<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNavigationHelperFieldsToStorePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_pages', function (Blueprint $table) {
            $table->boolean('hide_from_menu')->default(false);
            $table->unsignedInteger('menu_display_order')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_pages', function (Blueprint $table) {
            $table->dropColumn('menu_display_order');
            $table->dropColumn('hide_from_menu');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreBgImageFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('intro_video')->nullable();
            $table->string('cart_bg_image')->nullable();
            $table->string('photo_booth_bg_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('intro_video');
            $table->dropColumn('cart_bg_image');
            $table->dropColumn('photo_booth_bg_image');
        });
    }
}

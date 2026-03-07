<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorePageHotspotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_page_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_page_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('value');
            $table->decimal('x', 8, 2);
            $table->decimal('y', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_page_hotspots');
    }
}

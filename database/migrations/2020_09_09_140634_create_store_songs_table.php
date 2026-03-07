<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_songs', function (Blueprint $table) {
            $table->id();
            $table->string('track_name');
            $table->string('track_artwork')->nullable();
            $table->string('track_audio_file');  // link to MP3 file
            $table->string('artist_name')->nullable();
            $table->string('album_name')->nullable();
            $table->string('track_link')->nullable(); // optional off site link to "purchase the track"
            $table->unsignedInteger('play_order')->default(10); // possible use for the playlist "playing order"

            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('track_name');
            $table->index('play_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_songs');
    }
}

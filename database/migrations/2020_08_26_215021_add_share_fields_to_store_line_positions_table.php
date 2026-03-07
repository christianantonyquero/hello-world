<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShareFieldsToStoreLinePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_line_positions', function (Blueprint $table) {
            $table->string('share_code');
            $table->foreignId('referred_by_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_line_positions', function (Blueprint $table) {
            $table->dropColumn(['share_code', 'referred_by_id']);
        });
    }
}

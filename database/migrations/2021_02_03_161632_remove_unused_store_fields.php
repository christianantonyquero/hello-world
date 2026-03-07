<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedStoreFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('tax_exempt');
            $table->dropColumn('state_tax_rate');
            $table->dropColumn('city_tax_rate');
            $table->dropColumn('county_tax_rate');
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
            $table->boolean('tax_exempt')->default(false);
            $table->decimal('state_tax_rate', 8, 6)->default(0.0);
            $table->decimal('city_tax_rate', 8, 6)->default(0.0);
            $table->decimal('county_tax_rate', 8, 6)->default(0.0);
        });
    }
}

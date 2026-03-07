<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('stripe_charge_id')->nullable();
            $table->string('status')->default('unpaid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedDecimal('subtotal', 8, 2);
            $table->unsignedDecimal('taxes', 8, 2);
            $table->unsignedDecimal('total', 8, 2);
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
        Schema::dropIfExists('orders');
    }
}

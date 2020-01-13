<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('lbaig_basket_orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->enum('status', ['open', 'filled', 'shipped'])->default('open');
            $table->integer('address_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('payment_id')->unsigned()->nullable();
            $table->string('email');
            $table->string('phone');
            $table->decimal('subtotal')->default(0);
            $table->decimal('shipping')->default(0);
            $table->decimal('tax')->default(0);
            $table->decimal('total')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lbaig_basket_orders');
    }
}

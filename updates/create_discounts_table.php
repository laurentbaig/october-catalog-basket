<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('lbaig_basket_discounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_fixed')->default(false);
            $table->decimal('amount', 10, 2)->default(0);
            $table->float('percent')->default(0);
            $table->datetime('since')->default('1970-01-01 00:00:00');
            $table->datetime('until')->default('2999-12-31 23:59:59');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lbaig_basket_discounts');
    }
}

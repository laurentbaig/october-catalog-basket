<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateBasketItemsTable extends Migration
{
    public function up()
    {
        Schema::create('lbaig_basket_basket_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('basket_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('basket_id')
                  ->references('id')
                  ->on('lbaig_basket_baskets');
            $table->foreign('product_id')
                  ->references('id')
                  ->on('lbaig_catalog_products');
        });

        Schema::create('basket_item_property_option', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('basket_item_id')->unsigned();
            $table->integer('property_option_id')->unsigned();
            $table->timestamps();

            $table->foreign('basket_item_id')
                  ->references('id')
                  ->on('lbaig_basket_basket_items');
            $table->foreign('property_option_id')
                  ->references('id')
                  ->on('lbaig_catalog_property_options');
        });
    }

    public function down()
    {
        Schema::dropIfExists('basket_item_property_option');
        Schema::dropIfExists('lbaig_basket_basket_items');
    }
}

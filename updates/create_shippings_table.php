<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateShippingsTable extends Migration
{
    public function up()
    {
        Schema::create('lbaig_basket_shippings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('active');
            $table->string('name');
            $table->string('code');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lbaig_basket_shippings');
    }
}

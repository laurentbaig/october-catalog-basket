<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateBasketItemsAddLinePrice extends Migration
{
    public function up()
    {
        Schema::table('lbaig_basket_basket_items', function (Blueprint $table) {
            $table->decimal('line_price')->default(0)->after('product_id');
        });
    }

    public function down()
    {
        Schema::table('lbaig_basket_basket_items', function (Blueprint $table) {
            $table->dropColumn('line_price');
        });
    }
}

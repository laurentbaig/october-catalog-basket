<?php namespace Lbaig\Basket\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Lbaig\Basket\Models\Country;
use Lbaig\Basket\Models\State;

function countries() {
    $data = [
        [
            'code' => 'US',
            'name' => 'United States',
            'shipping' => 0, 
            'states' => ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District Of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming']
        ],
        [
            'code' => 'CA',
            'name' => 'Canada',
            'shipping' => 10, 
            'states' => ['Alberta', 'British Columbia', 'Manitoba', 'New Brunswick', 'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia', 'Nunavut', 'Ontario', 'Prince Edward Island', 'Quebec', 'Saskatchewan', 'Yukon']
        ]
    ];

    return $data;
}

class CreateStatesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('lbaig_basket_states');
        Schema::create('lbaig_basket_states', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('name');
            $table->decimal('shipping', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('country_id')
                  ->references('id')
                  ->on('lbaig_basket_countries')
                  ->onDelete('cascade');
        });

        foreach (countries() as $data) {
            $country = Country::create([
                'is_active' => 1,
                'code' => $data['code'],
                'name' => $data['name'],
                'shipping' => $data['shipping']
            ]);

            foreach ($data['states'] as $state) {
                State::create([
                    'country_id' => $country->id,
                    'name' => $state,
                    'shipping' => 0
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('lbaig_basket_states');
    }
}

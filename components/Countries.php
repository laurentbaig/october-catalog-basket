<?php namespace Lbaig\Basket\Components;

use Cms\Classes\ComponentBase;
use Lbaig\Basket\Models\Country;

class Countries extends ComponentBase
{
    public $countries = [];
    public $countryId;
    
    public function componentDetails()
    {
        return [
            'name'        => 'Countries',
            'description' => 'Provides list of countries and shipping surcharges'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->countries =  Country::active()->get();
        $this->countryId = $this->countries->first()->id;
    }

    public function states() {
        $country = Country::find($this->countryId);

        return $country->states;
    }

    public function onCountrySelect() {
        \Log::info(request());

        $this->countryId = request('country_id');
    }
}

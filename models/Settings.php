<?php namespace Lbaig\Basket\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'lbaig_basket_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}

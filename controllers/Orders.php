<?php namespace Lbaig\Basket\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Lbaig\Basket\Models\Settings;

/**
 * Orders Back-end Controller
 */
class Orders extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        $this->vars['thumbsize'] = Settings::instance()->get('order_thumb_size');

        BackendMenu::setContext('Lbaig.Basket', 'basket', 'orders');
    }
}

<?php namespace Lbaig\Basket;

use Backend;
use Carbon\Carbon;
use Event;
use Lbaig\Catalog\Models\Product;
use Rainlab\User\Models\User;
use System\Classes\PluginBase;

/**
 * Basket Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Basket',
            'description' => 'No description provided yet...',
            'author'      => 'Lbaig',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // add orders to user
        User::extend(function ($model) {
            $model->hasMany['orders'] = 'Lbaig\Basket\Models\Order';
        });
        
        // add the basket item relation to produts
        Product::extend(function ($model) {
            $model->hasMany['basketItems'] = 'Lbaig\Basket\Models\BasketItem';
            $model->hasMany['discounts'] = 'Lbaig\Basket\Models\Discount';
            $model->addDynamicMethod('currentDiscounts', function () use ($model) {
                $now = Carbon::now();
                return Models\Discount::active()
                    ->where('product_id', $model->id)
                    ->where('since', '<=', $now)
                    ->where('until', '>', $now)
                    ->get();
            });
            $model->addDynamicMethod('categoryDiscounts', function () use ($model) {
                $now = Carbon::now();
                $mcat = $model->category;
                return Models\Discount::active()
                    ->where('discount_type', 'category')
                    ->where('since', '<=', $now)
                    ->where('until', '>', $now)
                    ->whereHas('category', function ($query) use ($mcat) {
                        $query->where('nest_left', '>=', $mcat->nest_left)
                              ->where('nest_right', '<=', $mcat->nest_right);
                    })
                    ->get();
            });
        });

        // extend product list to include # of orders it has
        Event::listen('backend.list.extendColumns', function ($widget) {
            if (! $widget->model instanceof Product) {
                return;
            }

            $widget->addColumns([
                'orders_count' => [
                    'label' => 'Orders (Baskets)',
                    'type' => 'partial',
                    'path' => '~/plugins/lbaig/basket/partials/_orders_count.htm',
                    'align' => 'right'
                ],
            ]);
        });

        Event::listen('backend.menu.extendItems', function($manager) {

            $manager->addSideMenuItems('Lbaig.Catalog', 'catalog', [
                'discounts' => [
                    'label' => 'Discounts',
                    'icon' => 'icon-book',
                    'url' => Backend::url('lbaig/basket/discounts'),
                    'permissions' => ['lbaig.catalog.some_permission']
                ]
            ]);

            $manager->addSideMenuItems('Lbaig.Catalog', 'catalog', [
                'countries' => [
                    'label' => 'Countries',
                    'icon' => 'icon-globe',
                    'url' => Backend::url('lbaig/basket/countries'),
                    'permissions' => ['lbaig.catalog.some_permission']
                ]
            ]);

        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lbaig\Basket\Components\Basket' => 'Basket',
            'Lbaig\Basket\Components\Countries' => 'Countries',
            'Lbaig\Basket\Components\Order' => 'Order',
            'Lbaig\Basket\Components\OrderHistory' => 'OrderHistory',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'lbaig.basket.access_settings' => [
                'tab' => 'Basket',
                'label' => 'Change settings'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'basket' => [
                'label'       => 'Orders',
                'url'         => Backend::url('lbaig/basket/orders'),
                'icon'        => 'icon-leaf',
                'permissions' => ['lbaig.basket.*'],
                'order'       => 501,
            ],
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'Lbaig\Basket\ReportWidgets\OrdersSummary' => [
                'label'   => 'Orders Summary',
                'context' => 'dashboard',
                'permissions' => [
                    'lbaig.basket.*',
                ],
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'basket' => [
                'label'       => 'Basket Settings',
                'description' => 'Manage basket settings.',
                'category'    => 'Basket',
                'icon'        => 'icon-cog',
                'class'       => 'Lbaig\Basket\Models\Settings',
                'order'       => 500,
                'keywords'    => 'security location',
                'permissions' => ['lbaig.basket.access_settings']
            ]
        ];
    }
}

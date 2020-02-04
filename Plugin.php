<?php namespace Lbaig\Basket;

use Backend;
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
        /*
        Event::listen('backend.menu.extendItems', function($manager)
        {
            $manager->addSideMenuItems('Lbaig.Catalog', 'catalog', [
                'comments' => [
                    'label'       => 'Comment',
                    'icon'        => 'icon-comments',
                    'code'        => 'comments',
                    'owner'       => 'RainLab.Blog',
                    'url'         => Backend::url('lbaig/basket/section')
                ],
            ]);
        });
        */
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
            'Lbaig\Basket\Components\Order' => 'Order',
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
            'lbaig.basket.some_permission' => [
                'tab' => 'Basket',
                'label' => 'Some permission'
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
}

<?php

namespace App\Main;

class SideMenu
{
    public static function menu(): array
    {
        $isAdmin = auth()->check() && auth()->user()->hasAnyRole(['Admin', 'super-admin']);

        $menu = [];

        if ($isAdmin) {
            $menu['pos_dashboard'] = [
                'icon' => 'home',
                'route_name' => 'admin.dashboard',
                'title' => 'Dashboard'
            ];
            $menu['productos'] = [
                'icon' => 'shopping-bag',
                'route_name' => 'admin.products',
                'title' => 'Productos'
            ];
            $menu['admin_categories'] = [
                'icon' => 'tag',
                'route_name' => 'admin.categories',
                'title' => 'Categorías'
            ];
            $menu['admin_brands'] = [
                'icon' => 'award',
                'route_name' => 'admin.brands',
                'title' => 'Marcas'
            ];
            $menu['divider_pos'] = 'divider';
        }

        $menu['caja_simple'] = [
            'icon' => 'credit-card',
            'route_name' => 'pos.simple-cashier',
            'title' => 'Caja'
        ];
        $menu['dashboard_simple'] = [
            'icon' => 'pie-chart',
            'route_name' => 'pos.simple-dashboard',
            'title' => 'Reportes'
        ];

        if ($isAdmin) {
            $menu['divider_admin'] = 'divider';
            $menu['usuarios'] = [
                'icon' => 'users',
                'title' => 'Usuarios',
                'sub_menu' => [
                    'users.create' => [
                        'icon' => 'user-plus',
                        'route_name' => 'users.create',
                        'title' => 'Crear usuario'
                    ],
                    'users.index' => [
                        'icon' => 'list',
                        'route_name' => 'users.index',
                        'title' => 'Lista de usuarios'
                    ],
                ]
            ];
        }

        return $menu;
    }
}

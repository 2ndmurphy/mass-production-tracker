<?php

namespace App\Helpers;

class MenuHelper
{
  public static function getMenuByRole($role, $department)
  {
    $menus = [];

    switch (strtolower($role)) {
      case 'staff':
        if ($department === 'warehouse') {
          $menus = [
            ['name' => 'Stocks', 'icon' => 'squares-2x2', 'route' => 'warehouse.stock.index'],
            ['name' => 'Inventory', 'icon' => 'boxes', 'route' => 'warehouse.inventory.index'],
            ['name' => 'Batch Management', 'icon' => 'arrow-down-circle', 'route' => 'warehouse.batches.index'],
            // ['name' => 'Movement Logs', 'icon' => 'boxes', 'route' => 'warehouse.movements.index'],
          ];
        } elseif ($department === 'production') {
          $menus = [
            [
              'name' => 'Production Orders',
              'route' => 'production.index',
              'icon' => 'squares-2x2',
            ],
            [
              'name' => 'Create Batch',
              'route' => 'production.create',
              'icon' => 'plus',
            ],
          ];
        } elseif ($department === 'qc') {
          $menus = [
            ['name' => 'Dashboard', 'icon' => 'squares-2x2', 'route' => 'qc.index'],
            // ['name' => 'Reviews', 'icon' => 'alert-triangle', 'route' => 'qc.review.show'],
            ['name' => 'History logs', 'icon' => 'alert-triangle', 'route' => 'qc.logs'],
          ];
        }
        break;

      case 'manager':
        $menus = [
          ['name' => 'Dashboard', 'icon' => 'squares-2x2', 'route' => 'dashboard'],
          ['name' => 'Production Plans', 'icon' => 'calendar-days', 'route' => 'plans.index'],
          ['name' => 'Reports', 'icon' => 'chart-bar', 'route' => 'reports.index'],
          ['name' => 'Team Overview', 'icon' => 'users', 'route' => 'teams.index'],
        ];
        break;

      case 'admin':
        $menus = [
          ['name' => 'Dashboard', 'icon' => 'squares-2x2', 'route' => 'dashboard'],
          ['name' => 'Users', 'icon' => 'users-2', 'route' => 'users.index'],
          ['name' => 'Departments', 'icon' => 'building', 'route' => 'departments.index'],
          ['name' => 'Audit Logs', 'icon' => 'scroll-text', 'route' => 'audit.index'],
        ];
        break;
    }

    return $menus;
  }
}

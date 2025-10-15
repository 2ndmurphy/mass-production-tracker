<?php

namespace App\Helpers;

use App\Models\User;

class RedirectHelper
{
    public static function redirectToDashboard(User $user)
    {
        $role = strtolower($user->role->name);
        $dept = strtolower(optional($user->department)->name);

        switch ($role) {
            case 'admin':
                return route('dashboard.admin');
            case 'manager':
                return route('manager.dashboard');
            case 'staff':
                if ($dept === 'production') {
                    return route('production.index');
                } elseif ($dept === 'qc') {
                    return route('qc.index');
                } elseif ($dept === 'warehouse') {
                    return route('warehouse.stock.index');
                } else {
                    return abort(403);
                }
            default:
                return redirect('/login');
        }
    }
}

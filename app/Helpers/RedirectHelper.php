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
                return redirect()->route('dashboard.admin');
            case 'manager':
                return redirect()->route('dashboard.manager');
            case 'staff':
                if ($dept === 'production') {
                    return redirect()->route('production.index');
                } elseif ($dept === 'qc') {
                    return redirect()->route('qc.index');
                } elseif ($dept === 'warehouse') {
                    return redirect()->route('warehouse.stock.index');
                } else {
                    return abort(403);
                }
            default:
                return redirect('/login');
        }
    }
}

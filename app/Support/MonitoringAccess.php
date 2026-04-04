<?php

namespace App\Support;

use App\Models\User;

final class MonitoringAccess
{
    /**
     * Проверяет, может ли пользователь просматривать служебные dashboard-страницы.
     */
    public static function canViewDashboard(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $currentTeamId = getPermissionsTeamId();
        setPermissionsTeamId($user->id);

        try {
            return $user->hasRole('root');
        } finally {
            setPermissionsTeamId($currentTeamId);
        }
    }
}

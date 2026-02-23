<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAccountLoginAndAcceptOffer
{
    /**
     * Сохраняет факт акцепта оферты и логирует вход в личный кабинет.
     */
    public function handle(Request $request, callable $next): mixed
    {
        $user = $request->user();

        if ($user instanceof User && $request->boolean('offer_accepted')) {
            if ($user->offer_accepted_at === null) {
                $user->offer_accepted_at = now();
                $user->save();
            }
        }

        if ($user instanceof User) {
            Log::info('Account login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $next($request);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $table = 'user_sessions';
    
    protected $fillable = [
        'user_id', 'session_id', 'ip_address', 'user_agent',
        'device_type', 'browser', 'platform', 'location',
        'last_activity', 'is_current', 'logged_in_at', 'logged_out_at'
    ];

    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
            'logged_in_at' => 'datetime',
            'logged_out_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function recordLogin(User $user): self
    {
        $session = self::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => self::detectDeviceType(request()->userAgent()),
            'browser' => self::detectBrowser(request()->userAgent()),
            'platform' => self::detectPlatform(request()->userAgent()),
            'location' => self::getLocation(request()->ip()),
            'last_activity' => now(),
            'is_current' => true,
            'logged_in_at' => now(),
        ]);

        self::where('user_id', $user->id)
            ->where('id', '!=', $session->id)
            ->update(['is_current' => false]);

        return $session;
    }

    public static function recordLogout(User $user): void
    {
        self::where('user_id', $user->id)
            ->where('is_current', true)
            ->update([
                'logged_out_at' => now(),
                'is_current' => false,
            ]);
    }

    public static function updateActivity(string $sessionId): void
    {
        self::where('session_id', $sessionId)->update(['last_activity' => now()]);
    }

    public static function getActiveSessions(User $user): array
    {
        return self::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->orderBy('last_activity', 'desc')
            ->get()
            ->toArray();
    }

    public static function terminateSession(User $user, int $sessionId): bool
    {
        $session = self::where('id', $sessionId)->where('user_id', $user->id)->first();
        
        if ($session) {
            $session->update(['logged_out_at' => now(), 'is_current' => false]);
            return true;
        }
        
        return false;
    }

    public static function terminateOtherSessions(User $user): int
    {
        return self::where('user_id', $user->id)
            ->where('is_current', false)
            ->whereNull('logged_out_at')
            ->update(['logged_out_at' => now()]);
    }

    private static function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            return 'mobile';
        }
        return 'desktop';
    }

    private static function detectBrowser(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/safari/i', $userAgent)) return 'Safari';
        if (preg_match('/edge/i', $userAgent)) return 'Edge';
        if (preg_match('/opera/i', $userAgent)) return 'Opera';
        return 'Unknown';
    }

    private static function detectPlatform(string $userAgent): string
    {
        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/mac/i', $userAgent)) return 'macOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/ios|iphone|ipad/i', $userAgent)) return 'iOS';
        return 'Unknown';
    }

    private static function getLocation(string $ip): ?string
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return 'Localhost';
        }
        return null;
    }
}

<?php

namespace App\Models;

use App\Services\ServerService;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Server|null $server
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function server(): HasOne
    {
        return $this->hasOne(Server::class, 'user_id', 'user_id');
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param int $size Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $imageset Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $rating Maximum rating (inclusive) [ g | pg | r | x ]
     * @return String containing either just a URL or a complete image tag
     */
    public function gravatar(int $size = 80, string $imageset = 'mp', string $rating = 'g'): string
    {
        return sprintf(
            'https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s',
            md5(strtolower(trim($this->email))),
            $size,
            $imageset,
            $rating
        );
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function delete(): bool | null
    {
        if ($this->server) {
            app(ServerService::class)->delete($this->server);
        }

        return parent::delete();
    }

    public function canAccessFilament(): bool
    {
        return true;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Server
 *
 * @property int $server_id
 * @property int $user_id
 * @property string $private_key
 * @property string $public_key
 * @property string $address
 * @property int $port
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read int|null $devices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUserId($value)
 * @method static \Database\Factories\ServerFactory factory(...$parameters)
 * @mixin \Eloquent
 */
class Server extends Model
{
    use HasFactory;

    protected $primaryKey = 'server_id';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'private_key',
        'public_key',
        'address',
        'port',
    ];

    /**
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'private_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'server_id', 'server_id');
    }
}

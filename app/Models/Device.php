<?php

namespace App\Models;

use App\DTO\EnrichDevice;
use App\Observers\DeviceObserver;
use App\Services\ServerService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Device
 *
 * @property string $client_id
 * @property int $server_id
 * @property string $name
 * @property string $preshared_key
 * @property string $public_key
 * @property string $device_id
 * @property int $keep_alive
 * @property string $is_active
 * @property string $private_key
 * @property string $address
 * @property int $mtu
 * @property mixed $dns
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Server|null $server
 * @method static \Illuminate\Database\Eloquent\Builder|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device query()
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device wherePresharedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereKeepAlive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereDns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereMtu($value)
 * @method static \Database\Factories\DeviceFactory factory(...$parameters)
 * @mixin \Eloquent
 */
class Device extends Model
{
    use HasFactory;

    protected $primaryKey = 'device_id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'server_id',
        'name',
        'public_key',
        'private_key',
        'preshared_key',
        'address',
        'keep_alive',
        'is_active',
        'mtu',
        'dns',
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
    protected $casts = [
        'dns' => 'array',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'transferTx',
        'transferRx',
        'latestHandshakeAt',
        'latestHandshakeAt',
        'isOnline',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'private_key',
        'preshared_key',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::observe(DeviceObserver::class);
    }

    public function getTransferTxAttribute(): ?int
    {
        $traffic = \Cache::getMultiple([
            "wg_tx_memory_{$this->device_id}",
            "wg_tx_current_{$this->device_id}"
        ], [0, $this->getEnrich()?->getTransferTx() ?? 0]);

        return array_sum($traffic);
    }

    public function getTransferRxAttribute(): ?int
    {
        $traffic = \Cache::getMultiple([
            "wg_rx_memory_{$this->device_id}",
            "wg_rx_current_{$this->device_id}"
        ], [0, $this->getEnrich()?->getTransferRx() ?? 0]);

        return array_sum($traffic);
    }

    public function getLatestHandshakeAtAttribute(): ?Carbon
    {
        return $this->getEnrich()?->getLatestHandshakeAt() > 0
            ? Carbon::createFromTimestamp($this->getEnrich()?->getLatestHandshakeAt())
            : null
        ;
    }

    public function getIsOnlineAttribute(): ?bool
    {
        return $this->getEnrich()?->getLatestHandshakeAt() > 0
            && Carbon::createFromTimestamp($this->getEnrich()?->getLatestHandshakeAt())
                ->toImmutable()
                ->addMinutes(5)
                ->gte(Carbon::now())
        ;
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'server_id');
    }

    private function getEnrich(): ?EnrichDevice
    {
        if (null === $this->server) {
            return null;
        }

        $data = app(ServerService::class)->getWgDevicesInfo($this->server);

        return $data[$this->public_key] ?? null;
    }
}

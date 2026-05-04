<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'payment_method',
        'trackingNumber',
        'total_amount',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatus(): string
    {
        return ucfirst((string) $this->status);
    }

    public function canBeCancelled(): bool
    {
        return in_array((string) $this->status, [self::STATUS_PENDING, self::STATUS_PAID], true);
    }

    public function markAsShipped(): void
    {
        if ((string) $this->status === self::STATUS_SHIPPED) {
            return;
        }

        $this->forceFill([
            'status' => self::STATUS_SHIPPED,
        ])->save();
    }
}

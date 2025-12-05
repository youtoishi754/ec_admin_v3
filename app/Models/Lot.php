<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'm_lots';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'lot_number',
        'goods_id',
        'manufacturing_date',
        'expiry_date',
        'received_date',
        'quantity_received',
        'quantity_remaining',
        'supplier_name',
        'notes',
        'is_active',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'received_date' => 'date',
        'quantity_received' => 'integer',
        'quantity_remaining' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * リレーション: 商品
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods', 'goods_id');
    }

    /**
     * スコープ: 有効なロットのみ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * スコープ: 在庫があるロットのみ
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity_remaining', '>', 0);
    }

    /**
     * スコープ: 有効期限切れ間近
     */
    public function scopeExpiringWithinDays($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereRaw('DATEDIFF(expiry_date, NOW()) <= ?', [$days])
                     ->whereRaw('DATEDIFF(expiry_date, NOW()) > 0');
    }

    /**
     * スコープ: 有効期限切れ
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', now());
    }

    /**
     * 有効期限までの日数を取得
     */
    public function getDaysToExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * 有効期限ステータスを取得
     */
    public function getExpiryStatusAttribute()
    {
        $days = $this->days_to_expiry;

        if ($days === null) {
            return 'none';
        }

        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 7) {
            return 'critical';
        } elseif ($days <= 30) {
            return 'warning';
        }

        return 'normal';
    }

    /**
     * 有効期限ステータスのバッジHTML
     */
    public function getExpiryStatusBadgeAttribute()
    {
        $status = $this->expiry_status;

        $badges = [
            'none' => '<span class="badge badge-secondary">期限なし</span>',
            'expired' => '<span class="badge badge-danger">期限切れ</span>',
            'critical' => '<span class="badge badge-danger">切迫</span>',
            'warning' => '<span class="badge badge-warning">警告</span>',
            'normal' => '<span class="badge badge-success">正常</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">不明</span>';
    }
}

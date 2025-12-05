<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't_inventories';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'goods_id',
        'warehouse_id',
        'location_id',
        'lot_number',
        'serial_number',
        'quantity',
        'reserved_quantity',
        'expiry_date',
        'manufacturing_date',
        'received_date',
        'alert_threshold',
        'status',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
        'received_date' => 'date',
        'alert_threshold' => 'integer',
    ];

    /**
     * available_quantityは生成カラムなので$appends不要
     * ただしアクセサーで取得可能にする
     */
    public function getAvailableQuantityAttribute($value)
    {
        return $value ?? ($this->quantity - $this->reserved_quantity);
    }

    /**
     * リレーション: 商品
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods', 'goods_id');
    }

    /**
     * リレーション: 倉庫
     */
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    }

    /**
     * リレーション: ロケーション
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    /**
     * スコープ: 在庫ありのみ
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * スコープ: 利用可能在庫ありのみ
     */
    public function scopeAvailable($query)
    {
        return $query->whereRaw('(quantity - reserved_quantity) > 0');
    }

    /**
     * スコープ: 低在庫
     */
    public function scopeLowStock($query)
    {
        return $query->where('status', 'low_stock');
    }

    /**
     * スコープ: 欠品
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
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
        return $query->where('status', 'expired')
                     ->orWhere('expiry_date', '<', now());
    }

    /**
     * ステータスバッジのHTML取得
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'normal' => '<span class="badge badge-success">正常</span>',
            'low_stock' => '<span class="badge badge-warning">低在庫</span>',
            'out_of_stock' => '<span class="badge badge-danger">欠品</span>',
            'excess' => '<span class="badge badge-info">過剰</span>',
            'expired' => '<span class="badge badge-dark">期限切れ</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">不明</span>';
    }

    /**
     * ステータスを自動判定して更新
     */
    public function updateStatus()
    {
        if ($this->expiry_date && $this->expiry_date < now()) {
            $this->status = 'expired';
        } elseif ($this->quantity == 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->alert_threshold && $this->quantity <= $this->alert_threshold) {
            $this->status = 'low_stock';
        } elseif ($this->goods && $this->goods->max_stock_level && $this->quantity > $this->goods->max_stock_level) {
            $this->status = 'excess';
        } else {
            $this->status = 'normal';
        }

        $this->save();
    }
}

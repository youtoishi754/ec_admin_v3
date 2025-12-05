<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'm_warehouses';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'postal_code',
        'prefecture_id',
        'city',
        'address_line',
        'manager_name',
        'phone',
        'is_active',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * リレーション: 都道府県
     */
    public function prefecture()
    {
        return $this->belongsTo('App\Models\Prefecture', 'prefecture_id');
    }

    /**
     * リレーション: ロケーション
     */
    public function locations()
    {
        return $this->hasMany('App\Models\Location', 'warehouse_id');
    }

    /**
     * リレーション: 在庫
     */
    public function inventories()
    {
        return $this->hasMany('App\Models\Inventory', 'warehouse_id');
    }

    /**
     * リレーション: 入出庫履歴
     */
    public function stockMovements()
    {
        return $this->hasMany('App\Models\StockMovement', 'warehouse_id');
    }

    /**
     * リレーション: 在庫アラート
     */
    public function stockAlerts()
    {
        return $this->hasMany('App\Models\StockAlert', 'warehouse_id');
    }

    /**
     * スコープ: 有効な倉庫のみ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * 完全住所を取得
     */
    public function getFullAddressAttribute()
    {
        $address = '';
        if ($this->postal_code) {
            $address .= '〒' . $this->postal_code . ' ';
        }
        if ($this->prefecture) {
            $address .= $this->prefecture->name;
        }
        if ($this->city) {
            $address .= $this->city;
        }
        if ($this->address_line) {
            $address .= $this->address_line;
        }
        return $address;
    }
}

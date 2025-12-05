<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'm_locations';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_id',
        'location_code',
        'aisle',
        'rack',
        'shelf',
        'capacity',
        'is_active',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * リレーション: 倉庫
     */
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    }

    /**
     * リレーション: 在庫
     */
    public function inventories()
    {
        return $this->hasMany('App\Models\Inventory', 'location_id');
    }

    /**
     * スコープ: 有効なロケーションのみ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * スコープ: 特定倉庫のロケーション
     */
    public function scopeForWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * ロケーション詳細を取得
     */
    public function getLocationDetailAttribute()
    {
        return sprintf(
            '%s (通路:%s 棚:%s 段:%s)',
            $this->location_code,
            $this->aisle ?? '-',
            $this->rack ?? '-',
            $this->shelf ?? '-'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't_goods';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'un_id',
        'goods_number',
        'goods_name',
        'image_path',
        'goods_price',
        'tax_rate',
        'goods_stock',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'lead_time_days',
        'is_lot_managed',
        'is_serial_managed',
        'expiry_alert_days',
        'category_id',
        'goods_detail',
        'intro_txt',
        'disp_flg',
        'delete_flg',
        'sales_start_at',
        'sales_end_at',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'goods_price' => 'integer',
        'tax_rate' => 'integer',
        'goods_stock' => 'integer',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'reorder_point' => 'integer',
        'lead_time_days' => 'integer',
        'is_lot_managed' => 'boolean',
        'is_serial_managed' => 'boolean',
        'expiry_alert_days' => 'integer',
        'category_id' => 'integer',
        'disp_flg' => 'boolean',
        'delete_flg' => 'boolean',
        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
    ];

    /**
     * リレーション: 在庫
     */
    public function inventories()
    {
        return $this->hasMany('App\Models\Inventory', 'goods_id');
    }

    /**
     * リレーション: 入出庫履歴
     */
    public function stockMovements()
    {
        return $this->hasMany('App\Models\StockMovement', 'goods_id');
    }

    /**
     * リレーション: 在庫アラート
     */
    public function stockAlerts()
    {
        return $this->hasMany('App\Models\StockAlert', 'goods_id');
    }

    /**
     * リレーション: ロット
     */
    public function lots()
    {
        return $this->hasMany('App\Models\Lot', 'goods_id');
    }

    /**
     * 合計在庫数を取得
     */
    public function getTotalInventoryAttribute()
    {
        return $this->inventories()->sum('quantity');
    }

    /**
     * 合計利用可能在庫数を取得
     */
    public function getTotalAvailableInventoryAttribute()
    {
        return $this->inventories()->sum('available_quantity');
    }

    /**
     * 在庫ステータスを取得
     */
    public function getInventoryStatusAttribute()
    {
        $total = $this->total_inventory;

        if ($total == 0) {
            return 'out_of_stock';
        } elseif ($this->min_stock_level && $total <= $this->min_stock_level) {
            return 'low_stock';
        } elseif ($this->max_stock_level && $total > $this->max_stock_level) {
            return 'excess';
        }

        return 'normal';
    }

    /**
     * 在庫ステータスのバッジHTML
     */
    public function getInventoryStatusBadgeAttribute()
    {
        $status = $this->inventory_status;

        $badges = [
            'normal' => '<span class="badge badge-success">正常</span>',
            'low_stock' => '<span class="badge badge-warning">低在庫</span>',
            'out_of_stock' => '<span class="badge badge-danger">欠品</span>',
            'excess' => '<span class="badge badge-info">過剰</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">不明</span>';
    }

    /**
     * 発注が必要かチェック
     */
    public function needsReorder()
    {
        if (!$this->reorder_point) {
            return false;
        }

        return $this->total_available_inventory <= $this->reorder_point;
    }

    /**
     * スコープ: 有効な商品のみ
     */
    public function scopeActive($query)
    {
        return $query->where('delete_flg', 0);
    }

    /**
     * スコープ: 表示可能な商品のみ
     */
    public function scopeDisplayable($query)
    {
        return $query->where('disp_flg', 1);
    }

    /**
     * スコープ: ロット管理対象商品
     */
    public function scopeLotManaged($query)
    {
        return $query->where('is_lot_managed', 1);
    }

    /**
     * スコープ: シリアル管理対象商品
     */
    public function scopeSerialManaged($query)
    {
        return $query->where('is_serial_managed', 1);
    }
}

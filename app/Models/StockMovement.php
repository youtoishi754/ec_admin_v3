<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't_stock_movements';

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
        'movement_type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
        'movement_date',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'before_quantity' => 'integer',
        'after_quantity' => 'integer',
        'movement_date' => 'datetime',
    ];

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
     * リレーション: ユーザー（処理者）
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * スコープ: 入庫のみ
     */
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'in');
    }

    /**
     * スコープ: 出庫のみ
     */
    public function scopeOut($query)
    {
        return $query->where('movement_type', 'out');
    }

    /**
     * スコープ: 期間指定
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * 入出庫区分の日本語表示
     */
    public function getMovementTypeNameAttribute()
    {
        $types = [
            'in' => '入庫',
            'out' => '出庫',
            'adjust' => '在庫調整',
            'transfer' => '倉庫間移動',
            'return' => '返品入庫',
            'reserve' => '引当',
            'release' => '引当解除',
        ];

        return $types[$this->movement_type] ?? $this->movement_type;
    }

    /**
     * 入出庫区分のバッジHTML
     */
    public function getMovementTypeBadgeAttribute()
    {
        $badges = [
            'in' => '<span class="badge badge-primary">入庫</span>',
            'out' => '<span class="badge badge-danger">出庫</span>',
            'adjust' => '<span class="badge badge-warning">調整</span>',
            'transfer' => '<span class="badge badge-info">移動</span>',
            'return' => '<span class="badge badge-success">返品</span>',
            'reserve' => '<span class="badge badge-secondary">引当</span>',
            'release' => '<span class="badge badge-light">解除</span>',
        ];

        return $badges[$this->movement_type] ?? '<span class="badge badge-secondary">' . $this->movement_type . '</span>';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't_stock_alerts';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'goods_id',
        'warehouse_id',
        'alert_type',
        'current_quantity',
        'threshold_quantity',
        'expiry_date',
        'alert_date',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'notes',
    ];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'current_quantity' => 'integer',
        'threshold_quantity' => 'integer',
        'expiry_date' => 'date',
        'alert_date' => 'datetime',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
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
     * リレーション: 解決者
     */
    public function resolver()
    {
        return $this->belongsTo('App\User', 'resolved_by');
    }

    /**
     * スコープ: 未解決のみ
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', 0);
    }

    /**
     * スコープ: 解決済みのみ
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', 1);
    }

    /**
     * スコープ: アラート種別指定
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * アラート種別の日本語表示
     */
    public function getAlertTypeNameAttribute()
    {
        $types = [
            'low_stock' => '低在庫',
            'out_of_stock' => '欠品',
            'excess' => '過剰在庫',
            'expiry_warning' => '有効期限警告',
            'expiry_critical' => '有効期限切迫',
        ];

        return $types[$this->alert_type] ?? $this->alert_type;
    }

    /**
     * アラート種別のバッジHTML
     */
    public function getAlertTypeBadgeAttribute()
    {
        $badges = [
            'low_stock' => '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> 低在庫</span>',
            'out_of_stock' => '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> 欠品</span>',
            'excess' => '<span class="badge badge-info"><i class="fas fa-boxes"></i> 過剰在庫</span>',
            'expiry_warning' => '<span class="badge badge-warning"><i class="fas fa-clock"></i> 期限警告</span>',
            'expiry_critical' => '<span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> 期限切迫</span>',
        ];

        return $badges[$this->alert_type] ?? '<span class="badge badge-secondary">' . $this->alert_type . '</span>';
    }

    /**
     * アラートを解決済みにする
     */
    public function resolve($userId = null, $notes = null)
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->resolved_by = $userId;
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }

    /**
     * 優先度を取得（1:高 2:中 3:低）
     */
    public function getPriorityAttribute()
    {
        $priorities = [
            'out_of_stock' => 1,
            'expiry_critical' => 1,
            'low_stock' => 2,
            'expiry_warning' => 2,
            'excess' => 3,
        ];

        return $priorities[$this->alert_type] ?? 3;
    }
}

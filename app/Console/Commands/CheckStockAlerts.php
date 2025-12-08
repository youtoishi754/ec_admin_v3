<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StockAlertService;

class CheckStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在庫アラートをチェックして、低在庫・欠品・有効期限切れなどのアラートを生成します';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('在庫アラートのチェックを開始します...');

        $service = new StockAlertService();
        
        try {
            $service->checkAllStockAlerts();
            $this->info('在庫アラートのチェックが完了しました。');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('エラーが発生しました: ' . $e->getMessage());
            return 1;
        }
    }
}

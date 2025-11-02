<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    |
    | single:デフォルトpathならlaravel.logファイルのみにログメッセージを書き込むチャンネルです。(DIR:storage/logs/)
    | daily:singleと同様にファイルにログメッセージを書き込むのですが、singleとは異なりファイル名に日付が付きます。(DIR:storage/logs/)
    | slack:slackのアプリケーションにログメッセージを送信する際に使うチャンネルです。
    | channelsは複数追加可能。(例:['single','slack'])
    | syslog:SyslogHandlerベースのMonologドライバ
    | errorlog:ErrorLogHandlerベースのMonologドライバ
    | monolog:サポートしているMonologハンドラをどれでも使用できる、Monologファクトリドライバ
    | custom:チャンネルを生成するため、指定したファクトリを呼び出すドライバ
    |
    |
    |
    |
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        //商品テーブルチャンネル
        't_goods' => [
            'driver' => 'single',
            'path' => storage_path('logs/t_goods.log'),
            'level' => 'info',
        ],

        //会員テーブルチャンネル
        't_members' => [
            'driver' => 'single',
            'path' => storage_path('logs/t_members.log'),
            'level' => 'info',
        ]
    ]

];

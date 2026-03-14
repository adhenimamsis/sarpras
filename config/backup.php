<?php

return [
    'backup' => [
        /*
         * Nama aplikasi untuk identifikasi file backup.
         */
        'name' => 'SimSarpras-Puskesmas-Bendan',

        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('app/Backup-Sarpras-Puskesmas-Bendan'),
                    storage_path('app/backup-temp'),
                ],

                'follow_links' => false,
                'ignore_unreadable_directories' => true,
                'relative_path' => null,
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        /*
         * FIX: Dimatikan (null) karena Windows tidak mengenali perintah 'gzip'.
         * File tetap akan dikompres nanti saat dibungkus menjadi format ZIP.
         */
        'database_dump_compressor' => null,

        'database_dump_file_timestamp_format' => 'Y-m-d-H-i-s',
        'database_dump_filename_base' => 'db_sarpras',

        /*
         * FIX: Menggunakan ekstensi .sql agar hasil dump terbaca jelas.
         */
        'database_dump_file_extension' => 'sql',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 9,
            'filename_prefix' => 'BACKUP_',
            'disks' => [
                'local',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',
        'tries' => 3,
        'retry_delay' => 5,
    ],

    /*
     * Notifikasi:
     * Driver 'telegram' dilepas karena menyebabkan error 'undefined method toTelegram'.
     */
    'notifications' => [
        'notifications' => [
            Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['mail'],
        ],

        'notifiable' => Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('MAIL_FROM_ADDRESS', 'admin@Puskesmasbendan.com'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Sistem Sarpras'),
            ],
        ],

        'telegram' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_CHAT_ID'),
        ],
    ],

    'monitor_backups' => [
        [
            'name' => 'SimSarpras-Puskesmas-Bendan',
            'disks' => ['local'],
            'health_checks' => [
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 2000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days' => 3,
            'keep_daily_backups_for_days' => 7,
            'keep_weekly_backups_for_weeks' => 4,
            'keep_monthly_backups_for_months' => 6,
            'keep_yearly_backups_for_years' => 1,
            'delete_oldest_backups_when_using_more_megabytes_than' => 1000,
        ],

        'tries' => 3,
        'retry_delay' => 5,
    ],
];

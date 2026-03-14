<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// Perintah bawaan: Quote inspirasi setiap jam
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// --- JADWAL BACKUP OTOMATIS + NOTIFIKASI TELEGRAM ---

// Backup Database otomatis setiap jam 02:00 pagi dengan laporan otomatis
Schedule::command('backup:run --only-db')->dailyAt('02:00')
    ->onSuccess(function () {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $appName = env('APP_NAME');

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "✅ *BACKUP SUKSES!*\n\nSistem: *{$appName}*\nStatus: Database telah berhasil diamankan.\nWaktu: ".now()->format('d-m-Y H:i:s'),
            'parse_mode' => 'Markdown',
        ]);
    })
    ->onFailure(function () {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $appName = env('APP_NAME');

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "⚠️ *BACKUP GAGAL!*\n\nSistem: *{$appName}*\nBahaya: Database gagal diamankan. Segera cek server Laragon, Bos!",
            'parse_mode' => 'Markdown',
        ]);
    });

// Hapus file backup lama setiap jam 03:00 pagi (biar storage tidak bengkak)
Schedule::command('backup:clean')->dailyAt('03:00');

// --- PERINTAH CUSTOM UNTUK TEST TELEGRAM ---

Artisan::command('telegram:test', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');
    $appName = env('APP_NAME');

    $this->info('Sedang mengirim pesan test ke Telegram...');

    $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
        'chat_id' => $chatId,
        'text' => "✅ *Laporan Sistem*\n\nSistem: *{$appName}*\nStatus: Koneksi Bot Berhasil!\nTanggal: ".now()->format('d-m-Y H:i:s'),
        'parse_mode' => 'Markdown',
    ]);

    if ($response->successful()) {
        $this->info('Pesan berhasil dikirim ke Telegram!');
    } else {
        $this->error('Gagal mengirim pesan. Cek Token/Chat ID di .env Bos!');
    }
})->purpose('Cek apakah Bot Telegram sudah terhubung dengan benar');

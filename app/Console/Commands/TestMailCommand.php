<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'test:mail';

    protected $description = 'Gửi mail test để kiểm tra cấu hình SMTP';

    public function handle()
    {
        Mail::raw('Test email từ Laravel - SMTP OK!', function ($message) {
            $message->to('receiver@gmail.com')->subject('Test gửi mail từ Laravel');
        });

        $this->info('Mail đã được gửi!');
    }
}

<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('netlearn:info', function () {
    $this->line('NETLEARN TKJ · E-modul Topologi Jaringan dan Pengalamatan IPv4');
})->purpose('Show the NETLEARN TKJ module title.');

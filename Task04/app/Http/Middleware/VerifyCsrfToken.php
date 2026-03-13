<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // Отключаем CSRF для всех маршрутов API
    protected $except = [
        'api/*',
    ];
}
<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBackHistory; // ⬅️ IMPORTANTE: agrega esta línea
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 👇 Alias personalizados
        $middleware->alias([
            'role' => CheckRole::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class, // ⬅️ agregamos este alias
        ]);

        // 👇 Si deseas que se aplique a TODAS las rutas automáticamente
        // $middleware->append(PreventBackHistory::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();


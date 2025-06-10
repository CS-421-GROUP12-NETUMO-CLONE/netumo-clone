<?php

use App\Jobs\CheckTargetStatus;
use App\Models\Target;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*$exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                Log::error($e->getMessage());
                return response()->json([
                    'message' => "Error: something went wrong, try again later! "
                ], 404);
            }
        });*/
    })
    ->withSchedule(function (Schedule $schedule){
        $schedule->call(function (){
            $targets = Target::all();
            $targets->each(function ($target){
                CheckTargetStatus::dispatch($target);
            });
        })->everyFiveMinutes();

        $schedule->command('check:certificates')
            ->daily()
            ->emailOutputTo('edibilysamwely774@gmail.com');
    })
    ->create();

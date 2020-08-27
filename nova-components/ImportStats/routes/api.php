<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Module;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::get('/endpoint', function (Request $request) {
    $stats = [];
    $modules = Module::where('code', 'like', 'Import\\\Vulnerabilities\\\%')->get();

    foreach ($modules as $module) {
        $log = $module->logs()->orderBy('id', 'desc')->first();
        $lastRun = 'Never';
        if ($log) {
            $lastRun = $log->finished_at ? Carbon::now()->longAbsoluteDiffForHumans($log->finished_at) : Carbon::now()->longAbsoluteDiffForHumans($log->executed_at);
        }

        $stats[] = [
            'module' => str_replace('Import\\Vulnerabilities\\', '', $module->code),
            'last_run' => $lastRun,
            'status' => $log && $log->status ? $log->status->name : 'Error',
        ];
    }

    return response()->json(['stats' => $stats]);
});

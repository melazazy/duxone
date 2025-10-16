<?php

use Illuminate\Support\Facades\Route;

Route::get('/check', function () {
    $tenant = tenant();

    return [
        'tenant_id' => $tenant->id,
        'domain' => request()->getHost(),
        // 'database' => $tenant->database()->getName(),
    ];
});

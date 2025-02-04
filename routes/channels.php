<?php

use App\Http\Middleware\SocketTenantDomainMiddleware;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::routes([
    'middleware' => [
        'web',
        SocketTenantDomainMiddleware::class,
    ]
]);

Broadcast::channel('tenant.{tenant_id}.ws-test', function ($user, $tenant_id) {
    return (tenant()->id == $tenant_id) && $user->id == 1; //örnek user id == 1 olana göre dogrulama
});


<?php

use App\Http\Middleware\SocketTenantDomainMiddleware;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::routes([
    'middleware' => [
        'web',
        SocketTenantDomainMiddleware::class,
    ]
]);

Broadcast::channel('tenant.{tenant_id}.reportProcessed.{user_id}', function ($user, $tenant_id, $user_id) {
    return (tenant()->id == $tenant_id) && $user->id == $user_id;
});

// Song Processing kanalları için broadcast izni
Broadcast::channel('song.processing.{product_id}', function ($user, $product_id) {
    return true; // Kimlik doğrulamasından geçmiş tüm kullanıcılara izin ver
});

// Tenant specific song processing channel
Broadcast::channel('tenant.{tenant_id}.song.processing.{product_id}', function ($user, $tenant_id, $product_id) {
    return tenant()->id == $tenant_id; // Sadece doğru tenant'taki kullanıcılara izin ver
});



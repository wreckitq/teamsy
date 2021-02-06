<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Domain\Tenant\Models\Tenant;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
            }
        });
    }

    public function tenant()
    {
        $this->belongsTo(Tenant::class);
    }
}

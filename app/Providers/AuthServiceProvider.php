<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Department' => 'App\Policies\DepartmentPolicy',
        'App\Models\Role'       => 'App\Policies\RolePolicy',
        'App\Models\Permission' => 'App\Policies\PermissionPolicy',
        'App\Models\Inventories\Brand'      => 'App\Policies\BrandPolicy',
        'App\Models\Inventories\Product'    => 'App\Policies\ProductPolicy',
        'App\Models\Inventories\Inbound'    => 'App\Policies\InboundPolicy',
        'App\Models\Inventories\Outbound'    => 'App\Policies\OutboundPolicy',
        //Department::class => DepartmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

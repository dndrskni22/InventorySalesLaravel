<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // Admin / Owner
        Gate::define('manage-master', fn (User $user) => $user->isAdmin());
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('cancel-transaction', fn (User $user) => $user->isAdmin());

        // Admin dan Staff
        Gate::define('manage-sales', fn (User $user) => $user->isAdmin() || $user->isStaff());
        Gate::define('manage-purchases', fn (User $user) => $user->isAdmin() || $user->isStaff());
        Gate::define('manage-customers', fn (User $user) => $user->isAdmin() || $user->isStaff());

        // Data internal (harga beli, stok) hanya untuk Admin dan Staff.
        Gate::define('view-inventory', fn (User $user) => $user->isAdmin() || $user->isStaff());

        // Katalog dan pemesanan: semua pengguna yang sudah login
        // (staff mencatat pesanan, pelanggan memesan sendiri).
        Gate::define('place-order', fn (User $user) => true);
    }
}
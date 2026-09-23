<?php

namespace App\Providers;

use App\Support\Permissions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @canDo('Module', 'action') ... @endcanDo — gate a single button/link/form
        // in a Blade view by a module_access permission (see config/permissions.php).
        Blade::directive('canDo', function ($expression) {
            return "<?php if (\\App\\Support\\Permissions::check({$expression})): ?>";
        });

        Blade::directive('endcanDo', function () {
            return '<?php endif; ?>';
        });
    }
}

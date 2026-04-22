<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\SignupSettings::class);
    }

    public function boot(): void
    {
        /**
         * @module('module_key')
         *   ... content only shown when gym plan includes this module ...
         * @endmodule
         *
         * Super admins always pass. Gym owners need the module in their plan.
         */
        Blade::directive('module', function (string $expression): string {
            return "<?php if(auth()->check() && auth()->user()->gymHasModule({$expression})): ?>";
        });

        Blade::directive('endmodule', function (): string {
            return '<?php endif; ?>';
        });
    }
}


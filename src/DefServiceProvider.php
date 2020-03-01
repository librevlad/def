<?php

namespace Librevlad\Def;

use Illuminate\Support\ServiceProvider;

class DefServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return  void
     */
    public function register() {

        $this->app->singleton( 'def', Database::class );

        if ( $this->app->runningInConsole() ) {
            $this->publishes( [
                __DIR__ . '/../config/config.php' => config_path( 'def.php' ),
            ], 'config' );
        }

    }

    /**
     * Bootstrap services.
     *
     * @return  void
     */
    public function boot() {
        $this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'def' );
    }
}

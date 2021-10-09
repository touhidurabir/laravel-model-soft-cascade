<?php

namespace Touhidurabir\ModelSoftCascade;

use Illuminate\Support\ServiceProvider;

class ModelSoftCascadeServiceProvide extends ServiceProvider {
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        $this->publishes([
            __DIR__.'/../config/soft-cascade.php' => base_path('config/soft-cascade.php'),
        ], 'config');
    }
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        $this->mergeConfigFrom(
            __DIR__.'/../config/soft-cascade.php', 'soft-cascade'
        );
    }
    
}
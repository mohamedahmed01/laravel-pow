<?php

namespace Mohamedahmed01\LaravelPow;

use Illuminate\Support\ServiceProvider;
use Mohamedahmed01\LaravelPow\PowManager;

class LaravelPowServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pow.php', 'pow');
        
        $this->app->bind('Pow', function ($app) {
            return new PowManager($app['config']['pow.difficulty']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/pow.php' => config_path('pow.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        
        $this->publishes([
            __DIR__.'/resources/js/components/vue/ProofOfWork.vue' => resource_path('js/components/ProofOfWork.vue'),
        ], 'vue-component');
    
        $this->publishes([
            __DIR__.'/resources/js/components/react/ProofOfWork.jsx' => resource_path('js/components/ProofOfWork.jsx'),
        ], 'react-component');
    
        $this->publishes([
            __DIR__.'/resources/js/components/angular/proof-of-work.component.ts' => resource_path('js/components/proof-of-work.component.ts'),
        ], 'angular-component');
    }
}
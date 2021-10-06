<?php

namespace Touhidurabir\ModelSoftCascade\Tests\Traits;

use Touhidurabir\ModelSoftCascade\Facades\ModelSoftCascade;
use Touhidurabir\ModelSoftCascade\ModelSoftCascadeServiceProvider;

trait LaravelTestBootstrapping {

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app) {

        return [
            ModelSoftCascadeServiceProvider::class,
        ];
    }   
    
    
    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app) {
        
        return [
            'ModelSoftCascade' => ModelSoftCascade::class,
        ];
    }


    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app) {

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.url', 'http://localhost/');
        $app['config']->set('app.debug', false);
        $app['config']->set('app.key', env('APP_KEY', 'base64:4vaRSWQcoaHh8mA8qIRxL1Ei+UNIj9Wst9rY+ne2rE4='));
        $app['config']->set('app.cipher', 'AES-256-CBC');
        
        $app['config']->set('hasher.enable', true);
    }
}
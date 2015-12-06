<?php

namespace Oxygen\Data;

use Illuminate\Support\ServiceProvider as ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider {

    /**
     * Register a namespace of Doctrine entities.
     *
     * @param  string  $namespace
     * @return void
     */
    public function loadEntitiesFrom($namespace) {
        $metadata = $this->app['config']['doctrine.managers.default.namespaces'];
        $metadata[] = $namespace;
        $this->app['config']->set('doctrine.managers.default.namespaces', $metadata);
    }

}

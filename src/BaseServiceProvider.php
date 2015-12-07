<?php

namespace Oxygen\Data;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider as ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider {

    /**
     * Register a directory of Doctrine entities.
     *
     * @param  string  $directory
     * @return void
     */
    public function loadEntitiesFrom($directory) {
        $metadata = $this->app['config']['doctrine.managers.default.paths'];
        $metadata[] = $directory;
        $this->app['config']->set('doctrine.managers.default.paths', $metadata);
    }

    /**
     * Modify the entity manager
     *
     * @param callable $closure
     */
    public function extendEntityManager(callable $closure) {
        // use 'em' instead of full class name to get around bug: https://github.com/laravel/framework/issues/11226
        if($this->app->resolved('em')) {
            $closure($this->app[EntityManager::class]);
        } else {
            $this->app->resolving(EntityManager::class, $closure);
        }
    }

}

<?php

namespace Oxygen\Data;

use Illuminate\Support\ServiceProvider as ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider {

    /**
     * Register a directory of Doctrine entities.
     *
     * @param  string  $directory
     * @return void
     */
    public function loadEntitiesFrom($directory) {
        $metadata = $this->app['config']['doctrine.metadata'];
        $metadata[] = $directory;
        $this->app['config']->set('doctrine.metadata', $metadata);
    }

}

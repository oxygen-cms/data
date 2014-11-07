<?php

namespace Oxygen\Data;

use Illuminate\Support\ServiceProvider;
use Oxygen\Data\Validation\Laravel\LaravelValidationService;
use Oxygen\Data\Validation\ValidationSubscriber;

class DataServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */

    public function register() {
        $this->app->resolving('Doctrine\ORM\EntityManagerInterface', function($entities) {
            $entities->getEventManager()
                     ->addEventSubscriber(
                         new ValidationSubscriber(new LaravelValidationService($this->app['validator']))
                     );
        });

        $this->app->bind('Oxygen\Data\Pagination\PaginationService', 'Oxygen\Data\Pagination\Laravel\LaravelPaginationService');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */

    public function provides() {
        return [
            'Doctrine\ORM\EntityManagerInterface'
        ];
    }

}

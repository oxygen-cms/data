<?php

namespace Oxygen\Data;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;
use Oxygen\Data\Pagination\Laravel\LaravelPaginationService;
use Oxygen\Data\Pagination\PaginationService;
use Oxygen\Data\Subscriber\CacheInvalidationSubscriber;
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
        $function = function($entities) {
            $entities->getEventManager()
                ->addEventSubscriber(new ValidationSubscriber(new LaravelValidationService($this->app['validator'])));
            $entities->getEventManager()
                ->addEventSubscriber(new CacheInvalidationSubscriber($this->app['events']));
        };
        if($this->app->resolved(EntityManager::class)) {
            $function($this->app[EntityManager::class]);
        } else {
            $this->app->resolving(EntityManager::class, $function);
        }

        $this->app->bind(PaginationService::class, LaravelPaginationService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [
            EntityManager::class,
            PaginationService::class
        ];
    }

}

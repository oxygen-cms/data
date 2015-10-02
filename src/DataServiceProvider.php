<?php

namespace Oxygen\Data;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;
use Oxygen\Data\Cache\CacheSettingsRepositoryInterface;
use Oxygen\Data\Cache\StubCacheSettingsRepository;
use Oxygen\Data\Pagination\Laravel\LaravelPaginationService;
use Oxygen\Data\Pagination\PaginationService;
use Oxygen\Data\Cache\CacheInvalidationSubscriber;
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
                ->addEventSubscriber(new CacheInvalidationSubscriber($this->app['events'], $this->app[CacheSettingsRepositoryInterface::class]));
        };
        if($this->app->resolved(EntityManager::class)) {
            $function($this->app[EntityManager::class]);
        } else {
            $this->app->resolving(EntityManager::class, $function);
        }

        $this->app->bind(PaginationService::class, LaravelPaginationService::class);
        $this->app->bind(CacheSettingsRepositoryInterface::class, StubCacheSettingsRepository::class);
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

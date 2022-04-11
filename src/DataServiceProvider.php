<?php

namespace Oxygen\Data;

use Doctrine\ORM\EntityManager;
use Oxygen\Data\Behaviour\BlameableSubscriber;
use Oxygen\Data\Pagination\Laravel\LaravelPaginationService;
use Oxygen\Data\Pagination\PaginationService;
use Oxygen\Data\Validation\Laravel\LaravelValidationService;
use Oxygen\Data\Validation\ValidationSubscriber;

class DataServiceProvider extends BaseServiceProvider {

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
        $this->app->bind(PaginationService::class, LaravelPaginationService::class);

        $this->extendEntityManager(function(EntityManager $entities) {
            $entities->getEventManager()
                     ->addEventSubscriber(new ValidationSubscriber(new LaravelValidationService($this->app['validator'])));
            $entities->getEventManager()
                     ->addEventSubscriber(new BlameableSubscriber($this->app['auth']));
        });
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

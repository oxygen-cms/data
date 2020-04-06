<?php


namespace Oxygen\Data\Migrations;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Oxygen\Data\BaseServiceProvider;

/**
 * Class MigrationsServiceProvider
 * @package Oxygen\Data\Migrations
 * @deprecated moving to laravel-doctrine/migrations
 */
class MigrationsServiceProvider extends BaseServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = true;

    /**
     * Registers the migration repository.
     */
    public function register() {
        $this->loadEntitiesFrom(__DIR__);

        $this->app->singleton('migration.repository', function($app) {
            return new DoctrineMigrationRepository(
                function() use($app) {
                    return $app->make(EntityManagerInterface::class);
                },
                function() use($app) {
                    return $app->make(SchemaTool::class);
                },
                function() use($app) {
                    return $app->make(ClassMetadataFactory::class);
                }
            );
        });
        $this->app->bind(MigrationRepositoryInterface::class, 'migration.repository');
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides() {
        return [
            EntityManagerInterface::class,
            EntityManager::class,
            MigrationRepositoryInterface::class,
            'migration.repository'
        ];
    }

}
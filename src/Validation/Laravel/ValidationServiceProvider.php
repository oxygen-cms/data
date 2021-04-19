<?php

namespace Oxygen\Data\Validation\Laravel;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider implements DeferrableProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $app = $this->app;

        $this->app['validator']->resolver(function(Translator $translator, array $data, array $rules, array $messages, array $customAttributes) use($app) {
            return new Validator(
                $translator,
                $app[Hasher::class],
                $app['view'],
                $app['router'],
                $data,
                $rules,
                $messages,
                $customAttributes
            );
        });
    }

    public function register() {
        $this->registerPresenceVerifier();
    }

    /**
     * Register the database presence verifier.
     *
     * @return void
     */
    protected function registerPresenceVerifier() {
        $this->app->singleton('validation.presence', function ($app) {
            return new DoctrinePresenceVerifier($app['registry']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [
            'validator', 'validation.presence',
        ];
    }

}

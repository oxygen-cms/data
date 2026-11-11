<?php

namespace Oxygen\Data\Validation\Laravel;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $app = $this->app;

        // This provider is deliberately NOT deferred: Laravel 12 no longer lets a deferred provider reliably
        // replace the framework's `validator` binding, so we wrap the framework's factory once it exists.
        // Resolving it here also registers the framework's own (Eloquent) presence verifier, so our Doctrine
        // verifier is bound afterwards to take precedence.
        $factory = $this->app['validator'];
        $this->app->singleton('validation.presence', function ($app) {
            return new DoctrinePresenceVerifier($app['registry']);
        });

        $factory->resolver(function(Translator $translator, array $data, array $rules, array $messages, array $customAttributes) use($app) {
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
        $factory->setPresenceVerifier($this->app['validation.presence']);
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


}

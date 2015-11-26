# Oxygen - Data

This repository contains the Oxygen Data Layer.

For more information visit the [Core](https://github.com/oxygen-cms/core) repository.

## Doctrine 2 Wrapper

The `data` package is focused around the robust Doctrine 2 ORM.

## Behaviours

- `Accessors` provides automatic `getXYZ` and `setXYZ` methods on entities
- `Authentication` provides an email, password and remember token.
- `CacheInvalidator` adds a `cacheInvalidationSettings` field, where different entities can be registered. When `$this` is updated, the event `oxygen.entity.cache.invalidated` will be fired for each of the entities inside `cacheInvalidationSettings`.
- `Fillable` adds the `fromArray` method which can update entity fields from an input array
- `PrimaryKey` adds a simple ID field with getters and setters.
- `Publishes` adds a `stage` field, entities can be either a *draft* or *published*
- `RememberToken` provides an email and remember token.
- `SoftDeletes` adds the `deletedAt` field, call `delete()` on the entity to 'soft' delete it.
- `Timestamps` adds `createdAt` and `updatedAt` fields that are automatically updated
- `Versions` adds versioning capabilities to the entity (**warning:** it requires two fields (`versions` and `headVersion`) to be already present in the entity and set up with the correct relationship information)
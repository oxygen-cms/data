<?php

namespace Oxygen\Data\Validation;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Oxygen\Data\Exception\InvalidEntityException;

class ValidationSubscriber implements EventSubscriber {

    /**
     * The validation service.
     *
     * @var ValidationService
     */

    protected $validator;

    /**
     * Constructs the ValidationSubscriber
     *
     * @param ValidationService $validator
     */
    public function __construct(ValidationService $validator) {
        $this->validator = $validator;
    }

    /**
     * Returns the subscribed events.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [Events::prePersist, Events::preUpdate];
    }

    /**
     * Performs validation before the entity is persisted.
     *
     * @param LifecycleEventArgs $args
     * @throws InvalidEntityException if the entity is invalid
     * @return void
     */

    protected function validate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));
        if($this->shouldBeValidated($entity)) {
            $rules = $entity->getValidationRules();
            $fields = $metadata->getFieldNames();
            $data = [];
            foreach($fields as $field) {
                $data[$field] = $metadata->getFieldValue($entity, $field);
            }

            $this->validator->with($data, $rules);
            if(!$this->validator->passes()) {
                throw new InvalidEntityException($entity, $this->validator->errors());
            }
        }
    }

    /**
     * Determines if the entity should be validated.
     *
     * @param object $entity
     * @return bool
     */

    protected function shouldBeValidated($entity) {
        return $entity instanceof Validatable;
    }

    public function prePersist(LifecycleEventArgs $args) {
        $this->validate($args);
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $this->validate($args);
    }

}

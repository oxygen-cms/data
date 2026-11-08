<?php

namespace Oxygen\Data\Validation;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Oxygen\Data\Exception\InvalidEntityException;

class ValidationSubscriber implements EventSubscriber {

    /**
     * The validation service.
     *
     * @var ValidationService
     */
    protected ValidationService $validator;

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
     * @param PrePersistEventArgs|PreUpdateEventArgs $args
     * @throws InvalidEntityException if the entity is invalid
     * @return void
     */
    protected function validate(PrePersistEventArgs|PreUpdateEventArgs $args) {
        $entity = $args->getObject();
        $metadata = $args->getObjectManager()->getClassMetadata(get_class($entity));
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
    protected function shouldBeValidated($entity): bool {
        return $entity instanceof Validatable;
    }

    /**
     * @throws InvalidEntityException
     */
    public function prePersist(PrePersistEventArgs $args) {
        $this->validate($args);
    }

    /**
     * @param PrePersistEventArgs|PreUpdateEventArgs $args
     * @throws InvalidEntityException
     */
    public function preUpdate(PreUpdateEventArgs $args) {
        $this->validate($args);
    }

}

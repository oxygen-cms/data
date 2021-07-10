<?php

namespace Oxygen\Data\Exception;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Oxygen\Core\Http\Notification;

class InvalidEntityException extends Exception {

    /**
     * The invalid entity
     *
     * @var object
     */

    protected $entity;

    /**
     * The validation errors.
     *
     * @var MessageBag
     */

    protected $errors;

    /**
     * Constructs the InvalidEntityException.
     *
     * @param object      $entity the invalid entity
     * @param MessageBag  $errors
     */
    public function __construct($entity, MessageBag $errors) {
        parent::__construct('Invalid Entity: ' . $errors->first());
        $this->entity = $entity;
        $this->errors = $errors;
    }

    /**
     * Returns the invalid entity.
     *
     * @return object
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * Returns the error messages.
     *
     * @return MessageBag
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request) {
        return response()->json([
            'content' => $this->getErrors()->first(),
            'status' => 'failed'
        ]);
    }

}

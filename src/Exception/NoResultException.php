<?php

namespace Oxygen\Data\Exception;

use Exception;
use RuntimeException;

class NoResultException extends RuntimeException {

    /**
     * Constructor.
     *
     * @param Exception $previous
     * @param string $sql
     */
    public function __construct(Exception $previous = null, $sql) {
        parent::__construct('No result was found for query (Query: "' . $sql . '")', 0, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request) {
        return response()->noContent(404);
    }

}

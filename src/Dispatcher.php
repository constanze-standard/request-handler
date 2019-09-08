<?php

/**
 * Copyright 2019 alex <omytty.alex@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ConstanzeStandard\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A handler for dispatch the request to middleware.
 * 
 * @author alex <omytty.alex@gmail.com>
 */
class Dispatcher implements RequestHandlerInterface
{
    /**
     * The outermost request handler.
     *
     * @var RequestHandlerInterface
     */
    protected $currentRequestHandler;

    /**
     * @param RequestHandlerInterface $coreRequestHandler
     */
    public function __construct(RequestHandlerInterface $coreRequestHandler)
    {
        $this->currentRequestHandler = $coreRequestHandler;
    }

    /**
     * Rewind the request handler stack.
     * 
     * @param RequestHandlerInterface $coreRequestHandler
     */
    public function rewind(RequestHandlerInterface $requestHandler)
    {
        $this->currentRequestHandler = $requestHandler;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->currentRequestHandler->handle($request);
    }

    /**
     * Add a PSR-15 middleware.
     * 
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $nextRequestHandler = $this->currentRequestHandler;
        $this->currentRequestHandler = new ProcessRequestHandler($middleware, $nextRequestHandler);
    }
}

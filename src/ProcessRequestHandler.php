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
 * The request handler for processing middlewares.
 * 
 * @author alex <omytty.alex@gmail.com>
 */
class ProcessRequestHandler implements RequestHandlerInterface
{
    /**
     * The PSR-15 middleware.
     * 
     * @var MiddlewareInterface $middleware
     */
    private $middleware;

    /**
     * The next request handler.
     * 
     * @var RequestHandlerInterface
     */
    private $nextRequestHandler;

    /**
     * @param MiddlewareInterface $middleware
     * @param RequestHandlerInterface $nextRequestHandler
     */
    public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $nextRequestHandler)
    {
        $this->middleware = $middleware;
        $this->nextRequestHandler = $nextRequestHandler;
    }

    /**
     * Process middleware.
     * 
     * @param ServerRequestInterface $request
     * 
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->nextRequestHandler);
    }
}

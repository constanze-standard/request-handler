<?php

use ConstanzeStandard\RequestHandler\ProcessRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

require_once __DIR__ . '/AbstractTest.php';

class ProcessRequestHandlerTest extends AbstractTest
{
    public function testHandle()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->createMock(MiddlewareInterface::class);
        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $middleware->expects($this->once())->method('process')->with($request, $requestHandler)->willReturn($response);
        $handler = new ProcessRequestHandler($middleware, $requestHandler);
        $result = $handler->handle($request);
        $this->assertEquals($result, $response);
    }
}

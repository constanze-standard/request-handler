<?php

use ConstanzeStandard\RequestHandler\Dispatcher;
use ConstanzeStandard\RequestHandler\ProcessRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

require_once __DIR__ . '/AbstractTest.php';

class DispatcherTest extends AbstractTest
{
    public function testHandle()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);
        $requestHandler->expects($this->once())->method('handle')->with($request)->willReturn($response);
        $dispatcher = new Dispatcher($requestHandler);
        $result = $dispatcher->handle($request);
        $this->assertEquals($result, $response);
    }

    public function testAddMiddleware()
    {
        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);
        $dispatcher = new Dispatcher($requestHandler);
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->createMock(MiddlewareInterface::class);
        $dispatcher->addMiddleware($middleware);
        $currentRequestHandler = $this->getProperty($dispatcher, 'currentRequestHandler');
        $this->assertInstanceOf(ProcessRequestHandler::class, $currentRequestHandler);
    }

    public function testRewind()
    {
        /** @var RequestHandlerInterface $requestHandler1 */
        $requestHandler1 = $this->createMock(RequestHandlerInterface::class);
        /** @var RequestHandlerInterface $requestHandler2 */
        $requestHandler2 = $this->createMock(RequestHandlerInterface::class);
        $dispatcher = new Dispatcher($requestHandler1);
        $dispatcher->rewind($requestHandler2);

        $currentRequestHandler = $this->getProperty($dispatcher, 'currentRequestHandler');
        $this->assertEquals($requestHandler2, $currentRequestHandler);
    }
}

# Constanze standard request handler

[![GitHub license](https://img.shields.io/github/license/alienwow/SnowLeopard.svg)](https://github.com/alienwow/SnowLeopard/blob/master/LICENSE)
[![LICENSE](https://img.shields.io/badge/license-Anti%20996-blue.svg)](https://github.com/996icu/996.ICU/blob/master/LICENSE)
[![Coverage 100%](https://img.shields.io/azure-devops/coverage/swellaby/opensource/25.svg)](https://github.com/speed-sonic/beige-route)

## PSR-15 请求处理程序
The PSR-15 request handler.

## 简介
这时是一个遵循 PSR-15 标准的请求与中间件处理器，用于构建中间件应用程序，并派发请求。

## 安装
> composer require constanze-standard/request-handler

## 开始使用
Request handler 需要和 PSR-7 标准结合使用，我们的示例使用 `guzzlehttp/psr7`, 你可能需要首先安装它，当然，你也可以选择使用自己熟悉的 `psr/http-message` 实现方案。

首先需要定义一个 `PSR-15 RequestHandler` 作为应用程序的核心初始化派发器 `Dispatcher`。
```php
use ConstanzeStandard\RequestHandler\Dispatcher;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CoreHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], json_encode(
            $request->getAttribute('order')
        ));
    }
}

$dispatcher = new Dispatcher(new CoreHandler());
```

现在我们可以向派发器添加中间件了，中间件必须实现 `psr/http-server-middleware` 提供的 `\Psr\Http\Server\MiddlewareInterface`.
```php
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware implements MiddlewareInterface
{
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $order = $request->getAttribute('order');
        array_push($order, 'middleware' . $this->order);
        $request = $request->withAttribute('order', $order);
        return $handler->handle($request);
    }
}
```

调用 `addMiddleware` 方法，将中间件按照从内向外的顺序添加到派发器中。
```php
$dispatcher->addMiddleware(new Middleware(1));
$dispatcher->addMiddleware(new Middleware(2));
```
DIspatcher 在内部构建了一个栈结构的中间件应用，当添加一个中间件时，相当于向栈的外层做了 push 操作。
```
*---------------------*
|     middleware2     |
|  *---------------*  |
|  |  middleware1  |  |
|  |   *-------*   |  |
|  |   |       |   |  |    栈结构的中间件  
|  |   | core  |   |  |
|  |   |       |   |  |
|  |   *-------*   |  |
|  *---------------*  |
*---------------------*
```

之后，我们触发 request handler, 通过 `Dispatcher::handle` 方法，自动的由栈的外层向内层依次调用，最终得到核心 handler 所返回的 response 对象。
```php
use GuzzleHttp\Psr7\ServerRequest;

$request = new ServerRequest('GET', '/user');
$request->withAttribute('order', []);
$response = $dispatcher->handle($request);

echo $response->getBody()->getContents();
// 输出 ["middleware2","middleware1"]
```

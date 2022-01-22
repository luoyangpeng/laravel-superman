<?php

namespace Itas\LaravelSuperman\Handlers;

use Illuminate\Http\Request;

class WorkermanHandler
{
    public function onWorkerStart($worker)
    {
        require base_path().'/vendor/autoload.php';
        require_once base_path().'/bootstrap/app.php';
    }
 
    // 处理客户端连接
    public function onConnect($connection)
    {
        echo "new connection from ip " . $connection->getRemoteIp() . "\n";
    }
 
    // 处理客户端消息
    public function onMessage($connection, $workermanRequest)
    {
        $files = $workermanRequest->file();
        $files = is_array($files) ? array_filter($files) : $files;

        $server = $_SERVER;
        $server['REQUEST_URI'] = $workermanRequest->uri();
        $server['DOCUMENT_URI'] = $workermanRequest->path();
        $server['SCRIPT_NAME'] = $workermanRequest->path();
        $server['QUERY_STRING'] = $workermanRequest->queryString();
        $server['REQUEST_METHOD'] = $workermanRequest->method();
        $server['REMOTE_ADDR'] = $connection->getRemoteIp();

        $headers = $workermanRequest->header();
        
        foreach ($headers as $k => $v) {
            $server['HTTP_' . str_replace('-', '_', strtoupper($k))] = $v;
        }

        // 加载laravel请求核心模块
        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = \Illuminate\Http\Request::createFromBase(new Request(
                $workermanRequest->get(),
                $workermanRequest->post(),
                [],
                $workermanRequest->cookie(),
                $files,
                $server,
                $workermanRequest->rawBody()
            ))
        );
        
        $kernel->terminate($request, $response);

        $connection->send($response->getContent());
    }
 
    // 处理客户端断开
    public function onClose($connection)
    {
        echo "connection closed from ip {$connection->getRemoteIp()}\n";
    }
}
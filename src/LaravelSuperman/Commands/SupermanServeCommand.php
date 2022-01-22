<?php

namespace Itas\LaravelSuperman\Commands;

use Workerman\Worker;
use Illuminate\Console\Command;
use Workerman\Connection\TcpConnection;

class SupermanServeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature  = 'superman:serve {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Superman serve the application';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->line("<info>server started:</info> <{$this->getListen()}>");
        $action = $this->argument('action') ?? 'start';

        switch ($action) {
            case 'start':
                $this->start();
                break;
            case 'stop':
                break;
            case 'restart':
                break;
            case 'reload':
                break;
            case 'status':
                break;
            case 'connections':
                break;
        }
    }

    protected function getListen()
    {
        return config('superman.listen');
    }

    /**
     * Get the full server command.
     *
     * @return string
     */
    protected function start()
    {
        Worker::$onMasterReload = function (){
            if (function_exists('opcache_get_status')) {
                if ($status = opcache_get_status()) {
                    if (isset($status['scripts']) && $scripts = $status['scripts']) {
                        foreach (array_keys($scripts) as $file) {
                            opcache_invalidate($file, true);
                        }
                    }
                }
            }
        };
        
        $config                               = config('superman');
        Worker::$pidFile                      = $config['pid_file'];
        Worker::$stdoutFile                   = $config['stdout_file'];
        Worker::$logFile                      = $config['log_file'];
        TcpConnection::$defaultMaxPackageSize = $config['max_package_size'] ?? 10*1024*1024;
        
        $worker = new Worker($config['listen'], $config['context']);

        $propertys = [
            'name',
            'count',
            'user',
            'group',
            'reusePort',
            'transport',
        ];

        foreach ($propertys as $property) {
            if (isset($config[$property])) {
                $worker->$property = $config[$property];
            }
        }

        $handler = \App::make('Itas\LaravelSuperman\Handlers\WorkermanHandler');
        // 连接时回调
        $worker->onConnect = [$handler, 'onConnect'];
        // 收到客户端信息时回调
        $worker->onMessage = [$handler, 'onMessage'];
        // 进程启动后的回调
        $worker->onWorkerStart = [$handler, 'onWorkerStart'];
        // 断开时触发的回调
        $worker->onClose = [$handler, 'onClose'];

        Worker::runAll();
    }
}
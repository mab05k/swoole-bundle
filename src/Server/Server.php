<?php

declare(strict_types=1);

namespace K911\Swoole\Server;

use Assert\Assertion;
use K911\Swoole\Server\Config\EventsCallbacks;
use K911\Swoole\Server\Config\Listeners;
use K911\Swoole\Server\Exception\NotRunningException;
use K911\Swoole\Server\Exception\UnexpectedPortException;
use Swoole\Process;
use Throwable;

final class Server
{
    /**
     * @var \Swoole\Http\Server|\Swoole\Server|\Swoole\WebSocket\Server
     */
    private $swooleServer;
    private $configuration;
    private $runningForeground;
    private $signalTerminate;
    private $signalReload;
    private $listeners;
    private $callbacks;

    /**
     * @param \Swoole\Http\Server|\Swoole\Server|\Swoole\WebSocket\Server $swooleServer
     */
    public function __construct($swooleServer, Listeners $listeners, HttpServerConfiguration $configuration, EventsCallbacks $callbacks, bool $running = false)
    {
        Assertion::inArray(\get_class($swooleServer), [\Swoole\Server::class, \Swoole\Http\Server::class, \Swoole\WebSocket\Server::class]);

        $defaultSocket = $configuration->getServerSocket();
        if ($defaultSocket->port() !== $swooleServer->port) {
            throw UnexpectedPortException::with($swooleServer->port, $defaultSocket->port());
        }

        $this->swooleServer = $swooleServer;
        $this->configuration = $configuration;
        $this->runningForeground = $running;
        $this->listeners = $listeners;
        $this->callbacks = $callbacks;

        $this->signalTerminate = \defined('SIGTERM') ? (int) \constant('SIGTERM') : 15;
        $this->signalReload = \defined('SIGUSR1') ? (int) \constant('SIGUSR1') : 10;
    }

    public function start(): bool
    {
        return $this->runningForeground = $this->swooleServer->start();
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws NotRunningException
     */
    public function shutdown(): void
    {
        if ($this->runningForeground) {
            $this->swooleServer->shutdown();

            return;
        }

        if ($this->runningBackground()) {
            Process::kill($this->configuration->getPid(), $this->signalTerminate);

            return;
        }

        throw NotRunningException::make();
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws NotRunningException
     */
    public function reload(): void
    {
        if ($this->runningForeground) {
            $this->swooleServer->reload();

            return;
        }

        if ($this->runningBackground()) {
            Process::kill($this->configuration->getPid(), $this->signalReload);

            return;
        }

        throw NotRunningException::make();
    }

    public function metrics(): array
    {
        if (!$this->runningForeground) {
            throw NotRunningException::make();
        }

        return $this->swooleServer->stats();
    }

    public function dispatchTask($data): void
    {
        if (!$this->runningForeground) {
            throw NotRunningException::make();
        }

        $this->swooleServer->task($data);
    }

    public function running(): bool
    {
        return $this->runningForeground || $this->runningBackground();
    }

    private function runningBackground(): bool
    {
        try {
            return Process::kill($this->configuration->getPid(), 0);
        } catch (Throwable $ex) {
            return false;
        }
    }
}

<?php

declare(strict_types=1);

namespace Woodynew\Hyperf\ThriftClient;

class ClientFactory
{
    public function create(string $host, int $port, bool $autoClose = true): Client
    {
        $client = make(Client::class, ['host' => $host, 'port' => $port]);
        if ($autoClose) {
            defer(function () use ($client) {
                $client->close();
            });
        }
        return $client;
    }
}

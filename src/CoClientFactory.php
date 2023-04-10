<?php

declare(strict_types=1);

namespace Woodynew\Hyperf\ThriftClient;

use Hyperf\Protocol\Packer\SerializePacker;

class CoClientFactory
{
    public function create(string $host, int $port, bool $autoClose = true): CoClient
    {
        $client = make(CoClient::class, ['host' => $host, 'port' => $port, 'packer' => new SerializePacker()]);
        if ($autoClose) {
            defer(function () use ($client) {
                $client->close();
            });
        }
        return $client;
    }
}

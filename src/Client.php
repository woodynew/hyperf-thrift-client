<?php

declare(strict_types=1);

namespace Woodynew\Hyperf\ThriftClient;

use Thrift\Transport\TTransport;
use Thrift\Exception\TTransportException;
use Swoole\Client as SwooleSocketClient;

/**
 * Swoole同步阻塞客户端
 */
class Client extends TTransport
{
    /**
     * @var string 连接地址
     */
    protected string $host;
    /**
     * @var int 连接端口
     */
    protected int $port;

    private ?SwooleSocketClient $socketClient = null;

    /**
     * ClientTransport constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function getSocketClient(): SwooleSocketClient
    {
        if (!$this->socketClient instanceof SwooleSocketClient) {
            $this->socketClient = $this->buildSocketClient();
        }
        return $this->socketClient;
    }

    /**
     * Whether this transport is open.
     *
     * @return boolean true if open
     */
    public function isOpen(): bool
    {
        return $this->getSocketClient()->isConnected();
        return $this->getSocketClient()->sock > 0;
    }

    /**
     * Open the transport for reading/writing
     *
     * @throws TTransportException if cannot open
     */
    public function open()
    {
        if ($this->isOpen()) {
            throw new TTransportException('ClientTransport already open.', TTransportException::ALREADY_OPEN);
        }

        if (!$this->getSocketClient()->connect($this->host, $this->port)) {
            throw new TTransportException(
                'ClientTransport could not open:' . $this->getSocketClient()->errCode,
                TTransportException::UNKNOWN
            );
        }
    }

    /**
     * Close the transport.
     * @throws TTransportException
     */
    public function close()
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        $this->getSocketClient()->close();
    }

    /**
     * Read some data into the array.
     *
     * @param int $len How much to read
     * @return string The data that has been read
     * @throws TTransportException if cannot read any more data
     */
    public function read($len): string
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        return $this->getSocketClient()->recv($len);
    }

    /**
     * Writes the given data out.
     *
     * @param string $buf The data to write
     * @throws TTransportException if writing fails
     */
    public function write($buf)
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        $this->getSocketClient()->send($buf);
    }

    private function buildSocketClient(): SwooleSocketClient
    {
        $socketClient = new SwooleSocketClient(SWOOLE_SOCK_TCP);
        return $socketClient;
    }
}

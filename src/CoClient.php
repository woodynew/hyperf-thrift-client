<?php

declare(strict_types=1);

namespace Woodynew\Hyperf\ThriftClient;

use Hyperf\Protocol\ProtocolPackerInterface;
use Thrift\Transport\TTransport;
use Thrift\Exception\TTransportException;
use Swoole\Coroutine\Socket as SwooleSocketClient;

/**
 * Swoole同步阻塞客户端
 */
class CoClient extends TTransport
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
    public function __construct(string $host, int $port, protected ProtocolPackerInterface $packer)
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
        return $this->getSocketClient()->checkLiveness();
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
        return $this->getSocketClient()->recvStream();
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

        $string = $this->packer->pack($data);

        $length = $this->getSocketClient()->sendAll($string);

        if ($length !== strlen($string)) {
            throw new TTransportException('Send failed: ' . $this->getSocketClient()->errMsg);
        }
    }

    private function buildSocketClient(): SwooleSocketClient
    {
        $socketClient = new SwooleSocketClient(AF_INET, SOCK_STREAM, 0);
        return $socketClient;
    }

    protected function recvStream(float $timeout = -1)
    {
        $head = $this->getSocketClient()->recvAll($this->packer::HEAD_LENGTH, $timeout);
        if ($head === false) {
            return false;
        }

        if (strlen($head) !== $this->packer::HEAD_LENGTH) {
            throw new TTransportException('Receive head failed: ' . $this->socket->errMsg);
        }

        $length = $this->packer->length($head);

        if ($length === 0) {
            throw new TTransportException('Recv body failed: body length is zero.');
        }

        $body = $this->getSocketClient()->recvAll($length, $timeout);
        if ($length !== strlen($body)) {
            throw new TTransportException('Receive body failed: ' . $this->socket->errMsg);
        }

        return $this->packer->unpack($head . $body);
    }
}

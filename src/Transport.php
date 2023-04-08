<?php
/**
 * @author xialeistudio
 */

namespace Woodynew\Hyperf\ThriftClient;

use Thrift\Transport\TTransport;
use Thrift\Exception\TTransportException;
use Swoole\Coroutine\Socket as SwooleSocketClient;
use function Swoole\Coroutine\run;

/**
 * Swoole同步阻塞客户端
 * Class SwooleTransport
 * @package thrift\transport
 */
class Transport extends TTransport
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
        if (! $this->socketClient instanceof SwooleSocketClient) {
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
        return $this->socketClient->socket > 0;
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
        if (!$this->socketClient->connect($this->host, $this->port)) {
            throw new TTransportException('ClientTransport could not open:' . $this->socketClient->errCode,
                TTransportException::UNKNOWN);
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
        $this->connect->close();
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
        return $this->connect->recv($len, 5);
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
        $this->connect->send($buf, 5);
    }

    private function buildSocketClient(): SwooleSocketClient
    {
        $socketClient = new SwooleSocketClient(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        $retval = $socketClient->connect($this->host, $this->port);
        return $socketClient;
    }
}
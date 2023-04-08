# hyperf-thrift-client
基于hyperf非堵塞协程的thrift客户端。

基于thrift标准接口编程，使用方法与原生thrift一致。

## version

+ swoole 4.x
+ thrift 0.18

## 使用方法

```bash
composer require woodynew/hyperf-thrift-client
```

## 示例代码
 
 + Client
    
    ```php
    <?php
    /**
     * thrift客户端，共用一个连接
     * @author xialeistudio
     * @date 2019-05-16
     */
    
    use swoole\foundation\thrift\client\Transport;
    use tests\services\SumService\SumServiceClient;
    use Thrift\Protocol\TBinaryProtocol;
    use Thrift\Transport\TFramedTransport;
    
    require __DIR__ . '/../vendor/autoload.php';
    
    $transport = new Transport('localhost', 9501);
    $transport = new TFramedTransport($transport);
    $protocol = new TBinaryProtocol($transport);
    
    $client = new SumServiceClient($protocol);
    
    $transport->open();
    
    $max = $min = $count = $total = 0;
    
    for ($i = 0; $i < 10000; $i++) {
        $start = microtime(true);
        $client->sum(1, 1);
        $duration = microtime(true) - $start;
        $max = max($duration, $max);
        $min = $min === 0 ? $duration : min($duration, $min);
        $count++;
        $total += $duration;
    }
    
    printf("max: %fs\nmin: %fs\navg: %fs\ncall count: %d\ntotal time: %fs\n", $max, $min, $total / $count, $count, $total);
    ```
    
## 压力测试
 
+ 4核i5
+ 8G内存

 ```text
max: 0.001053s
min: 0.000084s
avg: 0.000094s
call count: 10000
total time: 0.936655s
```
# Guzzle多线程示例

```
$url_arr = [
            'https://caixuanplant.1688.com',
            'https://shop1406652834510.1688.com/',
            'https://abitokyo.1688.com',
            'https://shop1483462719108.1688.com',
            'https://suzhijia.1688.com',
            'https://snidols.1688.com',
        ];

$client = new Client(['verify' => false]);

$requests = function ($url_arr) {
    foreach ($url_arr as $url)
    {
        yield new GuzzleRequest('GET', $url);
    }
};

$pool = new Pool($client, $requests($url_arr), [
    'concurrency' => 10,
    'fulfilled' => function ($response, $index) {
        $content = $response->getBody()->getContents();
        preg_match('#<title>(.*?)</title>#is', $content, $matches);
        $title = iconv('GBK', 'UTF-8', $matches[1]);
        var_dump($index);
        var_dump($title);
    },
    'rejected' => function ($reason, $index) {
        // this is delivered each failed request
    },
]);

$promise = $pool->promise();

$promise->wait();
```

```php    
    $curl = new CurlMulti();
    foreach ($requests as &$request) {
        $curl->addTask(new CurlTask(
                        array(
                            CURLOPT_USERAGENT => self::USERAGENT,
                            CURLOPT_FOLLOWLOCATION => 1,
                            CURLOPT_CONNECTTIMEOUT => 5,
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_HTTPHEADER => array(
                                'Expect:',
                            ),
                            CURLOPT_COOKIEFILE => $request->cookieFile,
                            CURLOPT_COOKIEJAR => $request->cookieFile,
                            CURLOPT_URL => $url,
                        ),
                        function ($page, $info) use (&$request) {
                            if ($info ['http_code'] == 200) {
                                $request->pageLoaded = true;
                                $request->page = $page;
                            } else {
                                var_dump($info);
                            }
                        }
        ));
    }
$curlPage->run();
```

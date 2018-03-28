# 百度LBS云 PHP SDK

目前只简单实现了`Web Api`中的`地理编码服务`,后续会实现更多接口。

示例代码：

```php
$ak = 'your-ak';
$sk = 'your-sk';

$baidu = new \JiaLeo\Baidu\LBSYun\WebApi($ak, $sk);

//geoconv  坐标转换
$result = $baidu->geoconv(1, 5, '113.35025277778,23.254811111111');

//逆地理编码
$result1 = $baidu->geocoder($result['result'][0]['y'] . ',' . $result['result'][0]['x']);

var_dump($result, $result1);
```


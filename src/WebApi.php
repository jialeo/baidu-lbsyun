<?php
namespace JiaLeo\Baidu\LBSYun;


class WebApi
{

    private $ak;
    private $sk;

    public $errorCode = 0;
    public $errorMsg = 'ok';

    public function __construct($ak, $sk)
    {
        $this->ak = $ak;
        $this->sk = $sk;
    }

    /**
     * 逆地理编码
     * @param $location
     * @param int $pois
     * @param string $coordtype
     * @param array $extra
     * @return bool|mixed
     */
    public function geocoder($location, $pois = 1, $coordtype = '', $extra = array())
    {

        $url = 'http://api.map.baidu.com/geocoder/v2/?';
        $uri = '/geocoder/v2/';
        $query_data = array(
            'location' => $location,
            'output' => 'json',
            'pois' => $pois,
            'ak' => $this->ak,
        );

        if (!empty($coordtype)) {
            $query_data['coordtype'] = $coordtype;
        }

        if (!empty($extra)) {
            $query_data = array_merge($query_data, $extra);
        }

        $url .= http_build_query($query_data);
        $sn = $this->caculateAKSN($this->sk, $uri, $query_data);
        $url = $url . '&sn=' . $sn;
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['status'];
                $this->errMsg = empty($json['message'] ? '' : $json['message']);
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 坐标转换
     * @param int $from
     * @param int $to
     * @return bool|mixed
     */
    public function geoconv($from, $to, $location)
    {

        $url = 'http://api.map.baidu.com/geoconv/v1/?';
        $uri = '/geoconv/v1/';
        $query_data = array(
            'coords' => $location,
            'from' => $from,
            'to' => $to,
            'output' => 'json',
            'ak' => $this->ak,
        );

        $url .= http_build_query($query_data);
        $sn = $this->caculateAKSN($this->sk, $uri, $query_data);
        $url = $url . '&sn=' . $sn;
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['status'];
                $this->errMsg = empty($json['message'] ? '' : $json['message']);
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 计算sn
     * @param $sk
     * @param $url
     * @param $querystring_arrays
     * @param string $method
     * @return string
     */
    protected function caculateAKSN($sk, $url, $querystring_arrays, $method = 'GET')
    {
        if ($method === 'POST') {
            ksort($querystring_arrays);
        }
        $querystring = http_build_query($querystring_arrays);
        return md5(urlencode($url . '?' . $querystring . $sk));
    }

    /**
     * @param $url
     * @param int $timeout
     * @return bool|mixed
     */
    protected function http_get($url, $timeout = 0)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);

        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        curl_setopt($oCurl, CURLOPT_USERAGENT, $user_agent);

        if (!empty($timeout)) {
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeout);   //秒
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }


}
<?php

namespace CSUInformation;

use CSUInformation\Exception\CurlException;
use CSUInformation\Exception\LoginException;
use CSUInformation\Exception\SessionException;
use CSUInformation\uitls\PasswordManager;

/**
* 需要登陆的网站抓取基类
*/
abstract class BaseLoginWebsite
{
    const JSP_PATTERN = '/JSESSIONID=(.+?);/';
    const ASP_PATTERN = '/NET_SessionId=(.+?);/';
    protected $passwordManager;

    function __construct(PasswordManager $passwordManager = NULL) {
        if($passwordManager) {
            $this->passwordManager = $passwordManager;
            $this->login($this->getUser(), $this->getPassword());
        }
    }

    function login($user, $password) {}

    protected function getUser() {}

    protected function getPassword() {}
    
    /**
     * 登陆获取session
     * @param  string $url      请求地址
     * @param  string $postdata post数据
     * @param  string $pattern  session获取正则
     * @param  array  $infoGet  登陆失败信息获取
     *     array('judge' => 存在字符串则登陆失败,
     *     'pattern' => 登陆失败具体信息正则模式,
     *     'reverse' => true是反向判断，不存在则登陆失败，false正向判断)
     * @param  string $encode   网页编码方式
     * @param  array  $header   额外的HTTP头
     * @return string           session字符串
     */
    protected function loginGetSession($url, $postdata, $pattern,
            $infoGet, $encode="UTF-8", $header=NULL) {
        $curl = curl_init ();
        curl_setopt_array ( $curl, array (
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_TIMEOUT => 4
        ) );
        if($header)
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close ( $curl );
        if($errno)
            throw new CurlException($errno, $error);

        // 转码
        if($encode != 'UTF-8')
            $result = mb_convert_encoding($result, 'UTF-8', $encode);

        // 判断登陆是否成功
        if(isset($infoGet['reverse']) && $infoGet['reverse'])
            $loginFailed = strpos($result, $infoGet['judge']) === false;
        else
            $loginFailed = strpos($result, $infoGet['judge']) > 0;
        if($loginFailed) {
            // 登陆失败
            if(isset($infoGet['pattern']) 
                    && preg_match($infoGet['pattern'], $result, $loginError))
                throw new LoginException($loginError[1]);
            else
                throw new LoginException('登陆失败');
        }
        // 匹配session字符串
        if(! preg_match($pattern, $result, $session))
            throw new SessionException();
        return $session[1];
    }
}
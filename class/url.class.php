<?php
/*
* php class for post or get query.
* @version: 1.1,
* @author: Bogdan Karpov,
* @email: php_pro@ukr.net,
* @date: 24.11.2017 14:47
*/
	class url{
		private $url;
		private $type;
		public $info;
		private $proxy;
		public $ssl;
		private $headers = '';
		private $cookies = '';
		public $err = '';
		public $agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36';
		public function __construct($url, $proxy = array() ){
			if (!function_exists('curl_init')){
				throw new Exception('Недоступний модуль cUrl!',2);
			}
			$this->url = $url;
			$this->proxy = $proxy;
			$this->checkURL();
			$this->ssl = $this->is_ssl();
		}
		private function is_ssl(){
			$this->url = trim($this->url);
			if ((strpos($this->url, 'https') !== false)){
				return true;
			}
				return false;
			
		}
		private function checkURL(){
			if (!(preg_match('#^(http|https)\:\/\/[\w\d\.\-\=\&\?\_\/].*#i', $this->url) !== false) )
				throw new Exception("Некоректний url для запиту!",3);
		}
		public function curl($par){
			$c = curl_init();
			curl_setopt_array($c, $par);
			$res = curl_exec($c);
			$this->info = curl_getinfo($c);
			$this->error = curl_error($c);
			curl_close($c);
			return $res;
		}
		public function get($par = ''){
			$url = $this->url;
			if (!empty($par))
				$url .= '?'.urldecode(http_build_query($par));
			$pr = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => 1
			);
			$pr = $this->smart_setopt($pr);
			return $this->curl($pr);
		}
		public function post($par = ''){
			if (!empty($par)){
				if(is_array($par)) $q = http_build_query($par);
				elseif(is_string($par)) $q = $par;
				else throw new Exception("Некоректний параметр пост запиту!!",4);
			}else throw new Exception("Некоректний параметр пост запиту!!",4);
			$pr = array(
				CURLOPT_URL => $this->url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $q
				);
			$pr = $this->smart_setopt($pr);
			return $this->curl($pr);
		}
		private function smart_setopt($pr){
			$pr[CURLOPT_HEADER] = (!empty($this->header)) ? $this->header : false;
			if (is_array($this->cookies) and !empty($this->cookies)){
				$pr[CURLOPT_COOKIE] = http_build_cookie($this->cookies);
			}
			if (!empty($this->proxy)){
				$pr[CURLOPT_PROXY] = $this->proxy[0].':'.$this->proxy[1];
				if (isset($this->proxy[2])) {
					$pr[CURLOPT_PROXYUSERPWD] = $this->proxy[2].':'.$this->proxy[3];
				}
			}
			if ($this->ssl){
				$pr[CURLOPT_SSL_VERIFYPEER] = false;
    			//$pr[CURLOPT_SSL_VERIFYHOST]= 2;

			}
			$pr[CURLOPT_USERAGENT] = $this->agent;
			$pr[CURLOPT_FOLLOWLOCATION] = 1;
			$pr[CURLOPT_REFERER] = 'google.com';
			return $pr;
		}
		public function set_agent($value='')
		{
			$this->agent = $value;
		}
		public function set_headers($str){
			$this->headers = $str;
		}
		public function set_cookies($arr){
			$this->cookies = $arr;
		}
		public function get_header($pot){
			$pot = trim( (string) $pot );
			if (preg_match('#^(.*?)\n\n#si', $pot, $arr) !== false ) {
				return $arr[1];
			}else{
				return false;
			}
		}
		public function get_agent()
		{
			return $this->agent;
		}
		public function __destruct(){
			if (!empty($this->error)){
				throw new Exception($this->error);
			}
		}
	}

<?php
class Spider {
	var $ch;
	var $postData;
	var $getData;
	var $reffer;
	var $infoExec;
	
	function __construct() {
		$this->postData = array();
		$this->getData = array();
		$this->reffer = '';
		$this->infoExec = NULL;
		
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookies.txt'); 
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookies.txt');
		curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; FloatingPointBot/0.2; +http://www.pontoflutuante.com.br/bots; pt-BR)');
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;application/jsonq=0.8,image/png,*/*;q=0.5",
														"Cache-Control: max-age=0",
														"Connection: keep-alive",
														"Keep-Alive: 300",
														"Accept-Charset: UTF-8,ISO-8859-1;q=0.7,*;q=0.7",
														"Accept-Language: pt-BR,pt;q=0.9, en-us,en;q=0.5",
														"Pragma: "));
		
	}
	
	function debug($enable = 1) {
		curl_setopt($this->ch, CURLOPT_HEADER, $enable);
		curl_setopt($this->ch, CURLOPT_VERBOSE, $enable);
	}
	
	function desableSSLVerify(){
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	
	function doGet($url, $data = array()) {
		$url = $this->mountGetUrl($url, $data);
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		!empty($this->reffer) || curl_setopt($this->ch, CURLOPT_REFERER, $this->reffer);
		
		$r = curl_exec($this->ch);
		
		$this->reffer = $url;
		$this->infoExec = curl_getinfo($this->ch);
		
		return $r;
	}
	
	function doPost($url, $data){
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->mountData($data));

		!empty($this->reffer) || curl_setopt($this->ch, CURLOPT_REFERER, $this->reffer);
		
		$r = curl_exec($this->ch);
		
		$this->reffer = $url;
		$this->infoExec = curl_getinfo($this->ch);
		
		curl_setopt($this->ch, CURLOPT_POST, 0);
				
		return $r;
	}
	
	function download($url) {
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, 1);
		!empty($this->reffer) || curl_setopt($this->ch, CURLOPT_REFERER, $this->reffer);
		
		$r = curl_exec($this->ch);
		
		$this->reffer = $url;
		$this->infoExec = curl_getinfo($this->ch);
		
		return $r;
	}
	
	function doAuthentication($url, $user, $password) {
		curl_setopt($this->ch, CURLOPT_URL, $url); 
		curl_setopt($this->ch, CURLOPT_USERPWD, sprintf('%s:%s', $user, $password));
		
		$r = curl_exec($this->ch);
		
		$this->reffer = $url;
		$this->infoExec = curl_getinfo($this->ch);
		
		return $r;
	}
	
	function mountGetUrl($url, $data) {
		if (!preg_match('/^(https?|ftp):\/\//', $url)) {
			$url = 'http://'.$url;
		}
		
		if (strpos($url, '?') === -1) {
			$url .= '?';
		}
		
		$url .= $this->mountData($data);
		
		return $url;
	}
	
	function mountData($data) {
		$url = array();

		foreach ($data as $name => $value) {
			array_push($url, sprintf('%s=%s', $name, $value));
		}

		return implode('&', $url);
	}
	
	function _destruct() {
		curl_close($this->ch);
	}
}

?>

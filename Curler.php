<?php

namespace Tamarillo\Weather;

/**
 * Class Curler
 * @package Tamarillo\Weather
 * @author Tobias Maxham
 */
class Curler {

	public function fetch($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout());
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

	private function timeout()
	{
		$timeout = getenv('FETCHER_TIMEOUT');
		return $timeout?$timeout:10;
	}

} 
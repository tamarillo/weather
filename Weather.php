<?php

namespace Tamarillo\Weather;

use Illuminate\Support\Facades\Cache;

/**
 * Class Weather
 * @package Tamarillo\Weather
 */
class Weather
{

	private static $lat, $lon, $id, $name, $url, $data;
	private static $apiUrl = 'http://api.openweathermap.org/data/2.5/forecast';
	private $format;
	private $defaultformat = '%oneLine%';


	/**
	 * @var string $dailyForecast API URL for daily forecast data.
	 */
	private $dailyForecast = "http://api.openweathermap.org/data/2.5/forecast/daily?";

	public static function getWeather($search)
	{
		if (is_array($search)) list(self::$lat, self::$lon) = $search;
		elseif (is_int($search)) self::$id = $search;
		else self::$name = $search;

		return self::handleRequest();
	}

	/**
	 * @return \Tamarillo\Weather\Weather
	 */
	private static function handleRequest()
	{
		self::buildUrl();

		if (!Cache::has('weather')) {
			echo 'request';
			self::$data = file_get_contents(self::$url);
			Cache::add('weather', self::$data, 1440);
		} else self::$data = Cache::get('weather');

		$last = date('mdy', json_decode(self::$data)->list[0]->dt);
		if (date('mdy') != $last) self::$data = file_get_contents(self::$url);

		$weather = new Weather(self::$data);

		self::$name = self::$id = self::$lat = self::$lon =
		self::$data = self::$url = NULL;

		return $weather;
	}

	private static function buildUrl()
	{
		self::$url = self::$apiUrl;
		if (self::$lat) $param = array('lat' => self::$lat, 'lon' => self::$lon);
		elseif (self::$name) $param = array('q' => self::$name);
		else {
			self::$url .= '/city';
			$param = array('id' => self::$id);
		}

		$param = array_add($param, 'APPID', self::apiKey());
		$param = array_add($param, 'lang', 'de');
		$param = array_add($param, 'units', 'metric');
		$param = array_add($param, 'cnt', 7);

		self::$url .= '?' . http_build_query($param);
	}

	private static function apiKey()
	{
		return '8bf3d1154e15c3edf7ff892291deb93a';
		//return \Config::get('admin.weather.key');
	}

	public function __toString()
	{
		if (is_null($this->format)) $this->setFormat($this->defaultformat);
		return $this->makeOutput();
	}

	public function setFormat($format)
	{
		$this->format = $format;
	}

	private function makeOutput()
	{
		return '';
	}

	public function next()
	{

	}

	public function prev()
	{

	}

	private function onlyMax()
	{
		return \Config::get('admin.weather.temp') == 'max';
	}


}

<?php

namespace Tamarillo\Weather;

/**
 * Class Weather
 * @package Tamarillo\Weather
 *
 * @author Tobias Maxham
 */
class Weather
{
	/**
	 * The Copyright http://openweathermap.org/copyright.
	 */
	const COPYRIGHT = "Weather data from <a href=\"http://www.openweathermap.org\">OpenWeatherMap.org</a>";

	private $lat, $lon, $id, $name, $url, $data;
	private $format;
	private $defaultformat = '%oneLine%';

	private $fetchHandler;


	/**
	 * @var string $dailyForecast API URL for daily forecast data.
	 */
	private $dailyForecast = "http://api.openweathermap.org/data/2.5/forecast/daily?";

	/**
	 * @var string $weeklyForecast API URL for weekly forecast data.
	 */
	private $weeklyForecast = 'http://api.openweathermap.org/data/2.5/forecast';

	public function __construct() {
		$this->fetchHandler = new Fetcher();
	}


	public static function getWeather($search, $unit = 'metric', $nl = 'de', $appid = '')
	{
		$weather = new self;
		return $weather->getWeatherData($search, $unit, $nl, $appid);
	}

	public function getWeatherData($search, $unit = 'metric', $nl = 'de', $appid = '')
	{
		$url = $this->buildRequestURL($search, $unit, $nl, $appid);
		return $this->fetchWithCacheFrom($url);
	}

	private function buildRequestURL($search, $unit, $nl, $appid)
	{
		if (is_array($search))
		{
			list($lat, $lon) = $search;
			$param = ['lat' => $lat, 'lon' => $lon];
		}
		elseif (is_int($search)) {
			$this->id = $search;

			$urlAdd = '/city';
			$param = ['id' => $search];
		}
		else {
			$param = ['q' => $search];
		}

		if(isset($urlAdd)) $url = $this->weeklyForecast . $urlAdd;
		else $url = $this->weeklyForecast;

		if(empty($appid)) $appid = $this->apiKey();
		if(!empty($appid)) $param = array_add($param, 'APPID', $appid);
		$param = array_add($param, 'lang', $nl);
		$param = array_add($param, 'units', $unit);
		$param = array_add($param, 'cnt', 7);

		return $url . '?' . http_build_query($param);
	}

	private function apiKey()
	{
		return getenv('WEATHER_API');
	}

	private function fetchWithCacheFrom($url)
	{
		return $this->fetchHandler->fetch($url);
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
		return getenv('WEATHER_MAX');
	}


}

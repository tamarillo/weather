<?php

namespace Tamarillo\Weather;

/**
 * Class Weather
 * @package Tamarillo\Weather
 * @author Tobias Maxham
 */
class Weather
{
	/**
	 * The Copyright http://openweathermap.org/
	 */
	const COPYRIGHT = "Weather data from <a href=\"http://www.openweathermap.org\">OpenWeatherMap.org</a>";

	private $id, $data;
	private $format;

	/**
	 * @var Formatter $formatter
	 */
	private $formatter;
	private $defaultformat = 'oneLine';

	/**
	 * @var Curler $curler
	 */
	private $curler;

	/**
	 * The current list position.
	 *
	 * @var int $pos
	 */
	private $pos = 0;

	/**
	 * @var string $dailyForecast API URL for daily forecast data.
	 */
	private $dailyForecast = "http://api.openweathermap.org/data/2.5/forecast/daily?";

	/**
	 * @var string $weeklyForecast API URL for weekly forecast data.
	 */
	private $weeklyForecast = 'http://api.openweathermap.org/data/2.5/forecast';

	/**
	 * Init the URL-Curler and the Formatter.
	 */
	public function __construct()
	{
		$this->curler = new Curler();
		$this->formatter = new Formatter($this->data, $this->pos);
	}

	/**
	 * @param array|int|string $search Can be coords, city-id or city-name
	 * @param string $unit metric or imperial
	 * @param string $nl The output language
	 * @param string $appid The OpenWeatherMAP API Key
	 * @return Weather $weather
	 */
	public static function getWeather($search, $unit = 'metric', $nl = 'de', $appid = '')
	{
		$weather = new self;
		$weather->getWeatherData($search, $unit, $nl, $appid);
		return $weather;
	}

	/**
	 * @param array|int|string $search Can be coords, city-id or city-name
	 * @param string $unit metric or imperial
	 * @param string $nl The output language
	 * @param string $appid The OpenWeatherMAP API Key
	 * @return \stdClass $data
	 */
	public function getWeatherData($search, $unit = 'metric', $nl = 'de', $appid = '')
	{
		$url = $this->buildRequestURL($search, $unit, $nl, $appid);
		$this->data = json_decode($this->fetchWithCacheFrom($url));
		return $this->data;
	}

	/**
	 * @param array|int|string $search Can be coords, city-id or city-name
	 * @param string $unit metric or imperial
	 * @param string $nl The output language
	 * @param string $appid The OpenWeatherMAP API Key
	 * @return string $requestURL
	 */
	private function buildRequestURL($search, $unit, $nl, $appid)
	{
		$param = $this->getParamFor($search);
		if (empty($appid)) $appid = $this->apiKey();
		if (!empty($appid)) $param = array_add($param, 'APPID', $appid);
		$param = array_add($param, 'lang', $nl);
		$param = array_add($param, 'units', $unit);
		$param = array_add($param, 'cnt', 7);

		return $this->weeklyForecast . '?' . http_build_query($param);
	}

	/**
	 * @param string $search
	 * @return array $param
	 */
	private function getParamFor($search)
	{
		if (is_array($search)) {
			list($lat, $lon) = $search;
			$param = ['lat' => $lat, 'lon' => $lon];
		} elseif (is_int($search)) {
			$this->id = $search;
			$this->weeklyForecast .= '/city';
			$param = ['id' => $search];
		} else {
			$param = ['q' => $search];
		}
		return $param;
	}

	/**
	 * @return string
	 */
	private function apiKey()
	{
		return getenv('WEATHER_API');
	}

	/**
	 * @param string $url
	 * @return string $jsonString
	 */
	private function fetchWithCacheFrom($url)
	{
		return $this->curler->fetch($url);
	}

	public function geo()
	{
		return $this->data->city->coord;
	}

	/**
	 * @return Weather $this
	 */
	public function icon()
	{
		$this->setFormat('icon');
		return $this;
	}

	/**
	 * @param $format string
	 */
	public function setFormat($format)
	{
		$this->format = "%$format%";
	}

	/**
	 * @return Weather $this
	 */
	public function weather()
	{
		$this->setFormat('weather');
		return $this;
	}

	/**
	 * @return int|null $id
	 */
	public function id()
	{
		if (strpos($this->format, 'city') != FALSE) return $this->data->city->id;
		if (!isset($this->data->list[$this->pos])) return NULL;
		return $this->data->list[$this->pos]->weather[0]->id;
	}

	/**
	 * @return Weather $this
	 */
	public function city()
	{
		$this->setFormat('city');
		return $this;
	}

	/**
	 * @return string $view
	 */
	public function __toString()
	{
		if (is_null($this->format)) $this->setFormat($this->defaultformat);
		return $this->makeOutput();
	}

	/**
	 * @return string $view
	 */
	private function makeOutput()
	{
		return $this->formatter->view($this->format);
	}

	/**
	 * @return Weather $this
	 */
	public function next()
	{
		if (isset($this->getList()[$this->pos + 1])) ;
		$this->pos++;
		return $this;
	}

	/**
	 * @return array $list
	 */
	private function getList()
	{
		return $this->data->list;
	}

	/**
	 * @return Weather $this
	 */
	public function prev()
	{
		if ($this->pos != 0) $this->pos--;
		return $this;
	}

	private function onlyMax()
	{
		return getenv('WEATHER_MAX');
	}

}

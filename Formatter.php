<?php

namespace Tamarillo\Weather;

/**
 * Class Formatter
 * @package Tamarillo\Weather
 * @author Tobias Maxham
 */
class Formatter
{

	private $data, $pos;
	private $icon = 'http://openweathermap.org/img/w/$.png';

	public function __construct(&$data, &$pos)
	{
		$this->data = &$data;
		$this->pos = &$pos;
	}

	/**
	 * @param string $format
	 * @return string $view
	 */
	public function view($format)
	{
		if (strpos($format, 'city') !== FALSE) return $this->city();
		if (strpos($format, 'weather') !== FALSE) return $this->weather();
		if (strpos($format, 'icon') !== FALSE) return $this->icon();
		return '';
	}

	private function weather() {

		$desc = $this->getWeatherInfo()->description;
		$tempMin = round($this->getCurrent()->main->temp_min);
		$tempMax = round($this->getCurrent()->main->temp_max);

		return "$desc; $tempMin / $tempMax °C";
	}

	/**
	 * @return \stdClass $currentInfo
	 */
	private function getCurrent()
	{
		return $this->data->list[$this->pos];
	}

	/**
	 * @return \stdClass $weatherInfo
	 */
	private function getWeatherInfo()
	{
		return $this->getCurrent()->weather[0];
	}

	private function icon()
	{
		$icon = str_replace('$', $this->getWeatherInfo()->icon , $this->icon);
		return '<img src="'.$icon.'" />';
	}

	private function city()
	{
		return $this->data->city->name;
	}

} 
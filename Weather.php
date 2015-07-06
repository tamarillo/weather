<?php

namespace Tamarillo\Weather;

/**
 * Class Weather
 * @package Tamarillo\Weather
 */
class Weather
{
	private $format;
	private $defaultformat = '%oneLine%';

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

	private function apiKey()
	{

	}


}

<?php

class PoaTransporte {

	public static $facade = 'http://www.poatransporte.com.br/php/facades/process.php';

	public static function onibus()
	{
		static $collection;
		if ( ! is_object($collection))
		{
			$collection = new PoaTransporte_Collection('onibus');
		}
		return $collection;
	}

	public static function lotacoes()
	{
		static $collection;
		if ( ! is_object($collection))
		{
			$collection = new PoaTransporte_Collection('lotacoes');
		}
		return $collection;
	}
	
}

class PoaTransporte_Collection implements ArrayAccess, Countable, IteratorAggregate {
	
	private $collection;

	public function __construct($type)
	{
		$data = $this->load_data($type);
		$this->collection = new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS);
	}

	private function load_data($type)
	{
		if ( ! in_array($type, array('onibus', 'lotacoes')))
		{
			throw new Exception('Invalid request');
		}

		$request_uri = PoaTransporte::$facade.'?a=nc&p=%&t='.substr($type, 0, 1);
		$request = file_get_contents($request_uri);
		$data = json_decode($request);

		foreach ($data as $key => $unit)
		{
			$data[$key] = new PoaTransporte_Unit($unit);
		}

		return $data;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset))
		{
			$this->collection[] = $value;
		}
		else
		{
			$this->collection[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->collection[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->collection[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
	}

	public function count() {
		return sizeof($this->collection);
	}

	public function getIterator() {
		return $this->collection->getIterator();
	}

}

class PoaTransporte_Unit {
	
	private $data;
	private $route;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function __get($param)
	{
		return trim($this->data->{$param});
	}
	
	public function route()
	{
		return $this->route ?: $this->load_route();
	}

	private function load_route()
	{
		$request_uri = PoaTransporte::$facade.'?a=il&p='.$this->id;
		$request = file_get_contents($request_uri);
		$route = get_object_vars(json_decode($request));
		unset($route['codigo'], $route['idlinha'], $route['nome']);

		$this->route = $route;
		return $route;
	}

}
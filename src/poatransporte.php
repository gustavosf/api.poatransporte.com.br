<?php

class PoaTransporte {

	/**
	 * Endereço do façade (nosso resource) provido pelo PoaTransporte
	 */
	public static $facade = 'http://www.poatransporte.com.br/php/facades/process.php';

	/**
	 * Método estático que retorna a listagem das linhas de ônibus
	 * @return  PoaTransporte_Collection
	 */
	public static function onibus()
	{
		static $collection;
		if ( ! is_object($collection))
		{
			$collection = new PoaTransporte_Collection('onibus');
		}
		return $collection;
	}

	/**
	 * Método estático que retorna a listagem das linhas de lotação
	 * @return  PoaTransporte_Collection
	 */
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
	
	/**
	 * Armazena a lista de unidades de transporte
	 */
	private $collection;

	/**
	 * Retorna uma coleção de unidades de transporte, carregadas do PoaTransporte
	 */
	public function __construct($type)
	{
		$data = $this->load_data($type);
		$this->collection = new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Carrega os dados do resource
	 * @see     PoaTransporte::$facade
	 * @return  array
	 */
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

	/* Os métodos abaixo são implementações simples de ArrayAccess,
	   Countable e IteratorAggregate */
	   
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
	
	/**
	 * Armazena os dados básicos da unidade de transporte
	 */
	private $data;

	/** 
	 * Armazena os dados da rota. É preenchido por demanda
	 * @see  load_route
	 */
	private $route;

	
	/**
	 * Retorna o objeto que representa uma unidade de transporte.
	 * Contém os dados básicos como nome, código, id e rota
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Método mágico para dar acesso aos dados do objeto
	 * @return  mixed
	 */
	public function __get($param)
	{
		return trim($this->data->{$param});
	}
	
	/**
	 * Pega a rota do ônibus
	 * Retorna um array onde cada elemento é um objeto com um par de atributos, 
	 *   lat (latitude) e lng (longitude)
	 * @return  array
	 */
	public function route()
	{
		return $this->route ?: $this->load_route();
	}

	/**
	 * Função para carregar a rota, por demanda
	 * @return  array
	 */
	private function load_route()
	{
		$request_uri = PoaTransporte::$facade.'?a=il&p='.$this->id;
		$request     = file_get_contents($request_uri);
		$route       = get_object_vars(json_decode($request));
		unset($route['codigo'], $route['idlinha'], $route['nome']);

		$this->route = $route;
		return $route;
	}

}
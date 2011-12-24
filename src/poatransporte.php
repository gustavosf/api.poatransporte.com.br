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
			$data = self::load_data('onibus');
			$collection = new PoaTransporte_Collection($data);
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
			$data = self::load_data('lotacoes');
			$collection = new PoaTransporte_Collection($data);
		}
		return $collection;
	}

	/**
	 * Método estático que retorna a listagem das paradas de ônibus/lotação
	 * @return  PoaTransporte_Collection
	 */
	public static function paradas()
	{
		static $collection;
		if ( ! is_object($collection))
		{
			$data = self::load_data('paradas');
			$collection = new PoaTransporte_Collection($data);
		}
		return $collection;
	}

	/**
	 * Carrega os dados do resource
	 * @see     self::$facade
	 * @return  array
	 */
	private static function load_data($type)
	{
		if ( ! in_array($type, array('onibus', 'lotacoes', 'paradas')))
		{
			throw new Exception('Invalid request');
		}

		$type = substr($type, 0, 1);
		if ($type === 'p')
		{
			$max_coords = '((-30.14296222668432,%20-51.87917968750003),%20(-29.79200328961529,%20-50.56082031250003))))';
			$request_uri = self::$facade.'?a=tp&p='.$max_coords;
		}
		else
		{
			$request_uri = self::$facade.'?a=nc&p=%&t='.$type;
		}
		$request = file_get_contents($request_uri);
		$data = json_decode($request);
		$class = 'PoaTransporte_'.($type === 'p' ? 'Stop' : 'Unit');

		foreach ($data as $key => $unit)
		{
			$data[$key] = new $class($unit);
		}

		return $data;
	}
	
}

class PoaTransporte_Collection implements ArrayAccess, Countable, IteratorAggregate {
	
	/**
	 * Armazena a lista de unidades de transporte
	 */
	public $collection;

	/**
	 * Retorna uma coleção de unidades de transporte
	 */
	public function __construct($data)
	{
		$this->collection = $data;
	}

		/**
	 * Retorna o primeiro elemento da coleção
	 * @return  object
	 */
	public function first()
	{
		return array_shift($this->collection);
	}

	/**
	 * Retorna o último elemento da coleção
	 * @return  object
	 */
	public function last()
	{
		return array_pop($this->collection);
	}

	/**
	 * Inicia a procura de um objeto na coleção, ou retorna o objeto com
	 * o id pesquisado
	 * @param   mixed   usar um tipo básico (string ou int)
	 * @return  object
	 */
	public function find($id = null)
	{
		$this->finder = array();
		if ($id !== null)
		{
			return $this->where('id', '^'.$id.'$')->execute();
		}
		else
		{
			return $this;
		}
	}

	/**
	 * Configura uma pesquisa por campo e valor
	 * Suporta apenas comparação por regex
	 * Para pesquisar por valor exato, utilizar ^VALOR$
	 * @param   string  atributo a ser pesqusiado
	 * @param   string  regex a ser utilizada (sem '/')
	 * @return  PoaTransporte_Collection  $this
	 */
	public function where($field, $value)
	{
		$this->finder[] = (object)array(
			'field' => $field,
			'value' => $value
		);
		return $this;
	}

	/**
	 * Executa a consulta e retorna uma coleção com os objetos encontrados
	 * @return  PoaTransporte_Collection
	 */
	public function execute()
	{
		$return = array();
		foreach ($this->collection as $key => $item)
		{
			$found = true;
			foreach ($this->finder as $where)
			{
				$found = ($found and preg_match('/'.$where->value.'/i', $item->{$where->field}));
			}
			if ($found)
			{
				array_push($return, $item);
			}
		}
		return new PoaTransporte_Collection($return);
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
		return new ArrayIterator($this->collection);
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

class PoaTransporte_Stop {
	
	/**
	 * Armazena os dados básicos da unidade de transporte
	 */
	private $data;

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
		$param = $this->data->{$param};
		return is_string($param) ? trim($param) : $param;
	}
	
}
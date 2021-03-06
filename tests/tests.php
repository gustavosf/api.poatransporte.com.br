<?php

require dirname(__FILE__).'/../src/poatransporte.php';

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	private $buses;
	private $lotacoes;

	protected function setUp()
	{
		parent::setUp();
		$this->buses = PoaTransporte::onibus();
		$this->lotacoes = PoaTransporte::lotacoes();
		$this->stops = PoaTransporte::paradas();
	}

	public function testDataLoad()
	{
		$this->assertEquals(gettype($this->buses), 'object');
		$this->assertEquals(gettype($this->lotacoes), 'object');
	}

	public function testCollections()
	{
		$this->assertEquals(get_class($this->buses), 'PoaTransporte_Collection');
		$this->assertGreaterThan(1, count($this->buses));
		$this->assertEquals(get_class($this->buses[0]), 'PoaTransporte_Unit');

		$this->assertEquals(get_class($this->lotacoes), 'PoaTransporte_Collection');
		$this->assertGreaterThan(1, count($this->lotacoes));
		$this->assertEquals(get_class($this->lotacoes[0]), 'PoaTransporte_Unit');

		$this->assertEquals(get_class($this->stops), 'PoaTransporte_Collection');
		$this->assertGreaterThan(1, count($this->stops));
		$this->assertEquals(get_class($this->stops[0]), 'PoaTransporte_Stop');
	}

	public function testUnitData()
	{
		$bus = $this->buses[0];
		$this->assertNotNull(@$bus->nome);
		$this->assertNotNull(@$bus->codigo);

		$lotacao = $this->lotacoes[0];
		$this->assertNotNull(@$lotacao->nome);
		$this->assertNotNull(@$lotacao->codigo);
	}

	public function testUnitRoute()
	{
		$bus = $this->buses[0];
		$route = $bus->route();
		$this->assertGreaterThan(1, count($route));

		$point = array_pop($route);;
		$this->assertObjectHasAttribute('lat', $route[0]);
		$this->assertObjectHasAttribute('lng', $route[0]);
	}

	public function testUnitStops()
	{
		$stop = $this->stops[0];

		$this->assertNotNull(@$stop->codigo);
		$this->assertNotNull(@$stop->latitude);
		$this->assertNotNull(@$stop->longitude);
		$this->assertNotNull(@$stop->terminal);
		$this->assertNotNull(@$stop->linhas);
		$this->assertInternalType('array', $stop->linhas);
	}

	public function testCollectionExtras()
	{
		$stops = $this->stops;
		$this->assertEquals($stops[0], $stops->first());
		$this->assertEquals($stops[count($stops)-1], $stops->last());
	}

	public function testCollectionFinder()
	{
		$lotacoes = $this->lotacoes;
		$this->assertEquals(1, sizeof($lotacoes->find(114660))); // wenceslau escobar
		
		$wenceslau = $lotacoes->find()->where('codigo', '^10.52.*')->execute();
		$this->assertEquals(2, sizeof($wenceslau));

		$wenceslau_ida = $wenceslau->find()->where('codigo', '\-1$')->execute();
		$this->assertEquals(1, sizeof($wenceslau_ida));
	}
}
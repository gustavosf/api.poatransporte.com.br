<?php

require dirname(__FILE__).'/../src/poatransporte.php';

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	public function testDataLoad()
	{
		$this->buses = PoaTransporte::onibus();
		$this->lotacoes = PoaTransporte::lotacoes();
	}

	public function testCollections()
	{
		$this->assertEquals(get_class($this->buses), 'PoaTransporte_Collection');
		$this->assertGreaterThan(1, count($this->buses));
		$this->assertEquals(get_class($this->buses[0]), 'PoaTransporte_Unit');

		$this->assertEquals(get_class($this->lotacoes), 'PoaTransporte_Collection');
		$this->assertGreaterThan(1, count($this->lotacoes));
		$this->assertEquals(get_class($this->lotacoes[0]), 'PoaTransporte_Unit');
	}

	public function testUnitData()
	{
		$bus   = $this->buses[0];
		$this->assertNotNull(@$bus->nome);
		$this->assertNotNull(@$bus->codigo);

		$lotacao = $this->lotacoes[0];
		$this->assertNotNull(@$lotacao->nome);
		$this->assertNotNull(@$lotacao->codigo);
	}

}
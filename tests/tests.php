<?php

require dirname(__FILE__).'/../src/poatransporte.php';

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	public function testCollections()
	{
		$buses = PoaTransporte::onibus();
		$lotacoes = PoaTransporte::lotacoes();


		$this->assertEquals(get_class($buses), 'PoaTransporte_Collection');
		$this->assertEquals(get_class($lotacoes), 'PoaTransporte_Collection');
		$this->assertGreatherThan(1, count($buses));
		$this->assertGreatherThan(1, count($lotacoes));
		$this->assertEquals(get_class($buses[0]), 'PoaTransporte_Unit');
		$this->assertEquals(get_class($lotacoes[0]), 'PoaTransporte_Unit');
	}

	public function testUnitData()
	{
		$buses = PoaTransporte::onibus();
		$bus = $buses[0];
		$this->assertNotNull(@$bus->nome);
		$this->assertNotNull(@$bus->codigo);

		$buses = PoaTransporte::lotacoes();
		$bus = $buses[0];
		$this->assertNotNull(@$bus->nome);
		$this->assertNotNull(@$bus->codigo);
	}

}
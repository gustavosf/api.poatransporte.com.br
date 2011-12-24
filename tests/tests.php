<?php

require dirname(__FILE__).'/../src/poatransporte.php';

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	public function testBusCollection()
	{
		$buses = PoaTransporte::onibus();
		$this->assertEquals(get_class($buses), 'PoaTransporte_Collection');
		$this->assertGreatherThan(1, count($buses));

		$bus = $buses[0];
		$this->assertEquals(get_class($bus), 'PoaTransporte_Unit');
	}

	public function testBusUnitData()
	{
		$buses = PoaTransporte::onibus();
		$bus = $buses[0];

		$this->assertNotNull(@$bus->nome);
		$this->assertNotNull(@$bus->codigo);
	}

}
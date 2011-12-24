<?php

require dirname(__FILE__).'/../src/poatransporte.php';

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	public function testBusList()
	{
		$buses = PoaTransporte::onibus();
		$bus = $buses[0];

		$this->assertEquals(get_class($buses), 'PoaTransporte_Collection');
		$this->assertEquals(get_class($bus), 'PoaTransporte_Unit');
		$this->assertObjectHasAttribute('nome', $bus);
		$this->assertObjectHasAttribute('codigo', $bus);
	}

}
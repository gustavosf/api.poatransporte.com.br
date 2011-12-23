<?php

class PoaTransporteTestCase extends PHPUnit_Framework_TestCase {
	
	public function testBusList()
	{
		$api = new PoaTransporte();
		
		$buses = $api->get_lists()->bus;
		$this->assertEquals(gettype($buses), 'array');
		
		$this->assertEquals(gettype($bus->nome), 'object');
		$this->assertEquals(get_class($bus->nome), 'PoaBusTransporte_Bus');
		$this->assertObjectHasAttribute('nome', $bus);
		$this->assertObjectHasAttribute('codigo', $bus);
	}

}
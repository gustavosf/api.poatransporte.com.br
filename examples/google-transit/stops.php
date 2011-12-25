<?php

/* Este exemplo lista todas as paradas da cidade conforme o padrão 
 *  pedido pelo Google para importação no Google Transit.
 *
 *  http://code.google.com/transit/spec/transit_feed_specification.html#General_Transit_Feed_Field_Definitions
 */

 require dirname(__FILE__).'/../src/poatransporte.php';

 $stops = PoaTransporte::paradas();

 echo implode("\t", array(
	 'stop_id',
	 'stop_name',
	 'stop_desc',
	 'stop_lat',
	 'stop_lon',
	 'zone_id',
	 'stop_url'
));

 foreach ($stops as $key => $stop)
 {
 	echo implode("\t", array(
		$stop->codigo,
		"Parada ".$stop->codigo,
		null,
		$stop->latitude,
		$stop->longitude,
		null,
		null,
	))."\n";
 }
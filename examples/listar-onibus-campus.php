<?php

require dirname(__FILE__).'/../src/poatransporte.php';

/* Listar todas as linhas de ônibus da cidade, com o nome campus */
$onibus = PoaTransporte::onibus()
		  ->find()
		  ->where('nome', 'campus')
		  ->execute();
		  
foreach ($onibus as $unidade)
{
	echo "[{$unidade->codigo}] {$unidade->nome}\n";
}
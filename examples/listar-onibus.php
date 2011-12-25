<?php

require dirname(__FILE__).'/../src/poatransporte.php';

/* Listar todas as linhas de ônibus da cidade */
$onibus = PoaTransporte::onibus();
foreach ($onibus as $unidade)
{
	echo "[{$unidade->codigo}] {$unidade->nome}\n";
}
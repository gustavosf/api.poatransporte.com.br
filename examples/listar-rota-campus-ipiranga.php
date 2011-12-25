<?php

require dirname(__FILE__).'/../src/poatransporte.php';

/* Puxa todas as rotas do campus ipiranga */
$onibus = PoaTransporte::onibus()
		  ->find()
		  ->where('nome', 'campus.*ipiranga')
		  ->execute();

 /* tem dois campus ipiranga, o de ida (codigo-1 e o de volta codigo-2) */
$campus_ida = $onibus->first();
$campus_volta = $onibus->last();

echo "Rota do Campus/Ipiranga, direção Centro/Bairro:\n";
print_r($campus_ida->route());

echo "Rota do Campus/Ipiranga, direção Bairro/Centro:\n";
print_r($campus_volta->route());
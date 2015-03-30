<?php

require_once('../vendor/autoload.php');


$kenteken = '5KGV46';
$peildatum = date('Ymd');

$vd = new Voertuigdata\Voertuigdata('username', 'password', ['environment'=>'production']);


try {
	$result = $vd->getCRWAMInformatieMiddelsKenteken(['Kenteken'=>$kenteken, 'Peildatum'=>$peildatum]);

	var_dump($result);

} catch (Voertuigdata\VoertuigdataException $e) {

	var_dump($e->getMessage());

}
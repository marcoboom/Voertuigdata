<?php

require_once('../vendor/autoload.php');


$kenteken = '5KGV46';

$vd = new Voertuigdata\Voertuigdata('username', 'password', ['environment'=>'production']);


try {
	$result = $vd->getPersonenautoInformatieMiddelsKenteken(['Kenteken'=>$kenteken]);

	echo "<pre>".print_r($result,true)."</pre>";

} catch (Voertuigdata\VoertuigdataException $e) {

	var_dump($e->getMessage());

}

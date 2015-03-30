# Voertuigdata
PHP Wrapper for the Voertuigdata.nl SOAP webservice

## Installation and Requirements

First, you'll need to require the package with Composer:

```bash
$ composer require marcoboom/voertuigdata
```

This package requires PHP 5.4 with SOAP installed

## Usage

First, create an Instance of the Voertuigdata class

```php
$vd = new Voertuigdata\Voertuigdata('username', 'password');
```
Replace username and password with the given account.

Optional you can pass as third argument, an options array:

```php

$options = [
	'environment' 	=> 	'production',
	'cache_wsdl' 	=> 	WSDL_CACHE_NONE
];

```

If the environment option is not passed through. The class wil search for an ENVIRONMENT constant. If this is not equal to production, the testing environment of Voertuigdata will be used.

## Calls

Within the instance you can call a method from the webservice, defined at <a href="http://www.voertuigdata.nl/soap/voertuigservice.asmx">http://www.voertuigdata.nl/soap/voertuigservice.asmx</a>. The parameters of the method should be send through by an array.

Each method returns the raw object given by the webservice. 

If the call failes it throws a VoertuigdataException. 


``php


try {
	$result = $vd->getPersonenautoInformatieMiddelsKenteken(['Kenteken'=>'56RPFV']);

	var_dump($result);

} catch (Voertuigdata\VoertuigdataException $e) {

	var_dump($e->getMessage());

}


```

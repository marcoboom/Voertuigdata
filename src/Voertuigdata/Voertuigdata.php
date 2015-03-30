<?php namespace Voertuigdata;


use SoapClient;
use SoapFault;

class Voertuigdata {

	protected $environments = [
		'testing'		=>		[

				'wsdl'		=>		'http://217.102.248.68/soap/voertuigservice.asmx?wsdl',
				'options'	=>		[

						'soap_version'  => 	SOAP_1_1,
						'exceptions' 	=> 	true,
						'trace' 		=> 	1,
						'cache_wsdl' 	=> 	WSDL_CACHE_NONE,

				]

		],

		'production'	=>		[

				'wsdl'		=>		'http://www.voertuigdata.nl/soap/voertuigservice.asmx?wsdl',
				'options'	=>		[

						'soap_version'  => 	SOAP_1_1,
						'exceptions' 	=> 	true,
						'trace' 		=> 	0,
						'cache_wsdl' 	=> 	WSDL_CACHE_BOTH,

				]
		]
	];

	protected $options;
	protected $username;
	protected $password;

	protected $client 	= 	null;

	protected $session 	=	null;


	/**
	 * Create an instance of the class
	 * 
	 *  @param string $username
	 *  @param string $password
	 *  @param array $options
	 * 
	 *  @return void
	 */


	public function __construct($username, $password, array $options=array()) {

		$this->username = $username;
		$this->password = $password;
		$this->options = $options;

	}

	/**
 	 * A static mapper to create an instance of this class
 	 *  
	 *  @param string $username
	 *  @param string $password
	 *  @param array $options
	 * 
	 * @return this
	 */

	public static function connect($username, $password, array $options=array()) {

		$self = new static($username, $password, $options);

		return $self;
	}


	/**
 	 * A static mapper for the __call method
 	 *  
	 * @param string $method
	 * @param array $params
	 * 
	 * @return Object
	 */

	public static function __callStatic($method, $params) {

		$self = new Static();

		return call_user_func_array([$self, $method], $params);

	}

	/**
	 * Call methods from the SoapServer dynammicly
	 * Open the URL of the webservice to find a complete list of the methods
	 * 
	 * @param string $method
	 * @param array $params
	 * 
	 * @return Object
	 */

	public function __call($method, $params=array()) {

		try {

			if (empty($this->client)) {
				$this->_connect();
			}

			$params = (array)reset($params);

			if ($method != 'getSession') {
				$params = array_merge(['SessionId' => $this->sessionId()], $params);
			}

			$result = $this->client->$method($params);
		}

		catch (VoertuigdataException $e) {

			throw $e;

		}

		catch (SoapFault $e) {

			throw new VoertuigdataException('SOAP Error: '.$e->getMessage, 0);

		}

		$result_name = $method."Result";

		if (!isset($result->$result_name)) {
			throw new VoertuigdataException('Geen resultaat gevonden', 0);
		}

		if (!empty($result->$result_name->ErrorCode)) {
			throw new VoertuigdataException($result->$result_name->ErrorMessage, $result->$result_name->ErrorCode);
		}

		return $result->$result_name;		
	}

	/**
	 * Connect to the soap server
	 * 
	 * @return SoapClient
	 */

	protected function _connect() {
		$env = $this->getEnvironment();

		try {
			$this->client = new SoapClient($env['wsdl'], $env['options']);
		}

		catch (\FatalErrorException $fault) {
			
			throw new VoertuigdataException('Fatal Error: '.$fault->getMessage(), 0);
		
		}

		catch (SoapFault $fault) {
			
			throw new VoertuigdataException('SOAP Connection Error: '.$fault->getMessage(), 0);
		
		}

		try {
			$this->session = $this->getSession(['Gebruikersnaam'=>$this->username, 'Wachtwoord'=>$this->password]);
		}

		catch (VoertuigdataException $e) {
			throw $e;
		}

		return $this->client;

	}


	/**
	 * Get the sessionId from VoertuigData generated with the connection
	 * 
	 * @return string
	 */

	protected function sessionId() {

		return $this->session->SessionId;

	}

	/**
	 * Get the environment to connect with, based on the given environment
	 * 
	 *  @return array
	 */

	protected function getEnvironment() {

		if (!empty($this->options['environment'])) {

			$env = $this->options['environment'];

		}

		elseif (defined('ENVIRONMENT')) {

			$env = ENVIRONMENT;

		}

		if ($env != 'production') {

			$config = $this->environments['testing'];

		}
		else {
		
			$config = $this->environments['production'];

		}

		$config['options'] = array_merge($config['options'], $this->options);


		return $config;
	}


}
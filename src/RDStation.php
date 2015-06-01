<?php namespace Cacobala\RDStation;

use Cacobala\RDStation\RDException;
use Guzzle\Http\Client as GuzzleClient;

class RDStation
{

	/**
	 * RDStation account token
	 * @type string
	 */
	protected $token = null;

	/**
	 * API Url
	 * @var string
	 */
	protected $api_url = 'http://www.rdstation.com.br/api/1.2/';
	
	/**
	 * Class constructor
	 * @param string $token      RDStation account token
	 * @param string $identifier Page or Event identifier
	 */
	public function __construct($token = null)
	{
		if (!$token) {
      $token = getenv('RDSTATION_TOKEN');
    }
    if(!$token) {
    	throw new RDException('You must provide a RDStation Token');
    }
	}

	/**
	 * Prepare data to request
	 * @param  array $data Data to sent
	 * @return string      URL-encoded query string
	 */
	protected function prepareData($data, $email, $identifier)
	{

		if (isset($_COOKIE['__utmz'])) {

			$data['c_utmz'] = $_COOKIE['__utmz'];

		}

		unset(
			$data['password'],
			$data['password_confirmation'],
			$data['senha'], 
          	$data['confirme_senha'],
          	$data['captcha'],
          	$data['_wpcf7'],
          	$data['_wpcf7_version'],
          	$data['_wpcf7_unit_tag'],
          	$data['_wpnonce'], 
          	$data['_wpcf7_is_ajax_call']
		);

		return array_merge(
			$data,
			[
				'token_rdstation' => $this->token,
				'identificador'   => $identifier,
				'email'						=> $email
			]
		);

	}

	/**
	 * Send data to RDStation
	 * @param  array $data       Data to sent
	 * @param  string $identifier Page or event identifier
	 * @return boolean
	 */
	public function send($data, $email, $identifier)
	{

		$data = $this->prepareData($data, $email, $identifier);
		
		try {

			$client = new GuzzleClient($this->getApiUrl());

			$request = $client->post(
				'conversions',
				array(
					'config' => array(
						'curl' => array(
							CURLOPT_POST => 1,
							CURLOPT_FOLLOWLOCATION => 1,
							CURLOPT_SSL_VERIFYPEER => false
						)
					)
				),
				$data
			)->send();

			return true;

		} catch (RDException $e) {

			if (ini_get('display_errors')) {

				echo $e->getMessage();

			}

			return false;

		}

	}

}
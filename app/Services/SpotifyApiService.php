<?php

namespace App\Services;

use GuzzleHttp\Client;

class SpotifyApiService
{
	const URL_ACCOUNTS = 'https://accounts.spotify.com';
	const URL_API = 'https://api.spotify.com/v1';

	const METHOD_POST = 'post';
	const METHOD_GET = 'get';

	const RESPONSE_STATUS_CODE = 'status_code';
	const RESPONSE_BODY = 'body';

	// Move this to config and inject it
	const CLIENT_ID = '405f604a1e18471987f020130144187c';
	const CLIENT_SECRET = 'db1d6aaf6c304e8089a24f81ff491e3b';

	protected $http;

	public function __construct(Client $client)
  {
  	$this->http = $client;
  }

  private function request($url, $uri, $method, $query = [], $headers = [])
  {
      $full_path = $url;
      $full_path .= $uri;
      $headers += [
      	'Content-Type' => 'application/x-www-form-urlencoded'
      ];

      $query += [
      	'client_id' => self::CLIENT_ID,
      	'client_secret' => self::CLIENT_SECRET,
      ];

      $params = [
      		'debug'           => true,
          'headers'         => $headers,
          'timeout'         => 30,
          'connect_timeout' => true,
          'http_errors'     => true,
          'query' 					=> $query
      ];

      switch ($method) {
      	case self::METHOD_POST:
      		$request = $this->http->post($full_path, $params);
      		break;
      	case self::METHOD_GET:
      		$request = $this->http->get($full_path, $params);
      		break;
      	default:
      		return false;
      		break;
      }
      

      $response = $request ? $request->getBody()->getContents() : null;
	    $status = $request ? $request->getStatusCode() : 500;

	    return [
	    	self::RESPONSE_STATUS_CODE => $status,
	    	self::RESPONSE_BODY => $response === null ? null : json_decode($response, true)
	    ];
  }

  public function getAuthToken($code, $redirectUri) 
  {
  	$params = [
      'grant_type' => 'authorization_code',
      'code' => $code,
      'redirect_uri' => $redirectUri
    ];

    return $this->request(self::URL_ACCOUNTS, '/api/token', self::METHOD_POST, $params);
  }

  public function refreshAuthToken($refreshToken)
  {
  	$params = [
      'grant_type' => 'refresh_token',
      'refresh_token' => $refreshToken
    ];
    
  	return $this->request(self::URL_ACCOUNTS, '/api/token', self::METHOD_POST, $params);
  }

  public function getCurrentUser($accessToken) 
  {
  	$headers = ['Authorization' => 'Bearer ' . $accessToken];
  	return $this->request(self::URL_API, '/me', self::METHOD_GET, [], $headers);
  }

}
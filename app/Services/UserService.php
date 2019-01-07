<?php 

namespace App\Services;

use App\User;
use App\Services\SpotifyApiService;

class UserService
{
	protected $spotify;

	public function __construct(SpotifyApiService $spotify)
  {
  	$this->spotify = $spotify;
  }

  private function saveUserToSession($user)
  {
  	session(['spotify_id' => $user->spotify_id]);
  }

  private function checkAccessTokenExpired($user)
  {
  	$now = new \DateTime();

  	if ($now->getTimestamp() > $user->expire_time) {
  		// Refresh token
  		
  		$response = $this->spotify->refreshAuthToken($user->refresh_token);
  		$body = $response['body'];
  		$user->access_token = $body['access_token'];
      $user->expire_time = $now->getTimestamp() + $body['expires_in'];
      $user->save();
  	}

  	return $user;
  }

  public function getUserFromSession()
  {
  	$spotifyId = session('spotify_id');
  	$user = User::where('spotify_id', $spotifyId)->first();
  	$user = $this->checkAccessTokenExpired($user);
  	return $user;
  }

  public function getUserFromAccessToken($accessToken, $expiresIn, $refreshToken)
  {
  	$response = $this->spotify->getCurrentUser($accessToken);
    $body = $response['body'];


    $date = new \DateTime();
    $expireTime = $date->getTimestamp() + $expiresIn;

    $user = User::firstOrNew(['spotify_id' => $body['id']]);

    if(!$user->exists()) {
      $user->spotify_id = $body['id'];
      $user->name = $body['display_name'];
      $user->picture_url = $body['images'][0]['url'];
      $user->access_token = $accessToken;
      $user->refresh_token = $refreshToken;
      $user->expire_time = $expireTime;
      $user->save();
    } else {
      $user->access_token = $accessToken;
      $user->refresh_token = $refreshToken;
      $user->expire_time = $expireTime;
      $user->save();
    }

    $this->saveUserToSession($user);
    return $user;
  }
}
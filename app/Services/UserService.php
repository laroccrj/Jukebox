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

  public function getUserFromSession()
  {
  	$spotifyId = session('spotify_id');
  	return User::where('spotify_id', $spotifyId)->first();
  }

  public function getUserFromAccessToken($token, $expiresIn, $refresh_token)
  {
  	$response = $this->spotify->getCurrentUser($token);
    $body = $response['body'];


    $date = new \DateTime();
    $expire_time = $date->getTimestamp() + $expiresIn;

    $user = User::firstOrNew(['spotify_id' => $body['id']]);

    if(!$user->exists()) {
      $user->spotify_id = $body['id'];
      $user->name = $body['display_name'];
      $user->picture_url = $body['images'][0]['url'];
      $user->access_token = $access_token;
      $user->refresh_token = $refresh_token;
      $user->expire_time = $expire_time;
      $user->save();
    } else {
      $user->access_token = $token;
      $user->refresh_token = $refresh_token;
      $user->expire_time = $expire_time;
      $user->save();
    }

    $this->saveUserToSession($user);
    return $user;
  }


}
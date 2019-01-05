<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\User;

class SpotifyConnectorController extends Controller
{
  /**
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    return view('spotify-connector');
  }

  public function connect()
  {
    $scopes = 'streaming playlist-read-private';
    $redirect = 'http://jukebox.test/spotify/accept-auth';
    $url = 'https://accounts.spotify.com/authorize?response_type=code&client_id=405f604a1e18471987f020130144187c'
      . '&scope=' . urlencode($scopes)
      . '&redirect_uri='. urlencode($redirect);
    return Redirect::to(
      $url
    );
  }

  public function acceptAuth(Request $request)
  {
    $code = $request->input('code');

    $ch = curl_init();

    $params = [
      'grant_type' => 'authorization_code',
      'code' => $code,
      'redirect_uri' => 'http://jukebox.test/spotify/accept-auth',
      'client_id' => '405f604a1e18471987f020130144187c',
      'client_secret' => 'db1d6aaf6c304e8089a24f81ff491e3b'
    ];

    curl_setopt($ch, CURLOPT_URL,"https://accounts.spotify.com/api/token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close ($ch);

    $dr = json_decode($response, true);

    $accessToken = $dr['access_token'];
    $chMe = curl_init();
    curl_setopt($chMe, CURLOPT_URL,"https://api.spotify.com/v1/me");
    curl_setopt($chMe, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
    curl_setopt($chMe, CURLOPT_RETURNTRANSFER, true);

    $response2 = curl_exec($chMe);
    $dr2 = json_decode($response2, true);

    curl_close ($chMe);


    $date = new \DateTime();
    $expire_time = $date->getTimestamp() + $dr['expires_in'];

    $user = User::firstOrNew(['spotify_id' => $dr2['id']]);

    if(!$user->exists()) {
      $user->spotify_id = $dr2['id'];
      $user->name = $dr2['display_name'];
      $user->picture_url = $dr2['images'][0]['url'];
      $user->access_token = $dr['access_token'];
      $user->refresh_token = $dr['refresh_token'];
      $user->expire_time = $expire_time;
      $user->save();
    } else {
      $user->access_token = $dr['access_token'];
      $user->refresh_token = $dr['refresh_token'];
      $user->expire_time = $expire_time;
      $user->save();
    }

    /*DB::insert('insert into users (spotify_id, name, picture_url, access_token, refresh_token, expire_time) values (?, ?, ?, ?, ?, ?)',
     [$dr2['id'], $dr2['display_name'], $dr2['images'][0]['url'], $dr['access_token'], $dr['refresh_token'], $expire_time]);

*/

    return view('spotify-connector-accept-auth', [
      'params' => print_r($request->all(), true), 
      'code' => $code, 
      'response' => print_r($response, true),
      'response2' => print_r($response2, true),
      'user' => print_r($user, true)
    ]);
  }
}

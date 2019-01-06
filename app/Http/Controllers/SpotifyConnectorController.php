<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Services\SpotifyApiService;
use App\Services\UserService;

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

  public function acceptAuth(Request $request, SpotifyApiService $spotify, UserService $userService)
  {
    $code = $request->input('code');
    $response = $spotify->getAuthToken($code, 'http://jukebox.test/spotify/accept-auth');
    $dr = $response['body'];

    $accessToken = $dr['access_token'];
    $expiresIn = $dr['expires_in'];
    $refreshToken = $dr['refresh_token'];
    $user = $userService->getUserFromAccessToken($accessToken, $expiresIn, $refreshToken);

    return Redirect::to(
      '/player'
    );
  }
}

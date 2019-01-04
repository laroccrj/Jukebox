<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
    $redirect = 'http://jukebox.test/spotify';
    $url = 'https://accounts.spotify.com/authorize?response_type=code&client_id=405f604a1e18471987f020130144187c'
      . '&scope=' . urlencode($scopes)
      . '&redirect_uri='. urlencode($redirect);
    return Redirect::to(
      $url
    );
  }
}

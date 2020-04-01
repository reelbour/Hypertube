<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\User;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
      if ($provider == 'github')
      {
    	return Socialite::driver($provider)->redirect();
      }
      else {
        //gerer l envoi de donne a l api 42

        return redirect('https://api.intra.42.fr/oauth/authorize?client_id=7cd0852136808242607c66d2ab64a711fbb4542c2a06b41fa7b727e658d57249&redirect_uri=http%3A%2F%2Flocalhost%3A8888%2Fpublic%2Fsocialauth%2Fintra%2Fcallback&response_type=code');
      }
    }

    public function handleProviderCallback($provider)
    {

      if ($provider == 'github')
      {
    	$user = Socialite::driver($provider)->user();

        $authUser=User::firstOrNew(['email'=>$user->email]);

        $authUser->name=$user->name;
        $authUser->email=$user->email;
        $authUser->provider=$provider;

        $authUser->save();

        auth()->login($authUser);

        return redirect('/');
      }
      else if($provider == 'intra')
      {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL,'https://api.intra.42.fr/oauth/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);

        if (!isset($_GET['code']))
        return redirect('/');

        $array = [
          "grant_type" => "authorization_code",
          "client_id" => "7cd0852136808242607c66d2ab64a711fbb4542c2a06b41fa7b727e658d57249",
          "client_secret" => "68711b1cf988be48a92d66424147fdda7c9c00c4f3b072a76aabe47c53717f2d",
          "code" => $_GET['code'],
          "redirect_uri" => "http://localhost:8888/public/socialauth/intra/callback",
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, $array);

        $response = curl_exec($curl);
        curl_close($curl);

        $sosa = explode(',' ,$response);
        $sosa = $sosa[0];
        $sosa = substr($sosa, 17 , -1);

        $curl = curl_init();
        $arr = [
          'Authorization: Bearer ' . $sosa
        ];

        curl_setopt($curl, CURLOPT_URL,'https://api.intra.42.fr/v2/me');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $arr);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        $authUser=User::firstOrNew(['email'=>$response['email']]);
        $authUser->name=$response['login'];
        $authUser->email=$response['email'];
        $authUser->provider=$provider;

        $authUser->save();

        auth()->login($authUser);

        return redirect('/');

      }

    }
}

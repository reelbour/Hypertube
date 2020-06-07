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
      else
      {
        //gerer l envoi de donne a l api 42
        return redirect('https://api.intra.42.fr/oauth/authorize?client_id=8b9d7990b08e876a4283bd1adced27a957a6e8065d3343d12ecb45448bd5c1ac&redirect_uri=http%3A%2F%2Flocalhost%3A8080%2Fpublic%2Fsocialauth%2Fintra%2Fcallback&response_type=code');
      }
    }

    public function handleProviderCallback($provider)
    {

      if ($provider == 'github')
      {
    	   $user = Socialite::driver($provider)->user();
         $authUser=User::firstOrNew(['email'=>$user->email]);

        $authUser->name = $user->name;
        $authUser->email = $user->email;
        $authUser->provider = $provider;
        $authUser->path_picture = $user->avatar;

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
          "client_id" => "8b9d7990b08e876a4283bd1adced27a957a6e8065d3343d12ecb45448bd5c1ac",
          "client_secret" => "1ff3ae0cf6db45fe2486379eea6573bf4f6c57a4c1da3d1398c82ca9d13281bc",
          "code" => $_GET['code'],
          "redirect_uri" => "http://localhost:8080/public/socialauth/intra/callback",
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
        $authUser->name=$response['first_name'];
        $authUser->last_name=$response['last_name'];
        $authUser->path_picture=$response['image_url'];
        $authUser->email=$response['email'];
        $authUser->provider=$provider;

        $authUser->save();

        auth()->login($authUser);

        return redirect('/');

      }

    }
}

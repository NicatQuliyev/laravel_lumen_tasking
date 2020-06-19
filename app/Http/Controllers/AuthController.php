<?php


namespace App\Http\Controllers;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    protected function jwt($user) {
        $payload = [
            'iss' => "Hybrid Tasking User",
            'sub' => $user->id,
            'sub_name' => $user->name,
            'iat' => time(),
            'exp' => time() + 60*200
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function authenticate(Request $request){
        $this->validate($request, [
           'email' => 'required|email',
           'password' => 'required'
        ]);

        $user = DB::table('users')
            ->where('email', '=', $request->email)->first();

        if(!$user)
        {
            $response = [
                'code' => 400,
                'message' => 'Sistemdə bu emailə aid istifadəçi tapılmadı'
            ];

            return response()->json($response, 400);
        }

        if(HASH::check($request->password, $user->password))
        {
            $response = [
              'code' => 200,
              'token' => $this->jwt($user)
            ];
            return response()->json($response, 200);
        }

       // Bad Request response
            $response = [
              'code' => 400,
              'message' => 'Sistemdə bu emailə aid istifadəçi tapılmadı'
            ];
            return response()->json($response, 400);
    }

}
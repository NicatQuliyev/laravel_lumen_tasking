<?php


namespace App\Http\Controllers;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    protected function jwt($user) {
        $payload = [
            'iss' => "Hybrid Tasking User",
            'sub' => $user->id,
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
              'message' => 'Email vəya şifrə yanlış daxil olunub.'
            ];
            return response()->json($response, 400);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
           'name' => 'required',
           'email' => 'required|email',
           'password' => 'required'
        ]);

        $user = DB::table('users')
            ->where('email', '=', $request->email)
            ->select('email')
            ->first();

        if(!$user)
        {
            $success = DB::table('users')
                ->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

            if($success)
            {
                $user = DB::table('users')
                    ->where('email', '=', $request->email)->first();
                $response = [
                  'code' => 200,
                  'token' => $this->jwt($user)
                ];
                return response()->json($response, 200);
            }
            else
            {
                $response = [
                  'code' => 400,
                  'message' => 'Qeydiyyat zamanı xəta baş verdi. Zəhmət olmasa bir daha yoxlayın vəya sistem administratoru ilə əlaqə saxlayın.'
                ];

                return response()->json($response, 400);
            }
        }
        else
        {
            $response = [
                'code' => 400,
                'message' => 'Bu email ilə artıq istifadəçi yaradılıb.'
            ];
            return response()->json($response, 400);
        }

    }

    public function user(Request $request)
    {
        $token = $request->header('Authorization');

        try{
            $creds = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        }catch (ExpiredException $e){
            return response()->json([
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 400);
        }catch (SignatureInvalidException $te){
            return response()->json([
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 400);
        }catch (BeforeValidException $bfe)
        {
            return response()->json([
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 400);
        }catch(\Exception $e)
        {
            return response()->json([
                'error' => 'Tokenin uyğun deyil'
            ], 400);
        }

        $user = DB::table('users')
            ->select('id', 'name', 'email', 'created_at', 'updated_at')
            ->where('id', '=', $creds->sub)
            ->first();

        if(!$user)
        {
            $response = [
              "code" => 401,
              "message" => "Sizin sistemdən istifadə üçün icazəniz yoxdur"
            ];

            return response()->json($response, 401);
        }

        return response()->json($user, 200);
    }

}
<?php


namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\DB;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Authorization');

        if(!$token)
        {
            return response()->json([
                'error' => 'Sistemdən istifadə üçün hüququnuz yoxdur'
            ], 401);
        }

        try{
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        }catch (ExpiredException $e){
            return response()->json([
                'code' => 401,
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 401);
        }catch (SignatureInvalidException $te){
            return response()->json([
                'code' => 401,
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 401);
        }catch (BeforeValidException $bfe)
        {
            return response()->json([
                'code' => 401,
                'error' => 'Tokenin sessia müddəti bitmişdir'
            ], 401);
        }catch(\Exception $e)
        {
            return response()->json([
                'code' => 401,
                'error' => 'Tokenin uyğun deyil'
            ], 401);
        }

        $user = DB::table('users')->where('id', '=', $credentials->sub);

        $request->auth = $user;

        return $next($request);
    }
}
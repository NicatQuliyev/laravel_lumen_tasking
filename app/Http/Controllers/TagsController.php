<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    public function getTags(){
        $tagQuery = DB::table('tags')->select('id', 'name');
        $tags = $tagQuery->get();

        return response()->json($tags, 200);
    }
    public function getTag(Request $request){
        $tagQuery = DB::table('tags')
            ->select('id', 'name');

        if($request->has('name'))
        {
            $tagQuery->where('name', '=', $request->name);
        }
        if($request->has('id'))
        {
            $tagQuery->where('id', '=', $request->id);
        }

        $tag = $tagQuery->first();

        if(isNull($tag))
        {
            $response = [
                'code' => 404,
                'message' => 'Axtardığınız tag tapılmadı'
            ];
            return response()->json($response, 404);
        }

        return response()->json($tag, 200);
    }
}
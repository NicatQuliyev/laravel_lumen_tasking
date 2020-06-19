<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{

    public function storeTask(Request $request){

        $this->validate($request, ['title' => 'required']);

        $tag_id = null;

        if($request->has('tag_name'))
        {
            $tag = DB::table('tags')
                ->where('name', '=', $request->tag_name)
                ->first() ?? null;
        }
        if(is_null($tag) && $request->has('tag_name'))
        {
            $tag_success = DB::table('tags')->insert([
               'name' => $request->tag_name
            ]);

            $tag = DB::table('tags')
                ->where('name', '=', $request->tag_name)
                ->select('id')
                ->first();
        }

        $success = DB::table('tasks')->insert([
            'title' => $request->title,
            'tag_id' => $tag->id,
            'isDone' => $request->has('isDone') ? $request->isDone : 0
        ]);

        if(!$success)
        {
            $response = [
                'code' => 400,
                'message' => 'Məlumatların daxil olunması zamanı xəta baş verdi.'
            ];

            return response()->json($response, 201);
        }

        $response = [
            'code' => 201,
            'message' => 'Məlumatlar uğurla yaradıldı.'
        ];

        return response()->json($response, 201);
    }

    public function getTasks(Request $request){
        $tasksQuery = DB::table('tasks')
                            ->select();

        if($request->has('isdone'))
        {
            $tasksQuery->where('isDone', '=', $request->isdone);
        }
        if($request->has('tag'))
        {
            $tasksQuery->where('tag_id', '=', $request->tag);
        }
        $tasks = $tasksQuery->get();

        if($tasks)
        {
            foreach($tasks as $task)
            {
                $task->isDone = $task->isDone == 0 ? false : true;
            }
        }

        return response()->json($tasks, 200);
    }

    public function taskById($id)
    {
        $taskQuery = DB::table('tasks')
            ->select('id','title','isDone')
            ->where('id', '=', $id);
        $task = $taskQuery->first();

        if(!$task)
        {
            $response = [
                'code' => 404,
                'message' => 'Axtarılan məlumat tapılmadı'
            ];

            return response()->json($response, 404);
        }

        return response()->json($task, 200);
    }

    public function updateTask(Request $request)
    {
        $this->validate($request, [
           'id' => 'required'
        ]);

        $success = DB::table('tasks')
        ->where('id', '=', $request->id)
        ->update([
            'title' => $request->title,
            'isDone' => $request->isDone
        ]);

        if(!$success)
        {
            $response = [
                'code' => 400,
                'message' => 'Məlumatların yenilənməsi zamanı xəta baş verdi.'
            ];

            return response()->json($response, 400);
        }

        $response = [
            'code' => 200,
            'message' => 'Məlumatlar uğurla yeniləndi.'
        ];

        return response()->json($response, 200);
    }

    public function deleteTask($id)
    {
        $affected = DB::table('tasks')
            ->where('id', '=', $id)
            ->delete();

        if($affected > 0)
        {
            $response = [
                'code' => 200,
                'message' => 'Məlumat uğurla silindi!'
            ];

            return response()->json($response, 200);
        }

            $response = [
                'code' => 400,
                'message' => 'Məlumatın silinməsi zamanı xəta baş verdi!'
            ];

            return response()->json($response, 400);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{

    public function storeTask(Request $request){

        $this->validate($request, ['title' => 'required']);

        $success = DB::table('tasks')->insert([
            'title' => $request->title,
            'isDone' => $request->has('isDone') ? $request->isDone : 0
        ]);

        if(!$success)
        {
            $response = [
                'code' => 400,
                'message' => 'Məlumatların daxil olunması zamanı xəta baş verdi.'
            ];

            return response(json_encode($response), 400);
        }

        $response = [
            'code' => 201,
            'message' => 'Məlumatlar uğurla yaradıldı.'
        ];

        return response(json_encode($response), 201);
    }

    public function getTasks(Request $request){
        $tasksQuery = DB::table('tasks')
                            ->select();

        if($request->has('isdone'))
        {
            $tasksQuery->where('isDone', '=', $request->isDone);
        }

        $tasks = $tasksQuery->get();

        if($tasks)
        {
            foreach($tasks as $task)
            {
                $task->isDone = $task->isDone == 0 ? false : true;
            }
        }

        return response(json_encode($tasks), 200);
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

            return response(json_encode($response), 400);
        }

        $response = [
            'code' => 200,
            'message' => 'Məlumatlar uğurla yeniləndi.'
        ];

        return response(json_encode($response), 200);
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

            return response(json_encode($response), 200);
        }

            $response = [
                'code' => 400,
                'message' => 'Məlumatın silinməsi zamanı xəta baş verdi!'
            ];

            return response(json_encode($response), 400);
    }

}

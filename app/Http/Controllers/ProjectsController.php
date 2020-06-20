<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function getProjects(){
        $projectsQuery = DB::table('projects')
        ->select('id', 'parent_id as parentId', 'name');

        $projects = $projectsQuery->get();

        $treeNode = generateProjectsTree($projects);
//        $treeNode = clearEmptyChildren($treeNode);

        return response()->json($treeNode, 200);
    }
}

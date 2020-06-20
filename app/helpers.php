<?php

    function generateProjectsTree($projectsList, $parent = null){
       $tree = [];

        foreach ($projectsList as $project) {
            if ($project->parentId == $parent) {
                $tree[] = array(
                    'key' => $project->id,
                    'parentId' => $project->parentId,
                    'title' => $project->name,
                    'children' => generateProjectsTree($projectsList, $project->id)
                );
            }
        }

        return $tree;
    }

    /**
     * Clear empty children attributes
     * @param $tree
     */
    function clearEmptyChildren(&$tree)
    {
        foreach ($tree as $key => $value) {
            if (empty($value['children'])) {
                unset($tree[$key]['children']);
            } else {
                clearEmptyChildren($tree[$key]['children']);
            }
        }
    }
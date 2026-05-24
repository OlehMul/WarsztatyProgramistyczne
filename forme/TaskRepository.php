<?php

namespace forme;

use function Sodium\add;

class TaskRepository{
    private $tasks;
    private $pattern;
    private $tags;


    function  __construct(){
        if (!isset($_SESSION['tasks'])) {
            $_SESSION['tasks'] = [];
        }
        $this->tasks = $_SESSION['tasks'];

}


function add(Task $task){
$this->tasks[] = $task;
    $_SESSION['tasks'] = $this->tasks;

}
function remove(int $id){
    foreach ($this->tasks as $key => $task){
    if($task->getId() == $id){
        unset($this->tasks[$key]);
    }
}
    $_SESSION['tasks'] = $this->tasks;
return true;
}
function find(int $id){
foreach($this->tasks as $task){
    if($task->getId() == $id){
        return $task;
    }
}

}

function all(){
return $this->tasks;
}
function filterByStatus(string $status){
$newTasks = [];
for($i = 0; $i < count($this->tasks); $i++){
    if(preg_match("/".$status."/i", $this->tasks[$i]->getStatus())){
        $newTasks[] = $this->tasks[$i];
    }
}
return $newTasks;

}

    function filterByTag($tag = null){
        $tagToFilter = $tag ?? $this->tags;
        if(empty($tagToFilter)){
            return $this->tasks;
        }

        $newTasks = [];
        foreach($this->tasks as $task){
            if(in_array($tagToFilter, $task->getTags())){
                $newTasks[] = $task;
            }
        }
        return $newTasks;
    }
    function searchPattern(){
        $newTasks = [];
        foreach($this->tasks as $task){
            $found = false;
            // Create a clone to avoid modifying the original task in session
            $highlightedTask = clone $task;

            // Search and highlight in tags
            $highlightedTags = [];
            foreach($task->getTags() as $tag){
                if(preg_match("/".$this->pattern."/i", $tag)){
                    $found = true;
                    $highlightedTags[] = preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $tag);
                } else {
                    $highlightedTags[] = $tag;
                }
            }
            if($found){
                $highlightedTask->setTags($highlightedTags);
            }

            // Search and highlight in status
            if(preg_match("/".$this->pattern."/i", $task->getStatus())){
                $found = true;
                $highlightedTask->setStatus(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getStatus()));
            }

            // Search and highlight in priority
            if(preg_match("/".$this->pattern."/i", $task->getPriority())){
                $found = true;
                $highlightedTask->setPriority(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getPriority()));
            }

            // Search and highlight in title
            if(preg_match("/".$this->pattern."/i", $task->getTitle())){
                $found = true;
                $highlightedTask->setTitle(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getTitle()));
            }

            // Search and highlight in description
            if(preg_match("/".$this->pattern."/i", $task->getDescription())){
                $found = true;
                $highlightedTask->setDescription(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getDescription()));
            }

            if($found){
                $newTasks[] = $highlightedTask;
            }
        }
        return $newTasks;
    }


    public function setPattern($pattern){
        $this->pattern = $pattern;
    }


function sort(string $sortBy){
    if($sortBy ==="data"){
        usort($this->tasks, fn($a, $b) => $a->getCreatedAt() <=> $b->getCreatedAt());

    }else if($sortBy==="priority"){
        $priorityOrder = ["wysoki" => 0, "średni" => 1, "niski" => 2];

        usort($this->tasks,fn($a,$b) => $priorityOrder[$a->getPriority() <=> $b->getPriority()]);


    }else if($sortBy==="title"){
        usort($this->tasks, fn($a, $b) => $a->getTitle() <=> $b->getTitle());
    }
return $this->tasks;
}

function nextId(){
        if(!empty($this->tasks)){
            $c =0;
           foreach($this->tasks as $task){
               if($c < $task->getId()){
                   $c = $task->getId();
               }
           }
            return $c+1;
        }else{
            return 1;
        }


}

function findAndChnageStatus(int $id,string $status):void{
      $task = $this->find($id);
      $task->changeStatus($status);
    $_SESSION['tasks'] = $this->tasks;

}


    public function setTags($tags){
        $this->tags = $tags;
    }



}
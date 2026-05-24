<?php

namespace forme;

use function Sodium\add;

class TaskRepository{
    private $tasks;
    private $pattern;
    private $tags;


    function  __construct($a){
        $this->tasks = $a;

}


function add(Task $task){
$t = $this->tasks->prepare("INSERT INTO tasks(type,title,description,priority,status,tags,created_at,created_by,interval) VALUES(:type,:title,:description,:priority,:status,:tags,:created_at,:created_by,:interval)");

if($task instanceof RecurringTask){
    $t->execute([':type' =>"1",':title'=>$task->getTitle(),':description'=>$task->getDescription(),':priority'=>$task->getPriority(),':status'=>$task->getStatus(),':tags'=>json_encode($task->getTags()),':created_at'=>$task->getCreatedAt(),':created_by'=>$task->getCreatedBy(),':interval'=>$task->getInterval()]);
}else{
    $t->execute([':type' =>"1",':title'=>$task->getTitle(),':description'=>$task->getDescription(),':priority'=>$task->getPriority(),':status'=>$task->getStatus(),':tags'=>json_encode($task->getTags()),':created_at'=>$task->getCreatedAt(),':created_by'=>$task->getCreatedBy(),]);
}

}
function remove(int $id){
 $t = $this->tasks->prepare("DELETE FROM tasks WHERE id=:id");
 $t->execute([':id'=>$id]);
return true;
}


function find(int $id){
$t = $this->tasks->prepare("SELECT * FROM tasks WHERE id=:id");
$t->execute([':id'=>$id]);
return $t->fetch();

}

function all(){
        $r =[];
$t = $this->tasks->prepare("SELECT * FROM tasks");
$t->execute();
$a = $t->fetchAll();
foreach($a as $ts){
    $r[] = Task::fromArray($ts);
}
return $r;
}
function filterByStatus(string $status){
$t = $this->tasks->prepare('SELECT FROM tasks WHERE status = :status');
$t->execute([':status'=>$status]);
return $t->fetchAll();
}

    function filterByTag($tag = null){
        $tagToFilter = $tag ?? $this->tags;
        if(empty($tagToFilter)){
            return $this->tasks;
        }

     $t = $this->tasks->prepare('SELECT FROM tasks WHERE tags LIKE :tag');
        $t->execute([':tag'=>'%'.$tag.'%']);
        return $t->fetchAll();
    }
    function searchPattern(){
        $newTasks = [];
        foreach($this->tasks as $task){
            $found = false;

            $highlightedTask = clone $task;







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


            if(preg_match("/".$this->pattern."/i", $task->getStatus())){
                $found = true;
                $highlightedTask->setStatus(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getStatus()));
            }


            if(preg_match("/".$this->pattern."/i", $task->getPriority())){
                $found = true;
                $highlightedTask->setPriority(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getPriority()));
            }


            if(preg_match("/".$this->pattern."/i", $task->getTitle())){
                $found = true;
                $highlightedTask->setTitle(preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $task->getTitle()));
            }


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
        if($sortBy == "priority" || $sortBy == "data" || $sortBy == "title"){
            $t = $this->tasks->prepare("SELECT * FROM tasks ORDER BY $sortBy");
            $t->execute();
            return $t->fetchAll();
        }else{
            echo "Invalid sort by $sortBy";
        }

}



function findAndChnageStatus(int $id,string $status):void{
        $t = $this->tasks->prepare("UPDATE tasks set status=:status WHERE id=:id");
        $t->execute([':id'=>$id,':status'=>$status]);
}


    public function setTags($tags){
        $this->tags = $tags;
    }



}
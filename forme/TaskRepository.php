<?php

namespace forme;

use forDataBase\DataBase;
use function Sodium\add;

class TaskRepository{
    private $tasks;
    private $pattern;
    private $tags;


    function  __construct($a){
        $this->tasks = $a;

}


function add(Task $task){
$t = $this->tasks->prepare("INSERT INTO tasks(type,title,description,priority,status,tags,created_at,created_by,interval,estimated_minutes,category) VALUES(:type,:title,:description,:priority,:status,:tags,:created_at,:created_by,:interval,:estimated_minutes,:category)");

if($task instanceof RecurringTask){
    $t->execute([':type' =>"rec",':title'=>$task->getTitle(),':description'=>$task->getDescription(),':priority'=>$task->getPriority(),':status'=>$task->getStatus(),':tags'=>json_encode($task->getTags()),':created_at'=>$task->getCreatedAt(),':created_by'=>$task->getCreatedBy(),':interval'=>$task->getInterval(),':estimated_minutes'=>$task->getEstimatedMinutes(), ':category'=>$task->getCategory()]);
}else{
    $t->execute([':type' =>$task->getType(),':title'=>$task->getTitle(),':description'=>$task->getDescription(),':priority'=>$task->getPriority(),':status'=>$task->getStatus(),':tags'=>json_encode($task->getTags()),':created_at'=>$task->getCreatedAt(),':created_by'=>$task->getCreatedBy(),':interval'=>'',':estimated_minutes'=>$task->getEstimatedMinutes(), ':category'=>$task->getCategory()]);
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
    try {
        $obj = Task::fromArray($ts);
        $r[] = $obj;
    } catch(\Throwable $e) {
        var_dump($e->getMessage());
    }

}

return $r;
}
function filterByStatus(string $status){
$t = $this->tasks->prepare('SELECT FROM tasks WHERE status = :status');
$t->execute([':status'=>$status]);
return $t->fetchAll();
}

function filterByTag($tag = ''){
        if(empty($tag)){
            return $this->all();
        }

     $t = $this->tasks->prepare('SELECT * FROM tasks WHERE tags LIKE :tag');
            $t->execute([':tag'=>"%".$tag."%"]);
        $res = $t->fetchAll();
        $r=[];
        foreach($res as $a){
            $r[] = Task::fromArray($a);
        }

        return $r;
    }
    function searchPattern(){
        $newTasks = [];
        $db = DataBase::get();
            $found = false;


            $highlightedTags = [];
       /*     foreach($this->tasks->getTags() as $tag){
                $os = $db->prepare("SELECT * FROM tasks WHERE tags LIKE :tag");
                $os->execute([':tag'=>"%".preg_match("/".$this->pattern."/", $this->tasks->getTags())."%"]);
                $r = $os->fetchAll();
                foreach($r as $t){
                    $newTasks[] = Task::fromArray($t);

                }

                if(preg_match("/".$this->pattern."/i", $tag)){
                    $found = true;
                    $highlightedTags[] = preg_replace('/'.$this->pattern.'/i', "<mark>$0</mark>", $tag);

                } else {
                    $highlightedTags[] = $tag;
                }

            }
       */

$os = $db->prepare ("SELECT * FROM tasks WHERE 
            title LIKE :pattern OR 
            description LIKE :pattern OR 
            status LIKE :pattern OR 
            priority LIKE :pattern OR 
            tags LIKE :pattern");

$os->execute([':pattern'=>"%".$this->pattern."%"]);
$res = $os->fetchAll();
foreach($res as $a){
    $newTasks[] =Task::fromArray($a);
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
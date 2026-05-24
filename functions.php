<?php

function h(string $str): string {   /*skrócona wersja htmlspecialchars()*/
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function sortBy($req,$tasks){
if(strcmp($req,"data")){
    usort($tasks, fn($a, $b) => $a["created_at"] <=> $b["created_at"]);

}else if(strcmp($req,"priority")){
    $priorityOrder = ["wysoki" => 0, "średni" => 1, "niski" => 2];

    usort($tasks, function($a, $b) use ($priorityOrder) {
        return $priorityOrder[$a["priority"]] <=> $priorityOrder[$b["priority"]];
    });
}else if(strcmp($req,"title")){
usort($tasks, fn($a, $b) => $a["title"] <=> $b["title"]);
}


return $tasks;
}

function searchTasks($tasks, $pattern){ /*Search tasks*/
    $pattern = trim($pattern);
    $arr =[];

    foreach($tasks as $task){
        if(preg_match('/'.$pattern.'/', $task['title']) || preg_match('/'.$pattern.'/', $task['description'])){ /*Finds pattern either in title or desc*/
            if(preg_match('/'.$pattern.'/', $task['title'])){
                $task['title'] = preg_replace('/'.$pattern.'/', "<mark>$0</mark>", $task['title']); /*both this and under just mark the pattern in task*/
            }else{
                $task['description'] = preg_replace('/'.$pattern.'/', "<mark>$0</mark>", $task['description']);
            }

            $arr[] = $task;
        }

    }
    return $arr;
}

function filterTasksByTag($tasks, $tag){           /*Filter tasks by tags*/
    $arr = [];
    foreach($tasks as $task){
        if(in_array($tag, $task['tags'])){
            $arr[] = $task;
        }
    }
    return $arr;
}

function extractTags($made_up_tags){      /*dla znalezienia tagów*/
    $made_up_tags =preg_replace( '/#([a-zA-Z0-9_]+)/', '<mark>$0</mark>',$made_up_tags);
    return $made_up_tags;
}

function validateInput($input){
    $errors = [];

    if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $input)) {           /*dla sprawdzania e-mailów*/
        if (!preg_match('^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}', $input)) {
            $errors[] = "Nieprawidłowy format adresu email";
        }
    }
    if (preg_match('/#\w+/', $input)) {                 /*dla poszukiwania i sprawdzania tagów*/
        if (!preg_match('/#[a-zA-Z0-9_+]/', $input)) {
            $errors[] = "Nieprawidlowy format tagów";
        }
    }

    if(preg_match('/[0-9]{4}[.-][0-1][0-2][.-][0-9]{2}/', $input)){     /*dla sprawdzania dat*/
        if(!preg_match('^(\d{4}[.-]0[1-9]|1[0-2]|[1-9])[.-]([1-9]|0[1-9]|[1-2]\d|3[0-1]',$input)){
            $errors[] = "Nieprawidlowy format daty";
        }
    }
    return $errors;
}

function formatTaskDescription($description) {        /*For correct format in desc*/
    // Zamiana URL na linki HTML
    $description = preg_replace(
        '/\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/i',
        '<a href="$0" target="_blank">$0</a>',
        $description
    );

    // Wykrywanie i formatowanie tagów
    $description = preg_replace(
        '/#([a-zA-Z0-9_]+)/',
        '<b class="tag">$0</b>',
        $description
    );

    // Wykrywanie i formatowanie list punktowanych
    $description = preg_replace(
        '/^[\s]*[-*+][\s]+(.+)$/m',
        '<li>$1</li>',
        $description
    );

    // Owijanie list w znaczniki <ul></ul>
    if (strpos($description, '<li>') !== false) {
        $description = '<ul>' . $description . '</ul>';
        $description = str_replace('</ul><ul>', '', $description);
    }

    $description = preg_replace('/[0-9]{3}-[0-9]{3}-[0-9]{4}/','<u>$0</u>' ,$description);  /*sprawdza czy jest w opisie numer telefonu*/

    $description = preg_replace('/[0-9]{4}[.-][0-1][0-2][.-][0-9]{2}/','<u>$0</u>' ,$description);  /*sprawdza czy jest data*/

    $description = preg_replace('/[0-9]{2}[-:][0-9]{2}/','<u>$0</u>' ,$description);       /*sprawdza czy są podane godziny*/
    return $description;
}






function deleteTask($task_id){
    $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function($task) use($task_id){
        return $task['id'] != $task_id;
    });

}

function changeStatus($task_id,$status){
    foreach($_SESSION['tasks'] as &$task){
        if($task['id'] == $task_id){
            $task['status'] = $status;
        }

    }


}
?>

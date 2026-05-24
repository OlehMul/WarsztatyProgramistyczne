<?php
session_start();
$theme = $_COOKIE['theme'] ?? 'light';
$bgcolor = $theme === 'dark' ? '#1a1a2e' : 'white';
$textcolor = $theme === 'dark' ? 'white' : 'black';
require_once('functions.php');

if(!isset($_SESSION['login_time'])){
    $_SESSION['login_time'] = time();
}




$TASKS=[];

$theme =  $_COOKIE['theme'] ?? 'light';
$limit = $_COOKIE['num'] ?? 10;
$req = $_COOKIE['req'] ?? 'data';
if($theme == 'light'){
    $bgcolor = "white";
    $textcolor = "black";
}else{
    $bgcolor = "#1a1a2e";
    $textcolor = "white";

}

if(!isset($_SESSION['tasks'])){
    $_SESSION['tasks'] = [
            [
                    "title" => "Wdrożenie nowego systemu logowania",
                    "category" => "Praca",
                    "priority" => "wysoki",
                    "status" => "w trakcie",
                    "description" => "coś",
                    "estimated_minutes" => 120,
                    "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                    "created_by" => $_SESSION['user'],
                    "tags" => ["backend", "pilne"],
                    "id" => uniqid()
            ],
            ["title" => "Zakupy spożywcze",
                    "category" => "Dom",
                    "priority" => "niski",
                    "status" => "do zrobienia",
                    "description" => "coś",
                    "estimated_minutes" => 45,
                    "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                    "created_by" => $_SESSION['user'],
                    "tags" => ["dom", "zakupy"],
                    "id" => uniqid()
            ],
            [
                    "title" => "Nauka CSS – model pudełkowy",
                    "category" => "Nauka",
                    "priority" => "średni",
                    "status" => "do zrobienia",
                    "description" => "coś",
                    "estimated_minutes" => 60,
                    "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                    "created_by" => $_SESSION['user'],
                    "tags" => ["frontend"],
                    "id" => uniqid()
            ],
            [
                    "title" => "Opłacić rachunki",
                    "category" => "Dom",
                    "priority" => "wysoki",
                    "status" => "zakończone",
                    "description" => "coś",
                    "estimated_minutes" => 20,
                    "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                    "created_by" => $_SESSION['user'],
                    "tags" => ["pilne", "dom"],
                    "id" => uniqid()
            ],
            [
                    "title" => "Przegląd techniczny samochodu",
                    "category" => "Inne",
                    "priority" => "średni",
                    "status" => "w trakcie",
                    "description" => "coś",
                    "estimated_minutes" => 90,
                    "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                    "created_by" => $_SESSION['user'],
                    "tags" => ["pilne"],
                    "id" => uniqid()
            ],
    ];
}



//wartosci
$allowed_categories = ["Praca", "Dom", "Nauka", "Zdrowie", "Inne"];
$allowed_priorities = ["niski", "średni", "wysoki"];
$allowed_statuses = ["do zrobienia", "w trakcie", "zakończone"];
$allowed_tags  =["pilne", "zespół", "backend", "frontend"];





//default values
$errors  = [];
$form_title  = "";
$form_category= "Praca";
$form_priority= "niski";
$form_status = "do zrobienia";
$form_minutes = "";
$form_tags  = [];
$opis = "coś";
$success  = false;
$SEARCHCOM = false;
$pattern = "";
$tag="";
$made_up_tags="";


if(isset($_POST['confFilter'])){
    $tag = $_POST['tag'] ?? '';
}
if(isset($_POST['confSearch'])){
    $pattern = $_POST['SEARCHr'] ?? '';
}

if(isset($_POST['deleteTASK'])){
    deleteTask($_POST['delete_id']);
}
if(isset($_POST['CHANGEstatus'])){
    changeStatus($_POST['change_id'],$_POST['changeStatus']);
}


if(isset($_POST['submit'])){



    $form_title = trim($_POST['title']  ?? '');
    $form_category = trim($_POST['category'] ?? '');
    $form_priority= trim($_POST['priority'] ?? '');
    $form_status = trim($_POST['status'] ?? '');
    $form_minutes = trim($_POST['estimated_minutes'] ?? '');
    $opis = trim($_POST['description']  ?? 'coś');
    $form_tags  = $_POST['tags'] ?? [];
    $made_up_tags = $_POST['textTags'] ?? '';

    if(!empty($made_up_tags)){
        $array_of_tags = explode(" ", $made_up_tags);
        foreach ($array_of_tags as $tag) {
            if(!preg_match("/\#([a-zA-Z0-9_]+)/",$tag)){
                $errors[] = "nieprawidłowy format tagów";
                break;
            }
        }
    }


    if ($form_title === '') {
        $errors[] = "Tytuł zadania nie może być pusty.";
    }
    if ($form_minutes === '' || !is_numeric($form_minutes) || (int)$form_minutes <= 0) {
        $errors[] = "Czas powinen być w minutach";
    }
    if (empty($form_tags)) {
        $errors[] = "Musisz wybrać co najmniej jeden tag.";
    }
    if (!in_array($form_category, $allowed_categories)) {
        $errors[] = "Nieprawidłowa kategoria.";
    }
    if (!in_array($form_priority, $allowed_priorities)) {
        $errors[] = "Nieprawidłowy priorytet.";
    }
    if (!in_array($form_status, $allowed_statuses)) {
        $errors[] = "Nieprawidłowy status.";
    }


    //glowna czesc
    if (empty($errors)) {

        $sorted_tags = array_values(array_filter($form_tags));
        $sorted_tags[] = $made_up_tags;
        sort($sorted_tags);

        $_SESSION['tasks'][] = ["title"=> $form_title,
                "category"=> $form_category,
                "priority" => $form_priority,
                "status" => $form_status,
                "estimated_minutes"=> (int)$form_minutes,
                "tags" => $sorted_tags,
                "id" => uniqid(),
                "description" => $opis,
                "created_at" => date("Y-m-d H:i:s", $_SESSION['login_time']),
                "created_by" => $_SESSION['user']

        ];

        $success = true;
        $form_title = "";
        $form_category= "Praca";
        $form_priority= "niski";
        $form_status = "do zrobienia";
        $form_minutes = "";
        $form_tags = [];
        $opis = "coś";
        $made_up_tags="";

    }
}

$TASKS = $_SESSION['tasks'];




//stats
$all_statuses  = array_column($TASKS, 'status');
$total_tasks = count($TASKS);
$todo_count= count(array_keys($all_statuses, 'do zrobienia'));
$done_count  = count(array_keys($all_statuses, 'zakończone'));
$total_minutes = array_sum(array_column($TASKS, 'estimated_minutes'));


//short form of thingy


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="men.css" rel="stylesheet" type="text/css">
</head>



<body bgcolor= "<?php echo $bgcolor?>" text= "<?php echo $textcolor ?>" >
<div id="WholeThing">
    <header>
        <div id="logo">
            <div id="men">Menedżer zadań</div>
            <div class="headerthingy"><a href="header2.php" class="link">Prefferences</a></div>
            <div class="headerthingy">Zalogowany jako <?php echo $_SESSION['user']?></div>
            <div class="headerthingy"><a class="link" href="logout.php">Wyloguj</a></div>
            <div class="headerthingy" id="32"><a class="link" href="smth">Wszystkie</a></div>
            <div class="headerthingy"><a href="ssdasda1" class="link">Do zrobienia</a></div>
            <div class="headerthingy">Czas trwania sesji: <?php echo time() - $_SESSION['login_time'] ?></></div>
        <div class="headerthingy"><a href="smtsdas2" class="link">W trakcie</a></div>
        <div class="headerthingy"><a href="smth3asdasd" class="link">Zakończone</a></div>

    </header>
    <?php $ar = validateInput($opis) ?>
    <?php $errors = array_merge($errors, $ar) ?>

    <aside>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($success): ?>
            <p> Zadanie zostało dodane!</p>
        <?php endif; ?>


        <form id="form" method="post" action="index2.php">
            <fieldset>
                <h3>Dodaj zadanie</h3>

                <label for="title">Tytuł zadania</label>
                <div>
                    <input type="text" id="title" name="title" value="<?= h($form_title) ?>" required>
                </div>

                <label for="category">Kategoria</label>
                <div>
                    <select id="category" name="category">
                        <option value="Praca" >Praca</option>
                        <option value="Dom" >Dom</option>
                        <option value="Nauka" >Nauka</option>
                        <option value="Zdrowie" >Zdrowie</option>
                        <option value="Inne" >Inne</option>
                    </select>
                </div>

                <label for="priority">Priorytet</label>
                <div>
                    <select id="priority" name="priority">
                        <option value="niski" >Niski</option>
                        <option value="średni">Średni</option>
                        <option value="wysoki" >Wysoki</option>
                    </select>
                </div>

                <label for="status">Status</label>
                <div>
                    <select id="status" name="status">
                        <option value="do zrobienia">Do zrobienia</option>
                        <option value="w trakcie" >W trakcie</option>
                        <option value="zakończone" >Zakończone</option>
                    </select>
                </div>

                <label for="estimated_minutes">Szacowany czas (minuty)</label>
                <div>
                    <input type="text" id="estimated_minutes" name="estimated_minutes" value="<?= h($form_minutes) ?>">
                </div>

                <label for="Date">Data wykonania</label>
                <div>
                    <input type="date" id="Date" name="date">
                </div>

                <label for="des">Opis</label>
                <div>
                    <textarea id="des" name="description" value="<?= h($opis) ?>"></textarea>
                </div>

                <label> Pilne</label>
                <input type="checkbox" name="tags[]" value="pilne" >

                <label>  Zespół </label>
                <input type="checkbox" name="tags[]" value="zespół" >


                <label> Backend</label>
                <input type="checkbox" name="tags[]" value="backend" >


                <label> Frontend</label>


                <input type="checkbox" name="tags[]" value="frontend" >

                </label>

                <hr>
                <label>Tagi</label>
                <div>
                    <textarea name="textTags"></textarea>
                </div>
                <br>


                <div id="submit">
                    <button type="submit" name="submit">Dodaj zadanie</button>
                </div>
            </fieldset>
        </form>




        <form method="post" action="index2.php">
            <br>
            <label>FILTRY</label>
            <hr>                                     <!--FORM FOR PRIORITY FILTERING-->
            <div>
                <label>Pilne</label>
                <input type="radio" name ="tag" value="pilne">

                <label>Zespół</label>
                <input type="radio" name ="tag" value="zespół">

                <label>Backend</label>
                <input type="radio" name ="tag" value="backend">

                <label>Frontend</label>
                <input type="radio" name ="tag" value="frontend">
            </div>
            <button type="submit" name="confFilter">BIG BUTTON TEXT</button>


        </form>

        <hr>
        <form method="post" action="index2.php">      <!--FORM FOR SEARCH-->

            <label>Wyszukiwanie</label>
            <div>
                <textarea name="SEARCHr" value="search"></textarea>
            </div>
            <label>Confirm search</label>
            <div>

                <button type="submit" name="confSearch"> BIG BUTTON NAME</button>
            </div>
        </form>

    </aside>

    <div id="frommain">
        <main>
            <div class="zadanie">
                <div class="leftside">
                    <p4>ZADANIA DNIA</p4>
                    <p3>Wdrożenie nowego systemu logowania</p3>
                    <p>Kategoria: Praca</p>
                    <div id="aaa">
                        <p>ablablablaaaaaaaaaa</p>
                    </div>
                </div>
                <div class="rightside">
                    <p>Termin: 2026-04-10</p>
                    <div class="wysoki">Wysoki</div>
                    <div class="wtrakcie">W trakcie</div>
                </div>
            </div>

            <div id="topBoxes">
                <div class="box box1">
                    <h2><?= $total_tasks ?></h2>
                    <p>Wszystkie</p>
                </div>

                <div class="box box2">
                    <h2><?= $todo_count ?></h2>
                    <p>Do zrobienia</p>
                </div>

                <div class="box box4">
                    <h2><?= $done_count ?></h2>
                    <p>Zakończone</p>
                </div>

                <div class="box box4">
                    <h2><?= $total_minutes ?></h2>
                    <p>Łączny czas (min)</p>
                </div>
            </div>

            <div class="SORT">
                <p id="bu">Sortuj:
                    <button id="tytl" class="but">Tytul&#8593;</button>
                    <button class="but">Priorytet</button>
                    <button class="but">Data</button>
                    <button class="but">Kategoria</button>
                </p>
            </div>



            <?php
            $arr2 = !empty($pattern) ? searchTasks($_SESSION['tasks'], $pattern) : [];
            $arr3 = !empty($tag) ? filterTasksByTag($_SESSION['tasks'], $tag) : [];
            ?>

            <?php
            $filterActive = isset($_POST['confFilter']);
            $searchActive = isset($_POST['confSearch']);

            if ($filterActive && !empty($arr3)):       /*displays tasks if there are active filters*/
                $displayTasks = $arr3;
            elseif ($searchActive && !empty($arr2)):    /*displays tasks after search*/
                $displayTasks = $arr2;
            elseif (!$filterActive && !$searchActive):    /*default: shows all tasks*/
                $displayTasks = $_SESSION['tasks'];
            else:
                $displayTasks = null;        /*if nothing was found neither by filter nor search*/
            endif;
            ?>



            <?php if ($displayTasks !== null): ?>
                <?php $displayTasks = array_slice($displayTasks, 0, $limit); ?>


                <?php if(!empty($_COOKIE["sort"])){
                    $displayTasks =  sortBy($req,$displayTasks);
                } ?>


                <?php foreach ($displayTasks as $task): ?>
                    <div id="first box" class="boxes">
                        <h2><?= h($task['title']) ?></h2>
                        <p>Kategoria: <?= h($task['category']) ?></p>
                        <p>Priorytet: <?= h($task['priority']) ?></p>
                        <p>Opis: <?= formatTaskDescription($task['description']) ?></p>
                        <p>Tagi: <?= extractTags(implode(', ', $task['tags'])) ?></p>

                        <p>Status: <?= h($task['status']) ?></p>
                        <p>Czas: <?= h($task['estimated_minutes']) ?> min</p>
                        <p>Created by <?= h($task['created_by']) ?></p>
                        <p>Created at <?= h($task['created_at']) ?></p>
                        <form method="post" action="index2.php">
                            <input type="hidden" name="delete_id" value="<?= h($task['id']) ?>">
                            <button type="submit" name="deleteTASK">DELETE TASK</button>
                        </form>
                        <form action="index2.php" method="post">
                            <select name="changeStatus">
                                <option value="do zrobienia">Do zrobienia</option>
                                <option value="w trakcie">W trakcie</option>
                                <option value="zakończone">Zakończone</option>
                            </select>
                            <input type="hidden" name="change_id" value="<?= h($task['id']) ?>">
                            <button type="submit" name="CHANGEstatus">CHANGE STATUS</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div>
                    <p>No task found</p>
                </div>
            <?php endif; ?>



        </main>
    </div>

    <footer>
        <div class="123">
            smth
        </div>
    </footer>
</div>
</body>
</html>
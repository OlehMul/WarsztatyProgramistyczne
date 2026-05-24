<?php

use forme\TaskRepository;

require_once "forme/Autoloader.php";
session_start();

$pattern ='';
$tag ='';
if(!isset($_SESSION['login_time'])){
    $_SESSION['login_time'] = time();
}
$_SESSION['last_update_time'] = time();

$db = \forDataBase\DataBase::get();
\forDataBase\DataBase::migrate();
$repo = new \forme\TaskRepository($db);
$auth = new \forme\AuthService($db);


$pref = new \forme\UserPreferences($_COOKIE);



$bgcolor = $pref->getTheme() === 'dark' ? '#1a1a2e' : 'white';
$textcolor = $pref->getTheme() === 'dark' ? 'white' : 'black';


if(isset($_POST['LOGIN'])){
    header('Location: login.php');
    exit();
}



$TASKS=[];






if(isset($_POST['confFilter'])){
    $repo->setTags($_POST['tag'] ?? '');
}
if(isset($_POST['confSearch'])){
    $repo->setPattern($_POST['SEARCHr'] ?? '');
}

if(isset($_POST['deleteTASK'])){
   $repo->remove((int)$_POST["delete_id"]);
}
if(isset($_POST['CHANGEstatus'])){
    $repo->findAndChnageStatus((int)$_POST['change_id'],$_POST['change_status']);

}


if(isset($_POST['submit']) && !empty($_SESSION['user'])){
    $made_up_tags = $_POST["textTags"];
    if(isset($_POST['cycle'])){
        $tc  = new \forme\RecurringTask($repo, $_POST['title'] ?? '', $_SESSION['user']->getLogin() ?? '', $_POST['estimated_minutes'], $_POST['description'] ?? '', $_POST['priority'] ?? '', $_POST['status'] ?? '', $_POST['tags'] ?? '', $_POST['category'], $made_up_tags,$_POST['interval']);
        if (empty($tc->getErrorArray())) {
            $repo->add($tc);
            header('Location: index.php');
            exit();
        }
    }else {
        $tt = new \forme\Task($_POST['title'], $auth->currentUser(),$_POST['estimated_minutes'], $_POST['description'] ?? '', $_POST['priority'] ?? '', $_POST['status'], $_POST['tags'] ?? '', $_POST['category'], $made_up_tags);
        if (empty($tt->getErrorArray())) {
            $repo->add($tt);
            header('Location: index.php');
            exit();
        }

    }
    $TASKS = $repo->all();



}
$allTasksForStats = $repo->all();
$all_statuses  = array_column(array_map(fn($t) => ['status' => $t->getStatus()], $allTasksForStats), 'status');
$total_tasks = count($allTasksForStats);
$todo_count = count(array_keys($all_statuses, 'todo'));
$done_count = count(array_keys($all_statuses, 'done'));
$total_minutes = array_sum(array_map(fn($t) => $t->getEstimatedMinutes(), $allTasksForStats));






//stats



//short form of thingy


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="men.css" rel="stylesheet" type="text/css">
</head>

<?php if(empty($_SESSION['user'])): ?>
<form action="index.php" method="post">
    <div id="errorBox">  <?php echo "You are not logged in."; ?>
        <div>
        <button name="LOGIN"> GO TO LOGIN PAGE</button>
        </div>
    </div>
</form>

<?php else: ?>



<body bgcolor= "<?php echo $bgcolor?>" text= "<?php echo $textcolor ?>" >
<div id="WholeThing">
    <header>
        <div id="logo">
            <div id="men">Menedżer zadań</div>
            <div class="headerthingy"><a href="header.php" class="link">Prefferences</a></div>
            <div class="headerthingy">Zalogowany jako <?php echo  $auth->currentUser()?></div>
            <div class="headerthingy">Czas trwania sesji:<?php echo gmdate('H:i:s',$_SESSION['last_update_time'] - (int)$_SESSION['login_time'])." s" ?></div>
            <div class="headerthingy"><a class="link" href="logout.php">Wyloguj</a></div>
            <div class="headerthingy" id="32"><a class="link" href="smth">Wszystkie</a></div>
            <div class="headerthingy"><a href="ssdasda1" class="link">Do zrobienia</a></div>

            <div class="headerthingy"><a href="smtsdas2" class="link">W trakcie</a></div>
            <div class="headerthingy"><a href="smth3asdasd" class="link">Zakończone</a></div>
        </div><!-- end #logo -->
    </header>


    <aside>
        <?php if (isset($tt)  && !empty( $tt->getErrorArray())): ?>
            <ul>
                <?php foreach ( $tt->getErrorArray() as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (isset($tt) && empty($tt->getErrorArray())): ?>
            <p> Zadanie zostało dodane!</p>
        <?php endif; ?>


        <form id="form" method="post" action="index.php">
            <fieldset>
                <h3>Dodaj zadanie</h3>

                <label for="title">Tytuł zadania</label>
                <div>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
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
                        <option value="low" >Low</option>
                        <option value="medium">Medium</option>
                        <option value="high" >High</option>
                    </select>
                </div>

                <label for="status">Status</label>
                <div>
                    <select id="status" name="status">
                        <option value="todo">To do</option>
                        <option value="in progress" >In progress</option>
                        <option value="done" >Done</option>
                    </select>
                </div>

                <label for="estimated_minutes">Szacowany czas (minuty)</label>
                <div>
                    <input type="text" id="estimated_minutes" name="estimated_minutes" value="<?= htmlspecialchars($_POST['minutes'] ?? '') ?>">
                </div>

                <label for="Date">Data wykonania</label>
                <div>
                    <input type="date" id="Date" name="date">
                </div>

                <label for="des">Opis</label>
                <div>
                    <textarea id="des" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <label>Pilne</label>
                <input type="checkbox" name="tags[]" value="pilne">

                <label>Zespół</label>
                <input type="checkbox" name="tags[]" value="zespół">

                <label>Backend</label>
                <input type="checkbox" name="tags[]" value="backend">

                <label>Frontend</label>
                <input type="checkbox" name="tags[]" value="frontend">

                <hr>
                <label>Tagi</label>
                <div>
                    <textarea name="textTags"></textarea>
                </div>
                <br>

                <label>Cykliczne Zadanie</label>
                <input type="checkbox" name="cycle" value="true">

                <div>
                <label>Daily</label>
                <input type="radio" name="interval" value="daily">
                </div>
                    <div>
                        <label>Weekly</label>
                        <input type="radio" name="interval" value="weekly">
                    </div>
                    <div>
                        <label>Monthly</label>
                        <input type="radio" name="interval"value="monthly" >
                    </div>



                <div id="submit">
                    <button type="submit" name="submit">Dodaj zadanie</button>
                </div>
            </fieldset>
        </form>

        <form method="post" action="index.php">
            <br>
            <label>FILTRY</label>
            <hr>
            <div>
                <label>Pilne</label>
                <input type="radio" name="tag" value="pilne">

                <label>Zespół</label>
                <input type="radio" name="tag" value="zespół">

                <label>Backend</label>
                <input type="radio" name="tag" value="backend">

                <label>Frontend</label>
                <input type="radio" name="tag" value="frontend">
            </div>
            <button type="submit" name="confFilter">BIG BUTTON TEXT</button>
        </form>

        <hr>
        <form method="post" action="index.php">
            <label>Wyszukiwanie</label>
            <div>
                <textarea name="SEARCHr"></textarea>
            </div>
            <label>Confirm search</label>
            <div>
                <button type="submit" name="confSearch">BIG BUTTON NAME</button>
            </div>
        </form>
    </aside>

    <div id="frommain">
        <main>
            <div class="zadanie">
                <div class="leftside">
                    <p>ZADANIA DNIA</p>
                    <p>Wdrożenie nowego systemu logowania</p>
                    <p>Kategoria: Praca</p>
                    <div id="aaa"><p>ablablablaaaaaaaaaa</p></div>
                </div>
                <div class="rightside">
                    <p>Termin: 2026-04-10</p>
                    <div class="wysoki">Wysoki</div>
                    <div class="wtrakcie">W trakcie</div>
                </div>
            </div>

            <div id="topBoxes">
                <div class="box box1">
                    <h2><?php echo $total_tasks?></h2>
                    <p>Wszystkie</p>
                </div>

                <div class="box box2">
                    <h2><?php echo $todo_count ?></h2>
                    <p>Do zrobienia</p>
                </div>

                <div class="box box4">
                    <h2><?php echo $done_count ?></h2>
                    <p>Zakończone</p>
                </div>

                <div class="box box4">
                    <h2><?php echo $total_minutes ?></h2>
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
            if(isset($_POST['confFilter'])){
                $tag = $_POST['tag'] ?? '';
                if(!empty($tag)){
                    $displayTasks = $repo->filterByTag($tag);
                    $filterActive = true;
                }else {
                    $displayTasks = $repo->all();
                }
            }

            elseif(isset($_POST['confSearch'])){
                $pattern = $_POST['SEARCHr'] ?? '';
                if(!empty($pattern)){
                    $repo->setPattern($pattern);
                    $displayTasks = $repo->searchPattern();
                    $searchActive = true;
                }else {
                    $displayTasks = $repo->all();
                }
            }else {
                $displayTasks = $repo->all();
            }
            ?>



            <?php if ($displayTasks !== null): ?>
                <?php $displayTasks = array_slice($displayTasks, 0, $pref->getNum()); ?>

                <?php
                if($pref->getReq() === "title"){
                    usort($displayTasks, fn($a, $b) => strcmp($a->getTitle(), $b->getTitle()));
                } else if($pref->getReq() === "priority"){
                    $priorityOrder = ["low" => 0, "medium" => 1, "high" => 2];
                    usort($displayTasks, fn($a, $b) => $priorityOrder[$a->getPriority()] <=> $priorityOrder[$b->getPriority()]);
                } else if($pref->getReq() === "data"){
                    usort($displayTasks, fn($a, $b) => strtotime($a->getCreatedAt()) <=> strtotime($b->getCreatedAt()));
                }
                ?>

                <?php foreach ($displayTasks as $task): ?>
                    <div class="boxes">
                        <h2><?= $task->getTitle() ?></h2>
                        <p>Kategoria: <?= htmlspecialchars($task->getCategory()) ?></p>
                        <p>Priorytet: <?= $task->getPriority() ?></p>
                        <p>Opis: <?= $task->formatTaskDescription() ?></p>
                        <p>Tagi: <?php
                            $tags = $task->getTags();
                            if(!empty($tags)){
                                foreach($tags as $tag){
                                    echo $tag . " ";
                                }
                            } else {
                                echo "Brak";
                            }
                            ?></p>

                        <p>Status: <?php echo $task->getStatus() ?></p>
                        <p>Czas: <?php echo $task->getEstimatedMinutes() ?> min</p>
                        <p>Created by <?php echo htmlspecialchars($task->getCreatedBy()) ?></p>
                        <p>Created at <?php echo htmlspecialchars($task->getCreatedAt()) ?></p>
                        <?php if($task instanceof \forme\RecurringTask): ?>
                            <p>Następne wystąpienie: <?= $task->getNext() ?></p>
                        <?php endif; ?>

                        <form method="post" action="index.php">
                            <input type="hidden" name="delete_id" value="<?= $task->getId() ?>">

                                <input type="hidden" name="confFilter" value="1">
                                <input type="hidden" name="tag" value="<?=$_POST['tag'] ?? '' ?>">

                                <input type="hidden" name="confSearch" value="1">
                                <input type="hidden" name="SEARCHr" value="<?= $_POST['SEARCHr'] ?? '' ?>">

                            <button type="submit" name="deleteTASK">DELETE TASK</button>
                        </form>

                        <form action="index.php" method="post">
                            <select name="change_status">
                                <option value="todo" <?= $task->getStatus() == 'todo' ? 'selected' : '' ?>>To do</option>
                                <option value="in progress" <?= $task->getStatus() == 'in progress' ? 'selected' : '' ?>>In progress</option>
                                <option value="done" <?= $task->getStatus() == 'done' ? 'selected' : '' ?>>Done</option>
                            </select>
                            <input type="hidden" name="change_id" value="<?= htmlspecialchars($task->getId()) ?>">
                            <?php if(isset($_POST['confFilter'])): ?>
                                <input type="hidden" name="confFilter" value="1">
                                <input type="hidden" name="tag" value="<?= htmlspecialchars($_POST['tag'] ?? '') ?>">
                            <?php elseif(isset($_POST['confSearch'])): ?>
                                <input type="hidden" name="confSearch" value="1">
                                <input type="hidden" name="SEARCHr" value="<?= htmlspecialchars($_POST['SEARCHr'] ?? '') ?>">
                            <?php endif; ?>
                            <button type="submit" name="CHANGEstatus">CHANGE STATUS</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div>
                    <p>No task found</p>
                </div>
            <?php endif; ?>
            <?php endif; ?>
</html>
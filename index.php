<?php

$tasks = [
        [
                "title"=> "Wdrożenie nowego systemu logowania",
                "category"  => "Praca",
                "priority"   => "wysoki",
                "status"   => "w trakcie",
                "estimated_minutes" => 120,
                "tags"   => ["backend", "pilne"],
        ],
        ["title"   => "Zakupy spożywcze",
                "category" => "Dom",
                "priority"   => "niski",
                "status" => "do zrobienia",
                "estimated_minutes" => 45,
                "tags"      => ["dom", "zakupy"],
        ],
        [
                "title"     => "Nauka CSS – model pudełkowy",
                "category"  => "Nauka",
                "priority"   => "średni",
                "status"   => "do zrobienia",
                "estimated_minutes" => 60,
                "tags"     => ["frontend"],
        ],
        [
                "title"   => "Opłacić rachunki",
                "category" => "Dom",
                "priority"  => "wysoki",
                "status"   => "zakończone",
                "estimated_minutes" => 20,
                "tags"  => ["pilne", "dom"],
        ],
        [
                "title" => "Przegląd techniczny samochodu",
                "category"=> "Inne",
                "priority"  => "średni",
                "status"  => "w trakcie",
                "estimated_minutes" => 90,
                "tags"  => ["pilne"],
        ],
];

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
$success  = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_title = trim($_POST['title']  ?? '');
    $form_category = trim($_POST['category'] ?? '');
    $form_priority= trim($_POST['priority'] ?? '');
    $form_status = trim($_POST['status'] ?? '');
    $form_minutes = trim($_POST['estimated_minutes'] ?? '');
    $form_tags  = $_POST['tags'] ?? [];


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

        $clean_tags = array_values(array_filter($form_tags));
        sort($clean_tags);

        $tasks[] = ["title"=> $form_title,
                "category"=> $form_category,
                "priority" => $form_priority,
                "status" => $form_status,
                "estimated_minutes"=> (int)$form_minutes,
                "tags" => $clean_tags];

        $success = true;
        $form_title = "";
        $form_category= "Praca";
        $form_priority= "niski";
        $form_status = "do zrobienia";
        $form_minutes = "";
        $form_tags = [];
    }
}


//stats
$all_statuses  = array_column($tasks, 'status');
$total_tasks = count($tasks);
$todo_count= count(array_keys($all_statuses, 'do zrobienia'));
$done_count  = count(array_keys($all_statuses, 'zakończone'));
$total_minutes = array_sum(array_column($tasks, 'estimated_minutes'));


//short form of thingy

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="men.css" rel="stylesheet" type="text/css">
</head>



<body>
<div id="WholeThing">
    <header>
        <div id="logo">
            <div id="men">Menedżer zadań</div>

            <div class="headerthingy" id="32"><a class="link" href="smth">Wszystkie</a></div>
            <div class="headerthingy"><a href="ssdasda1" class="link">Do zrobienia</a></div>
            <div class="headerthingy"><a href="smtsdas2" class="link">W trakcie</a></div>
            <div class="headerthingy"><a href="smth3asdasd" class="link">Zakończone</a></div>
        </div>
    </header>

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

        <form id="form" method="post" action="index.php">
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
                        <option value="Dom" Dom</option>
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
                    <textarea id="des" name="description"></textarea>
                </div>

                <label>
                    <input type="checkbox" name="tags[]" value="pilne" >
                    Pilne
                </label>
                <label>
                    <input type="checkbox" name="tags[]" value="zespół" >
                    Zespół
                </label>
                <label>
                    <input type="checkbox" name="tags[]" value="backend" >
                    Backend
                </label>
                <label>
                    <input type="checkbox" name="tags[]" value="frontend" >
                    Frontend
                </label>

                <div id="submit">
                    <button type="submit">Dodaj zadanie</button>
                </div>
            </fieldset>
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

                <div class="box box5">
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

            <?php foreach ($tasks as $task): ?>
                <div class="boxes">
                    <h2><?= h($task['title']) ?></h2>
                    <p>Kategoria: <?= h($task['category']) ?></p>
                    <p>Priorytet: <?= h($task['priority']) ?></p>
                    <p>Status: <?= h($task['status']) ?></p>
                    <p>Czas: <?= h($task['estimated_minutes']) ?> min</p>
                    <p>Tagi: <?= h(implode(', ', $task['tags'])) ?></p>
                </div>
            <?php endforeach; ?>

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
<?php

require "../vendor/autoload.php";

use SeaBattle\Game\Game;


error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();


if (isset($_SESSION['game'])) {
    $game = unserialize($_SESSION['game']);
} else {
    $game = new Game();
}


$myField = $game->getMyField();
$enemyField = $game->getEnemyField();


if (isset($_POST['startNewGame'])) {
    $_SESSION = [];

    $game = new Game();
    $game->startNewGame();
}


if (isset($_GET['x']) && isset($_GET['y'])) {
    if (!$game->isGameover()) {
        $x = filter_input(INPUT_GET, 'x', FILTER_SANITIZE_NUMBER_INT);
        $y = filter_input(INPUT_GET, 'y', FILTER_SANITIZE_NUMBER_INT);
        $game->shootingTo($enemyField, $x, $y);
    }

    if (!$game->isGameover()) {
        $game->shootingTo($myField, null, null);
    }
}


$_SESSION['game'] = serialize($game);

?>




<!doctype html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="container">
        <h1>Sea Battle</h1>

        <?php
            if ($game->isGameover()) {
                switch($game->getWinner()) {
                    case Game::I_AM_WINNER:
                        echo '<h2 class="victory">You are a winner!</h2>';
                        break;
                    case Game::ENEMY_IS_WINNER:
                        echo '<h2 class="defeat">Enemy is a winner!</h2>';
                        break;
                }
            }
        ?>

        <div id="myFieldContainer">

            <h2>My ships</h2>

            <table id="myField">
                <?php $myField->draw(); ?>
            </table>
        </div>


        <div id="enemyFieldContainer">

            <h2>Enemy's ships</h2>

            <table id="enemyField">
                <?php $enemyField->draw(); ?>
            </table>
        </div>


        <form id="startGameForm" method="post" action="">
            <input type="hidden" name="startNewGame" value="true">
            <button id="startGame">New Game</button>
        </form>

    </div>


    <script   src="https://code.jquery.com/jquery-2.2.3.min.js" integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>

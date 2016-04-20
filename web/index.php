<?php

require "../vendor/autoload.php";

use SeaBattle\Game\Game;
use SeaBattle\Field\Field;


error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();


if (isset($_SESSION['game'])) {
    $game = unserialize($_SESSION['game']);
} else {
    $game = new Game();
}


if (isset($_GET['startNewGame'])) {
    $_SESSION = [];

    $game = new Game();
    $game->startNewGame();
}


$myField = $game->getMyField();
$enemyField = $game->getEnemyField();

if (isset($_GET['x']) && isset($_GET['y'])) {
    if (!$game->isGameover()) {
        $x = filter_input(INPUT_GET, 'x', FILTER_SANITIZE_NUMBER_INT);
        $y = filter_input(INPUT_GET, 'y', FILTER_SANITIZE_NUMBER_INT);
        $game->shootingTo($enemyField, $x, $y);
    }

    if (!$game->isGameover()) {
        $shootingAI = $enemyField->getShootingAI();
        $coords = $shootingAI->calculateCoordsForShooting($myField->getSlots());

        /*$x = mt_rand(0, Field::WIDTH - 1);
        $y = mt_rand(0, Field::HEIGT - 1);*/
        $game->shootingTo($myField, $coords['x'], $coords['y'], true);
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

        <div id="myFieldContainer">

            <h2>My ships</h2>

            <table id="myField">
                <?php $myField->draw(); ?>
            </table>
        </div>


        <div id="enemyFieldContainer">

            <h2>Enemy's ships</h2>

            <table id="enemyField">
                <?php $enemyField->draw(true); ?>
            </table>
        </div>


        <?php
        if ($game->isGameover()) {
            switch($game->getWinner()) {
                case Game::I_AM_WINNER:
                    echo '<h1 id="victory">You are the winner!</h1>';
                    break;
                case Game::ENEMY_IS_WINNER:
                    echo '<h1 id="defeat">Enemy is the winner!</h1>';
                    break;
            }
        }
        ?>


        <form id="startGameForm" method="get" action="">
            <input type="hidden" name="startNewGame" value="true">
            <button id="startGame">New Game</button>
        </form>

    </div>


    <script   src="https://code.jquery.com/jquery-2.2.3.min.js" integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>

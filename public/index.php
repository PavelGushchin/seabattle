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


if (isset($_GET['startNewGame'])) {
    $_SESSION = [];

    $game = new Game();
    $game->startNewGame();
}


$myField = $game->getMyField();
$enemyField = $game->getEnemyField();


if (isset($_GET['x']) && isset($_GET['y'])) {
    if (!$game->isGameover() && $game->getTurn() === Game::MY_TURN) {
        $x = intval($_GET['x']);
        $y = intval($_GET['y']);

        $shipWasHit = $game->shootingTo($enemyField, $x, $y);

        if ($shipWasHit) {
            $game->setTurn(Game::MY_TURN);
        } else {
            $game->passTurnToNextPlayer();
        }
    }

    if (!$game->isGameover()) {
        $shootingAI = $enemyField->getShootingAI();

        while ($game->getTurn() === Game::ENEMY_TURN) {
            $coords = $shootingAI->calculateCoordsForShooting(
                $myField->getSlots(),
                $myField->getShips()
            );

            $shipWasHit = $game->shootingTo($myField, $coords['x'], $coords['y'], true);

            if ($shipWasHit) {
                $game->setTurn(Game::ENEMY_TURN);
            } else {
                $game->passTurnToNextPlayer();
            }
        }
    }
}


if (isset($_GET['autobattle'])) {

    set_time_limit(0);

    $numberOfGames = filter_input(INPUT_GET, 'autobattle', FILTER_SANITIZE_NUMBER_INT);
    $currentGame = 1;

    $firstAlgorithmWins = 0;
    $secondAlgorithmWins = 0;

    $firstAlgorithmTotalShots = 0;
    $secondAlgorithmTotalShots = 0;

    while($currentGame <= $numberOfGames) {

        $game = new Game();
        $game->startAutobattleGame();

        $myField = $game->getMyField();
        $enemyField = $game->getEnemyField();

        while(!$game->isGameover()) {
            while (!$game->isGameover() && $game->getTurn() === Game::MY_TURN) {
                $shootingAI = $myField->getShootingAI();
                $coords = $shootingAI->calculateCoordsForShooting(
                    $enemyField->getSlots(),
                    $enemyField->getShips()
                );

                $shipWasHit = $game->shootingTo($enemyField, $coords['x'], $coords['y']);

                if ($shipWasHit) {
                    $game->setTurn(Game::MY_TURN);
                } else {
                    $game->passTurnToNextPlayer();
                }
            }

            while (!$game->isGameover() && $game->getTurn() === Game::ENEMY_TURN) {
                $shootingAI = $enemyField->getShootingAI();
                $coords = $shootingAI->calculateCoordsForShooting(
                    $myField->getSlots(),
                    $myField->getShips()
                );

                $shipWasHit = $game->shootingTo($myField, $coords['x'], $coords['y'], true);

                if ($shipWasHit) {
                    $game->setTurn(Game::ENEMY_TURN);
                } else {
                    $game->passTurnToNextPlayer();
                }
            }
        }


        switch($game->getWinner()) {
            case Game::I_AM_WINNER:
                $firstAlgorithmWins++;
                break;
            case Game::ENEMY_IS_WINNER:
                $secondAlgorithmWins++;
                break;
        }

        $firstAlgorithmTotalShots += $enemyField->getTotalShots();
        $secondAlgorithmTotalShots += $myField->getTotalShots();

        $currentGame++;
    }

    $firstAlgorithmAverageShotsToWin = $firstAlgorithmTotalShots / $numberOfGames;
    $secondAlgorithmAverageShotsToWin = $secondAlgorithmTotalShots / $numberOfGames;

    echo "{$myField->getShootingAI()}: wins - $firstAlgorithmWins<br>";

    echo "{$enemyField->getShootingAI()}: wins - $secondAlgorithmWins<br>";
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
            switch ($game->getWinner()) {
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


    <script src="js/jquery-2.2.3.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>

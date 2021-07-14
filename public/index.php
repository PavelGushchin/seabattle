<?php

require "../vendor/autoload.php";

use SeaBattle\Game;


session_start();

if (isset($_SESSION['game'])) {
    $game = unserialize($_SESSION['game']);
} else {
    $game = new Game();
}

if (isset($_GET['start_new_game'])) {
    $game->startNewGame();
}

if (isset($_GET['autobattle'])) {
    $game->startAutobattle();
}

$game->play();

$_SESSION['game'] = serialize($game);

?>


<!doctype html>
<html lang="en">

    <head>
        <title>Sea Battle</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div id="container">
            <h1>Sea Battle</h1>
    
            <div id="mainBoardContainer">
                <h2>My ships</h2>
    
                <div id="mainBoard">
                    <?php $game->getMyPlayer()->printMainBoard(); ?>
                </div>
            </div>
    
            <div id="shootingBoardContainer">
                <h2>Enemy's ships</h2>
    
                <div id="shootingBoard">
                    <?php $game->getMyPlayer()->printShootingBoard(); ?>
                </div>
            </div>

            <?php
                $theWinner = $game->getTheWinner();

                if ($theWinner === Game::I_AM_THE_WINNER) {
                    echo "<h1 id='victory'>You are the winner!</h1>";
                } elseif ($theWinner === Game::ENEMY_IS_THE_WINNER) {
                    echo "<h1 id='defeat'>Enemy is the winner!</h1>";
                }
            ?>
    
            <form method="get">
                <input type="hidden" name="start_new_game" value="true">
                <button id="new-game__button">New Game</button>
            </form>
        </div>

        <script src="js/scripts.js"></script>
    </body>
</html>

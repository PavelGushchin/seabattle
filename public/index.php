<?php

require "../vendor/autoload.php";

use SeaBattle\Game;


session_start();

if (! isset($_SESSION["game"])) {
    $game = new Game();
} else {
    $game = unserialize($_SESSION["game"]);
}

if (isset($_GET['start_new_game'])) {
    $game->startNewGame();
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
    
            <div id="shipBoardContainer">
                <h2>My ships</h2>
    
                <div id="shipBoard">
                    <?php
                        echo $game->getMyPlayer()->getShipBoard()->print();
                    ?>
                </div>
            </div>
    
            <div id="shootingBoardContainer">
                <h2>Enemy's ships</h2>
    
                <div id="shootingBoard">
                    <?php
                        echo $game->getMyPlayer()->getShootingBoard()->print();
                    ?>
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

        <script src="js/main.js"></script>
    </body>
</html>

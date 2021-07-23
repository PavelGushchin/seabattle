<?php

require "../vendor/autoload.php";

use SeaBattle\Game;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\MyPlayer;


session_start();

if (! isset($_SESSION["game"])) {
    $game = new Game();
} else {
    $game = unserialize($_SESSION["game"]);
}

if (isset($_GET['start_new_game'])) {
    $game->startNewGame();
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

        if ($game->getTheWinner() === Game::I_AM_THE_WINNER) {
            $firstAlgorithmWins++;
        } elseif ($game->getTheWinner() === Game::ENEMY_IS_THE_WINNER) {
            $secondAlgorithmWins++;
        } else {
            throw new Exception("No winner!");
        }

        $currentGame++;
    }

    echo "First algorithm wins - $firstAlgorithmWins<br>";
    echo "Second algorithm wins - $secondAlgorithmWins<br>";
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
                        echo $game->getMyPlayer()->printShipBoard();
                    ?>
                </div>
            </div>
    
            <div id="shootingBoardContainer">
                <h2>Enemy's ships</h2>
    
                <div id="shootingBoard">
                    <?php
                        echo $game->getMyPlayer()->printShootingBoard();
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

        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>

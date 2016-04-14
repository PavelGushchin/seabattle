<?php

require "../vendor/autoload.php";

use SeaBattle\Game\Game;
use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();

$game = isset($_SESSION['game'])
    ? unserialize($_SESSION['game'])
    : new Game();

$myField = isset($_SESSION['myField'])
    ? unserialize($_SESSION['myField'])
    : new Field();

$enemyField = isset($_SESSION['enemyField'])
    ? unserialize($_SESSION['enemyField'])
    : new Field();


//$myField->getSlot(3,4)->setState(Slot::THERE_IS_A_SHIP);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['startNewGame'])) {
    $game = new Game();

    $myField = new Field();
    $myField->locateShips();

    $enemyField = new Field();
    $enemyField->locateShips();

    $game->setRunning(true);

    $_SESSION['game'] = serialize($game);
    $_SESSION['myField'] = serialize($myField);
    $_SESSION['enemyField'] = serialize($enemyField);

//    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//    exit;
}

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
                <?php $enemyField->draw(); ?>
            </table>
        </div>

        <!--<div id="myShips">
            <h3>Locate your ships:</h3>
            <ul>
                <li id="smallShip">Small ship</li>
                <li id="mediumShip">Medium ship</li>
                <li id="bigShip">Big ship</li>
                <li id="largeShip">Large ship</li>
            </ul>
        </div>-->

        <form id="startGameForm" method="post" action="">
            <input type="hidden" name="startNewGame" value="true">
            <button id="startGame">New Game</button>
        </form>

    </div>


    <script   src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>

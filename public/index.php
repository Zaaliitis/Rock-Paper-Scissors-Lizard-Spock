<?php
require_once 'app/Models/Element.php';
require_once 'app/Models/Player.php';
require_once 'app/Models/Enemy.php';
require_once 'app/Core/Game.php';

$elements = [
    "rock" => new Element("Rock", ["scissors", "lizard"]),
    "paper" => new Element("Paper", ["rock", "spock"]),
    "scissors" => new Element("Scissors", ["paper", "lizard"]),
    "lizard" => new Element("Lizard", ["paper", "spock"]),
    "spock" => new Element("Spock", ["rock", "scissors"]),
];

$enemies = [
    'Sheldon Cooper',
    'Mario',
    'Daenerys Targaryen',
    'Chandler Bing',
    'Michael Scott',
    'Gandalf',
    'Rick Sanchez',
    'Darth Vader',
    'Batman'
];

echo "Let's play a game! 
The game is Rock, Paper, Scissors, Lizard, Spock!
Rules:
*Scissors cuts paper
*Paper covers rock
*Rock crushes lizard
*Lizard poisons Spock 
*Spock smashes Scissors
*Scissors decapitates Lizard
*Lizard eats Paper 
*Paper disproves Spock
*Spock vaporizes Rock
*(and as it always has) Rock crushes Scissors" . PHP_EOL;

$game = new Game();
$player = new Player(readline("Enter your name: "));

$playerCount = $game->getPlayerCount();
$rounds = $game->getRoundCount();

$chosenEnemies = $game->getEnemies($enemies, $playerCount);
echo "You're playing against: " . implode(", ", $chosenEnemies) . PHP_EOL;

$wins = $game->storeWins($chosenEnemies, $player);

$playedMatches = [];
for ($round = 1; $round <= $rounds; $round++) {
    echo PHP_EOL . "Round $round:" . PHP_EOL;
    $game->playRound($player, $chosenEnemies, $elements, $wins, $playerCount, $playedMatches);
}

echo PHP_EOL . "Final Results:" . PHP_EOL;
echo "Player: {$player->getName()} - Score: " . $player->getScore() . PHP_EOL;
foreach ($chosenEnemies as $enemy) {
    echo "$enemy - Score: " . $wins[$enemy]->getScore() . PHP_EOL;
}

$winners = $game->getWinners($wins);
echo $winners;

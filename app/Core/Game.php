<?php
require_once 'app/Models/Player.php';

class Game
{
    public function getPlayerCount(): int
    {
        $playerCount = readline("Enter the number of computer players (1-9): ");
        if (!is_numeric($playerCount) || $playerCount < 1 || $playerCount > 9) {
            echo "Invalid input. Please enter a number between 1 and 9." . PHP_EOL;
            return $this->getPlayerCount();
        }

        return $playerCount;
    }

    public function getRoundCount(): int
    {
        $rounds = readline("Enter the number of rounds to play (1-5): ");
        if (!is_numeric($rounds) || $rounds < 1 || $rounds > 5) {
            echo "Invalid input. Please enter a number between 1 and 5." . PHP_EOL;
            return $this->getRoundCount();
        }
        return $rounds;
    }

   public function getEnemies(array $enemies, int $playerCount): array
    {
        $chosenEnemies = array_rand(array_flip($enemies), $playerCount);
        if (!is_array($chosenEnemies)) {
            $chosenEnemies = [$chosenEnemies];
        }
        return $chosenEnemies;
    }

    public function storeWins($chosenEnemies, $player): array
    {
        $wins = [];
        foreach ($chosenEnemies as $enemy) {
            $wins[$enemy] = new Enemy($enemy);
        }
        $wins[$player->getName()] = $player;
        return $wins;
    }

    public function playRound($player, $chosenEnemies, $elements, $wins, $playerCount, &$playedMatches): void
    {
        $playerChoice = $this->getPlayerChoice($player, $elements);
        $playedMatches[] = $this->playerVsComputer($chosenEnemies, $wins, $elements, $playerChoice, $player);
        $playedMatches[] = $this->computerVsComputer($playerCount, $chosenEnemies, $wins, $elements);
    }

    public function getWinners(array $wins): string
    {
        $maxScore = max(array_map(function ($enemy) {
            return $enemy->getScore();
        }, $wins));

        $winners = [];
        foreach ($wins as $enemy) {
            if ($enemy->getScore() === $maxScore) {
                $winners[] = $enemy->getName();
            }
        }

        if ($maxScore > 0) {
            if (count($winners) === 1) {
                return PHP_EOL . "The winner is: " . implode(", ", $winners) . "!" . PHP_EOL;
            } else {
                return PHP_EOL . "Shared first place winners are: " . implode(", ", $winners) . "!" . PHP_EOL;
            }
        } else {
            return "It's a draw!" . PHP_EOL;
        }
    }

    private function playerVsComputer($chosenEnemies, $wins, $elements, $playerChoice, $player): array
    {
        $playedMatch = null;
        foreach ($chosenEnemies as $enemyName) {
            $enemy = $wins[$enemyName];
            $computerChoice = array_rand($elements);
            echo "$enemyName chose $computerChoice" . PHP_EOL;

            if ($playerChoice == $computerChoice) {
                echo "It's a draw!" . PHP_EOL;
            } elseif (in_array($computerChoice, $elements[$playerChoice]->beats)) {
                echo "Congratulations, {$player->getName()}! You won this round against $enemyName." . PHP_EOL;
                $player->incrementScore();
            } else {
                echo "You lost this round against $enemyName." . PHP_EOL;
                $enemy->incrementScore();
            }

            $playedMatch = [$player->getName(), $enemyName];
        }
        return $playedMatch;
    }

    private function computerVsComputer($playerCount, $chosenEnemies, $wins, $elements): array
    {
        $playedMatch = [];
        if ($playerCount > 1) {
            $matchedEnemies = $chosenEnemies;
            shuffle($matchedEnemies);

            while (count($matchedEnemies) > 1) {
                $enemy1 = array_shift($matchedEnemies);
                $enemy1Object = $wins[$enemy1];

                foreach ($matchedEnemies as $enemy2) {
                    $enemy2Object = $wins[$enemy2];

                    $computerChoice1 = array_rand($elements);
                    $computerChoice2 = array_rand($elements);
                    echo "$enemy1 chose $computerChoice1, $enemy2 chose $computerChoice2" . PHP_EOL;

                    if ($computerChoice1 == $computerChoice2) {
                        echo "It's a draw between $enemy1 and $enemy2!" . PHP_EOL;
                    } elseif (in_array($computerChoice2, $elements[$computerChoice1]->beats)) {
                        echo "Congratulations, $enemy1! You won this round against $enemy2." . PHP_EOL;
                        $enemy1Object->incrementScore();
                    } else {
                        echo "Congratulations, $enemy2! You won this round against $enemy1." . PHP_EOL;
                        $enemy2Object->incrementScore();
                    }

                    $playedMatch = [$enemy1, $enemy2];
                }
            }
        }
        return $playedMatch;
    }

   private function getPlayerChoice($player, $elements): string
    {
        $playerChoice = strtolower(readline("{$player->getName()}, what's your choice? :"));

        if (!array_key_exists($playerChoice, $elements)) {
            echo "Invalid choice. Try again!" . PHP_EOL;
            return $this->getPlayerChoice($player, $elements);
        }

        return $playerChoice;
    }
}
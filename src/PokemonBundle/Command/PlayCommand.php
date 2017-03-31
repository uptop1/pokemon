<?php
/**
 * Pokemon RPG
 * @author Mostafa Ameen <admin@uptop1.com>
 */

declare(strict_types=1);

namespace PokemonBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pokemon\PokemonGame;
use PokemonBundle\Entity\Pokemon;

/**
 * This class implements PokemonGame as console command.
 */
class PlayCommand extends ContainerAwareCommand
{
    protected $io;
    protected $game;

    /**
     * Set command name and arguments
     */
    protected function configure()
    {
        $this->setName('play');
        $this->setDescription('Start new game, or resume a game');

        $this->addArgument('username', InputArgument::OPTIONAL);
    }

    /**
     * Runs on command execution
     * @param InputInterface $input Inputs from CLI
     * @param OutputInterface $output CLI output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $this->game = new PokemonGame($doctrine);

        $this->io->writeln([
            'Pokemon RPG (BETA)',
            '============',
            '',
        ]);


        $helper = $this->getHelper('question');

        $username = $input->getArgument('username');
        
        if ($username){
            // Resume game
            $this->resume($username);
        }else{
            // New game
            $username = $this->io->ask("Hello there! What is your name?", "", function ($answer) {
                $answer = trim($answer);

                if (!$this->game->isValidName($answer)){
                    throw new \RuntimeException(
                        'Name should be at least 3 characters.'
                    );
                }

                return $answer;
            });

            $this->newGame($username);      
        }
    }

    /**
     * Start new game
     * @param string $username 
     */
    protected function newGame(string $username){
        $this->io->writeln("Welcome <comment>{$username}</comment> to Pokemon RPG, hope you will enjoy playing it.\n");

        $status = $this->game->newGame($username);

        switch ($status) {
            case -1: // Duplicate user
                $this->io->warning("We found a player named $username already.");

                $resumeGame = $this->io->confirm('Do you want to resume saved game?', true);

                if ($resumeGame){
                    $this->resume($username);
                }else{
                    $username = $this->io->ask("I accept nicknames too, what is your nickname?", "", function ($answer) {
                        $answer = trim($answer);

                        if (!$this->game->isValidName($answer)){
                            throw new \RuntimeException(
                                'Name should be at least 3 characters.'
                            );
                        }

                        return $answer;
                    });

                    $this->newGame($username); 
                }
                break;
            
            case 0: // Failure
                $this->io->error("Something wrong happened. Please try again later");
                die;
                break;
            
            case 1: // Success
                $this->choosePokemon($username);

                $this->info();

                $this->whatToDo();
                break;
        }
    }

    /**
     * Ask the player to choose their first pokemon
     * @param string $username The name of the player
     */
    protected function choosePokemon(string $username){

        $this->io->writeln("<info>Professor Oak</info>: Greetings <comment>{$username}</comment>. You are now eligable for training Pokemons, but you have to reach level 10 to be qualified for the Johto league.");

        $pokemons = $this->game->getAvailablePokemons();

        $choice = $this->io->choice('<info>Professor Oak</info>: You need to pick your first pokemon to start. Which one do you prefer? Choose carefully', $pokemons);

        $pokemonName = $pokemons[$choice];

        $this->io->writeln("<info>Professor Oak</info>: Great, <comment>{$pokemonName}</comment> is an excellent match for you. You need to train your pokemon to get it stronger. Choose your battles wisely so you can won them, get more experience, and then join the Johto leage in time.\n");

        $this->game->setPokemon($choice);
        $this->save();
    }

    /**
     * Load saved game
     * @param string $username 
     */
    protected function resume(string $username){

        $status = $this->game->resumeGame($username);

        switch ($status) {
            case -1: // User does not exist
                $this->io->writeln("There is no player named <comment>{$username}</comment> matching our records!");
                $isNewGame = $this->io->confirm('Do you want to start a new game?', true);

                if ($isNewGame){
                    $this->newGame($username);
                }else{

                    $username = $this->io->ask("I'm going to search further for you, what is your name again?", "", function ($answer) {
                        $answer = trim($answer);

                        if (!$this->game->isValidName($answer)){
                            throw new \RuntimeException(
                                'Name should be at least 3 characters.'
                            );
                        }

                        return $answer;
                    });

                    $this->resume($username);
                }
                break;
            
            case 0: // Failure
                $this->io->error("Something wrong happened. Please try again later");
                die;
                break;
            
            case 1: // Success
            case 2: // No pokemon chosed yet
                $this->io->writeln("Welcome back <comment>{$username}</comment>.\n");

                if ($status == 2){
                    $this->choosePokemon($username);
                }

                $this->info();

                $this->whatToDo();
                break;
        }


    }

    /**
     * Ask for action decision repeatedly
     */
    protected function whatToDo(){
        $choice = $this->io->choice('What do you want to do now?', array('i'=>'Show player info', 'p'=>'My pokemon info', 'e'=>'Explore nearby places', 's'=>'Save progress', 'x'=>'Exit the game'));

        switch($choice){
            case 'i': // Show player info
                $this->info();
                break;

            case 'p': // Show pokemon info
                $this->io->writeln("My Pokemon Info");
                $this->pokemonInfo();
                break;
            
            case 'e': // Explore nearby places
                $this->whereToGo();
                break;
            
            case 's': // Save progress
                $this->save();
                break;

            case 'x': // Exit the game
                $save = $this->io->confirm('Do you want to save before exitting?', true);
                if ($save){
                    $this->save();
                }

                $this->io->writeln("Bye Bye!");
                exit;
                break;
        }

        $this->whatToDo();
    }

    /**
     * Display the places once the player decides to explore nearby places
     */
    protected function whereToGo(){
        $places = $this->game->getPlaces();

        $choices = array();
        foreach($places as $k=>$place){
            $choices[$k+1] = $place->getName();
        }
        $choices['b'] = "< Go back";

        $choice = $this->io->choice('Where do you want to go?', $choices);

        if ($choice == "b") return;

        $place = $places[$choice-1];
        $this->io->section($place->getName());

        $pokemons = $this->game->getPlacePokemons($place->getId());

        $choices = array();
        foreach($pokemons as $k=>$pokemon){
            $choices[$k+1] = $pokemon->getName() . ' (' . $pokemon->getStamina() . ')';
        }
        $choices['b'] = "< Go back";

        $choice = $this->io->choice('There are plenty of pokemons nearby, which one do you want to battle?', $choices);

        if ($choice == "b") return;

        $pokemon = $pokemons[$choice-1];

        $this->battle($pokemon);
    }

    /**
     * Display the pokemons in the selected place
     */
    protected function showPokemons($place){
        $places = $this->game->getPlaces();

        $choices = array();
        foreach($places as $k=>$place){
            $choices[$k+1] = $place->getName();
        }
        $choices['b'] = "< Go back";

        $choice = $this->io->choice('Where do you want to go?', $choices);

        if ($choice == "b") return;

        $place = $places[$choice-1];
        $this->io->section($place->getName());

        $pokemons = $this->game->getPlacePokemons($place->getId());

        $choices = array();
        foreach($pokemons as $k=>$pokemon){
            $choices[$k+1] = $pokemon->getName() . ' (' . $pokemon->getStamina() . ')';
        }
        $choices['b'] = "< Go back";

        $choice = $this->io->choice('There are plenty of pokemons nearby, which one do you want to battle?', $choices);

        if ($choice == "b") return;

        $pokemon = $pokemons[$choice-1];

        $this->battle($pokemon);
    }

    /**
     * Start fight with a pokemon
     * @param Pokemon $opponent The pokemon to battle with
     */
    protected function battle(Pokemon $opponent){
        $this->io->section("Batteling with " . $opponent->getName());
        $this->pokemonInfo($opponent);

        $playerInfoBefore = $this->game->getCurrentPlayerInfo();
        $wins = $this->game->battle($opponent);

        if ($wins){
            $xpGained = PokemonGame::WIN_XP;
            $this->io->success("Congratulations, You won the battle and gained $xpGained XP!");
            
            $currentPlayerInfo = $this->game->getCurrentPlayerInfo();

            if ($playerInfoBefore['level'] != $currentPlayerInfo['level']){
                $this->onLevelUp($currentPlayerInfo['level']);
            }
        }else{
            $this->io->warning("This " . $opponent->getName() . " was too strong, unfortunately you lost the battle! You should check the pokemon strength and type, they really matter.");
        }
    }

    /**
     * Runs once the user level is increased
     */
    protected function onLevelUp(int $level){
        $this->io->writeln("Awesome! you are now at level <comment>{$level}</comment>. Your pokemon strength has been raised.");

        if ($level >= 10){
            $this->io->success("Fantastic! you are now officially qualified for joining the Johto league.");
        }
    }

    /**
     * Display current player info
     */
    protected function info(){
        $this->io->writeln("Player Info");

        $playerInfo = $this->game->getCurrentPlayerInfo();

        $this->io->table(
            array('Name', $playerInfo['name']),
            array(
                array('Level', $playerInfo['level']),
                array('XP', $playerInfo['xp']),
            )
        );
    }

    /**
     * Display info of the given pokemon
     * @param Pokemon $pokemon The pokemon to show it's details. if ommitted, will get current player pokemon
     */
    protected function pokemonInfo(Pokemon $pokemon = null){

        if (!$pokemon){
            $pokemonInfo = $this->game->getCurrentPokemonInfo();
        }else{
            $pokemonInfo = $this->game->getPokemonInfo($pokemon);
        }

        $this->io->table(
            array('Name', $pokemonInfo['name']),
            array(
                array('Type', $pokemonInfo['type']),
                array('Stamina', $pokemonInfo['stamina']),
                array('Attack', $pokemonInfo['attack']),
                array('Defense', $pokemonInfo['defense']),
            )
        );
    }

    /**
     * Save current porgress to the database
     */
    protected function save(){
        if ($this->game->save())
            $this->io->writeln("Progress saved.\n");
        else
            $this->io->error("Unable to save the progress now. please try again later.\n");
    }
}
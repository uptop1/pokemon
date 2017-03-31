<?php
/**
 * Pokemon RPG
 * @author Mostafa Ameen <admin@uptop1.com>
 */

declare(strict_types=1);

namespace Pokemon;

use Doctrine\ORM\Query;

use PokemonBundle\Entity\Pokemon;
use PokemonBundle\Entity\Player;
use PokemonBundle\Entity\Place;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * This class is the core of Pokemon RPG. It can be used to implement console application, API, or web application.
 */
class PokemonGame
{
    protected $player;
    protected $doctrine;

    const WIN_XP = 100;

    /**
     * Instantiate the game
     * @param Registry $doctrine Doctrine registry object
     */
    public function __construct(Registry $doctrine){
        $this->doctrine = $doctrine;
    }

    /**
     * Start new game
     * @param string $username The name of the player
     * @return int 0=Failure, 1=Success, -1=Duplicate user
     */
    public function newGame(string $username):int {
        $username = trim($username);
        if (!self::isValidName($username)) return 0;


        try{
            $player = $this->doctrine
                ->getRepository('PokemonBundle:Player')
                ->findOneBy(array('username'=>$username));

            if ($player) return -1;

            $this->player = new Player;
            $this->player->setUsername($username);

            $em = $this->doctrine->getManager();
            $em->persist($this->player);

            $this->save();

        }catch(UniqueConstraintViolationException $ex){

            return -1;

        }catch(\Exception $ex){

            return 0;

        }

        return 1;
    }

    /**
     * Check whether the given name is valid or not
     * @param string $username The name of the player
     * @return bool True when valid, false otherwise
     */
    public static function isValidName($username):bool {
        return strlen(trim($username))>=3;
    }

    /**
     * Get the pokemons available to be selected as first pokemon
     * @return array Available pokemons
     */
    public function getAvailablePokemons():array {
        return ["b"=>"Bulbasaur", "c"=>"Charmander", "s"=>"Squirtle"];
    }

    /**
     * Set the pokemon the player chose
     * @param string First character of the choosen pokemon
     * @return bool True on success, false otherwise
     */
    public function setPokemon(string $pokemonChoice):bool {
        if (!$this->player) return false;

        $pokemon_id = 0;
        switch(strtolower($pokemonChoice)){
            case 'b': // Bulbasaur
                $pokemon_id = 1;
                break;
            case 'c': // Charmander
                $pokemon_id = 4;
                break;
            case 's': // Squirtle
                $pokemon_id = 7;
                break;
            default:
                return false;        
        }

        try{
            $pokemon = $this->doctrine
                ->getRepository('PokemonBundle:Pokemon')
                ->findOneBy(array('id'=>$pokemon_id));

            $pokemonName = $pokemon->getName();

            $this->player->setPokemon($pokemon);

            $em = $this->doctrine->getManager();
            if (!$em->isOpen()){
                $em = $this->doctrine->resetManager();
            }

            $em->merge($this->player);

            $this->save();

            return true;
        }catch(\Exception $ex){
            return false;
        }
    }

    /**
     * Load saved game
     * @param string $username The name of the player
     * @return int 0=Failure, 1=Success, 2=No pokemon chosed yet, -1=User does not exist
     */
    public function resumeGame(string $username):int {
        $username = trim($username);
        if (!self::isValidName($username)) return 0;

        try{
            $player = $this->doctrine
                ->getRepository('PokemonBundle:Player')
                ->findOneBy(array('username'=>$username));

            if (!$player) return -1;
            $this->player = $player;

            $pokemon = $this->player->getPokemon();

            if (!$pokemon){
                return 2;
            }

            $em = $this->doctrine->getManager();
            if (!$em->isOpen()){
                $em = $this->doctrine->resetManager();
            }

            $em->merge($this->player);

            return 1;
        }catch(\Exception $ex){
            return 0;
        }
    }

    /**
     * Get the nearby places
     * @return array Places ex.[["id"=>1,"name"=>"Pinwheel Forest"], ["id"=>2,"name"=>"The Volcanic Eifel"]]
     */
    public function getPlaces():array {
        try{
            //TODO: These data should be cached
            $places = $this->doctrine
                ->getRepository('PokemonBundle:Place')
                ->findAll();

            return $places;
        }catch(\Exception $ex){
            return [];
        }
    }

    /**
     * Get the nearby pokemons
     * @param int The id of the place to search for pokemons
     * @return array Pokemons
     */
    public function getPlacePokemons(int $placeId):array {
        try{
            //TODO: These data should be cached
            $place = $this->doctrine
                ->getRepository('PokemonBundle:Place')
                ->findOneBy(array('id'=>$placeId));

            if (!$place) throw new Exception("Invalid place!");

            $types = $place->getType();
            $typesIDs = array();
            foreach($types as $type){
                $typesIDs[] = $type->getId();
            }

            //TODO: This query should return only 5 random pokemons in this area instead of retreiving all and then selecting 5 randomly
            $query = $this->doctrine->getRepository('PokemonBundle:Pokemon')->createQueryBuilder('p');
            $query->join('p.type', 'pt')
                  ->join('pt.place', 'pl')
                  ->where($query->expr()->eq('pl.id', $place->getId()));
            $pokemonsAll = $query->getQuery()->getResult();
            $randomPokemons = array_rand($pokemonsAll, 5);

            $pokemons = [];
            foreach($randomPokemons as $randomIndex){
                $pokemons[] = $pokemonsAll[$randomIndex];
            }

            return $pokemons;
        }catch(\Exception $ex){
            return [];
        }
    }

    /**
     * Start fight with a pokemon
     * @param Pokemon $opponent The pokemon to battle with
     * @return bool True when the player wins, false otherwise
     */
    public function battle(Pokemon $opponent):bool {
        if (!$this->player) return false;

        try{
            $level = $this->player->getLevel();
            $playerPokemon = $this->player->getPokemon();

            // Simple way to put the level and pokemons types in mind
            $playerStamina = $playerPokemon->getStamina($level);
            $opponentStamina = $opponent->getStamina();

            $attack = $this->doctrine
                ->getRepository('PokemonBundle:PokemonVsPokemon')
                ->findOneBy(array('attackerPokemonId'=>$playerPokemon->getId(),'defenderPokemonId'=>$opponent->getId()));

            $attackRatio = 1;

            if ($attack) $attackRatio = $attack->getRatio();

            $playerStamina *= $attackRatio;

            $wins = $playerStamina>=$opponentStamina;

            if ($wins){
                $xpGained = self::WIN_XP;
                $this->player->setXp($this->player->getXp() + $xpGained);
                $newLevel = min(10, 1 + ceil($this->player->getXp()/300));
                if ($level != $newLevel){
                    $this->player->setLevel($newLevel);
                }
            }

            return $wins;
        }catch(\Exception $ex){
            return false;
        }
    }

    /**
     * Get current player info
     * @return array Player info ex:["name"=>"ABC","level"=>4,"xp"=>1300]
     */
    public function getCurrentPlayerInfo():array {
        if (!$this->player) return false;

        $username = $this->player->getUsername();
        $level = (int)$this->player->getLevel();
        $xp = (int)$this->player->getXp();

        return ["name"=>$username,"level"=>$level,"xp"=>$xp];
    }

    /**
     * Get current pokemon info
     * @return array Pokemon info ex:["name"=>"ABC","type"=>"Water","stamina"=>96,"attack"=>45,"defense"=>64]
     */
    public function getCurrentPokemonInfo():array {
        if (!$this->player) return ["name"=>"","type"=>"","stamina"=>0,"attack"=>0,"defense"=>0];
        
        $pokemon = $this->player->getPokemon();
        if (!$pokemon) return ["name"=>"","type"=>"","stamina"=>0,"attack"=>0,"defense"=>0];
        
        $level = $this->player->getLevel();
        return $this->getPokemonInfo($pokemon, (int)$level);
    }

    /**
     * Get info of the given pokemon
     * @param Pokemon $pokemon The pokemon to show it's details
     * @param int $level Pokemon level
     * @return array Pokemon info ex:["name"=>"ABC","type"=>"Water","stamina"=>96,"attack"=>45,"defense"=>64]
     */
    public function getPokemonInfo(Pokemon $pokemon, int $level = 1):array {

        $pokemonName = $pokemon->getName();
        $types = $pokemon->getTypes();
        $stamina = $pokemon->getStamina($level);
        $attack = $pokemon->getAttack();
        $defense = $pokemon->getDefense();

        return ["name"=>$pokemonName,"type"=>$types,"stamina"=>$stamina,"attack"=>$attack,"defense"=>$defense];
    }

    /**
     * Delete the current player
     * @return bool True when succeeded, false otherwise
    */
    public function deletePlayer():bool {

        try{

            $em = $this->doctrine->getManager();

            $em->remove($this->player);
            $em->flush();

            return true;
        }catch(\Exception $ex){
            return false;
        }
    }

    /**
     * Save current porgress to the database
     * @return bool True when succeeded, false otherwise
    */
    public function save():bool {
        try{
            $em = $this->doctrine->getManager();

            $em->flush();

            return true;
        }catch(\Exception $ex){
            return false;
        }
    }
}
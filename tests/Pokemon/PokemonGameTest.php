<?php
declare(strict_types=1);
 
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Pokemon\PokemonGame;
use PokemonBundle\Entity\Pokemon;

/**
 * @covers PokemonGame
 */
final class PokemonGameTest extends KernelTestCase
{
	protected static $testUsername;
	protected static $game;
    protected static $doctrine;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::$testUsername = uniqid('TestUser');

        self::bootKernel();
        self::$doctrine = static::$kernel->getContainer()->get('doctrine');
        self::$game = new PokemonGame(self::$doctrine);
    }

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();

        self::$game->deletePlayer();
    }

    public function testStartNewGame()
    {
        $status = self::$game->newGame(self::$testUsername);
        // Should pass
        $this->assertEquals(1, $status);

        $status = self::$game->newGame(self::$testUsername);
        // Should fail to duplicate username
        $this->assertEquals(-1, $status);

        $status = self::$game->newGame("a");
        // Should fail to short username
        $this->assertEquals(0, $status);
    }

    public function testPickingFirstPokemon()
    {
        $status = self::$game->resumeGame(self::$testUsername);
        $this->assertEquals(2, $status);

        $status = self::$game->setPokemon('INVALID INPUT');
        $this->assertFalse($status);

        // Squirtle
        $status = self::$game->setPokemon('s');
        $this->assertTrue($status);
    }

    public function testBattle()
    {
        $blastoise = self::$doctrine
            ->getRepository('PokemonBundle:Pokemon')
            ->findOneBy(array('id'=>9));

        $status = self::$game->battle($blastoise);
        $this->assertFalse($status);

        $charmander = self::$doctrine
            ->getRepository('PokemonBundle:Pokemon')
            ->findOneBy(array('id'=>4));

        // Wins first time, Level = 2, XP = 100
        $status = self::$game->battle($charmander);
        $this->assertTrue($status);
        $playerInfo = self::$game->getCurrentPlayerInfo();
        $this->assertEquals(["name"=>self::$testUsername,"level"=>2,"xp"=>100], $playerInfo);

        // Wins again, Level = 2, XP = 200
        $status = self::$game->battle($charmander);
        $this->assertTrue($status);
        $playerInfo = self::$game->getCurrentPlayerInfo();
        $this->assertEquals(["name"=>self::$testUsername,"level"=>2,"xp"=>200], $playerInfo);

        // Wins again, Level = 2, XP = 300
        $status = self::$game->battle($charmander);
        $this->assertTrue($status);
        $playerInfo = self::$game->getCurrentPlayerInfo();
        $this->assertEquals(["name"=>self::$testUsername,"level"=>2,"xp"=>300], $playerInfo);

        // Wins again, Level = 3, XP = 400
        $status = self::$game->battle($charmander);
        $this->assertTrue($status);
        $playerInfo = self::$game->getCurrentPlayerInfo();
        $this->assertEquals(["name"=>self::$testUsername,"level"=>3,"xp"=>400], $playerInfo);
    }

    public function testSaveGame()
    {
        $status = self::$game->save();
        $this->assertTrue($status);
    }

    public function testLoadGame()
    {
        self::$game = new PokemonGame(self::$doctrine);

        $status = self::$game->resumeGame("INVALID PLAYER");
        $this->assertEquals(-1, $status);

        $status = self::$game->resumeGame(self::$testUsername);
        $this->assertEquals(1, $status);

        // Test player info after loading the saved game
        $playerInfo = self::$game->getCurrentPlayerInfo();
        $this->assertEquals(["name"=>self::$testUsername,"level"=>3,"xp"=>400], $playerInfo);
    }

}


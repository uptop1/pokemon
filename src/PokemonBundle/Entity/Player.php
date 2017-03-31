<?php

namespace PokemonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})}, indexes={@ORM\Index(name="pokemon_id", columns={"pokemon_id"})})
 * @ORM\Entity
 */
class Player
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="smallint", precision=0, scale=0, nullable=false, unique=false)
     */
    private $level;

    /**
     * @var integer
     *
     * @ORM\Column(name="xp", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $xp;

    /**
     * @var \PokemonBundle\Entity\Pokemon
     *
     * @ORM\ManyToOne(targetEntity="PokemonBundle\Entity\Pokemon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pokemon_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $pokemon;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Player
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Player
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set xp
     *
     * @param integer $xp
     *
     * @return Player
     */
    public function setXp($xp)
    {
        $this->xp = $xp;

        return $this;
    }

    /**
     * Get xp
     *
     * @return integer
     */
    public function getXp()
    {
        return $this->xp;
    }

    /**
     * Set pokemon
     *
     * @param \PokemonBundle\Entity\Pokemon $pokemon
     *
     * @return Player
     */
    public function setPokemon(\PokemonBundle\Entity\Pokemon $pokemon = null)
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    /**
     * Get pokemon
     *
     * @return \PokemonBundle\Entity\Pokemon
     */
    public function getPokemon()
    {
        return $this->pokemon;
    }
}

<?php

namespace PokemonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PokemonVsPokemon
 *
 * @ORM\Table(name="pokemon_vs_pokemon")
 * @ORM\Entity
 */
class PokemonVsPokemon
{
    /**
     * @var integer
     *
     * @ORM\Column(name="attacker_pokemon_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $attackerPokemonId;

    /**
     * @var integer
     *
     * @ORM\Column(name="defender_pokemon_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $defenderPokemonId;

    /**
     * @var float
     *
     * @ORM\Column(name="ratio", type="float", precision=10, scale=0, nullable=false)
     */
    private $ratio;



    /**
     * Set attackerPokemonId
     *
     * @param integer $attackerPokemonId
     *
     * @return PokemonVsPokemon
     */
    public function setAttackerPokemonId($attackerPokemonId)
    {
        $this->attackerPokemonId = $attackerPokemonId;

        return $this;
    }

    /**
     * Get attackerPokemonId
     *
     * @return integer
     */
    public function getAttackerPokemonId()
    {
        return $this->attackerPokemonId;
    }

    /**
     * Set defenderPokemonId
     *
     * @param integer $defenderPokemonId
     *
     * @return PokemonVsPokemon
     */
    public function setDefenderPokemonId($defenderPokemonId)
    {
        $this->defenderPokemonId = $defenderPokemonId;

        return $this;
    }

    /**
     * Get defenderPokemonId
     *
     * @return integer
     */
    public function getDefenderPokemonId()
    {
        return $this->defenderPokemonId;
    }

    /**
     * Set ratio
     *
     * @param float $ratio
     *
     * @return PokemonVsPokemon
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * Get ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }
}

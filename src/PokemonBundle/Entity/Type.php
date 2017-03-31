<?php

namespace PokemonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type", uniqueConstraints={@ORM\UniqueConstraint(name="title", columns={"title"})})
 * @ORM\Entity
 */
class Type
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Type", inversedBy="attackType")
     * @ORM\JoinTable(name="battle_chart",
     *   joinColumns={
     *     @ORM\JoinColumn(name="attack_type", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="defense_type", referencedColumnName="id")
     *   }
     * )
     */
    private $defenseType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Place", mappedBy="type")
     */
    private $place;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Pokemon", mappedBy="type")
     */
    private $pokemon;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defenseType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->place = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pokemon = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set title
     *
     * @param string $title
     *
     * @return Type
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add defenseType
     *
     * @param \PokemonBundle\Entity\Type $defenseType
     *
     * @return Type
     */
    public function addDefenseType(\PokemonBundle\Entity\Type $defenseType)
    {
        $this->defenseType[] = $defenseType;

        return $this;
    }

    /**
     * Remove defenseType
     *
     * @param \PokemonBundle\Entity\Type $defenseType
     */
    public function removeDefenseType(\PokemonBundle\Entity\Type $defenseType)
    {
        $this->defenseType->removeElement($defenseType);
    }

    /**
     * Get defenseType
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefenseType()
    {
        return $this->defenseType;
    }

    /**
     * Add place
     *
     * @param \PokemonBundle\Entity\Place $place
     *
     * @return Type
     */
    public function addPlace(\PokemonBundle\Entity\Place $place)
    {
        $this->place[] = $place;

        return $this;
    }

    /**
     * Remove place
     *
     * @param \PokemonBundle\Entity\Place $place
     */
    public function removePlace(\PokemonBundle\Entity\Place $place)
    {
        $this->place->removeElement($place);
    }

    /**
     * Get place
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Add pokemon
     *
     * @param \PokemonBundle\Entity\Pokemon $pokemon
     *
     * @return Type
     */
    public function addPokemon(\PokemonBundle\Entity\Pokemon $pokemon)
    {
        $this->pokemon[] = $pokemon;

        return $this;
    }

    /**
     * Remove pokemon
     *
     * @param \PokemonBundle\Entity\Pokemon $pokemon
     */
    public function removePokemon(\PokemonBundle\Entity\Pokemon $pokemon)
    {
        $this->pokemon->removeElement($pokemon);
    }

    /**
     * Get pokemon
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPokemon()
    {
        return $this->pokemon;
    }
}

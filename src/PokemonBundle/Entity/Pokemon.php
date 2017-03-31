<?php

namespace PokemonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pokemon
 *
 * @ORM\Table(name="pokemon")
 * @ORM\Entity
 */
class Pokemon
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="types", type="string", length=100, nullable=false)
     */
    private $types;

    /**
     * @var integer
     *
     * @ORM\Column(name="stamina", type="integer", nullable=false)
     */
    private $stamina;

    /**
     * @var integer
     *
     * @ORM\Column(name="attack", type="integer", nullable=false)
     */
    private $attack;

    /**
     * @var integer
     *
     * @ORM\Column(name="defense", type="integer", nullable=false)
     */
    private $defense;

    /**
     * @var integer
     *
     * @ORM\Column(name="capture_rate", type="integer", nullable=false)
     */
    private $captureRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="flee_rate", type="integer", nullable=false)
     */
    private $fleeRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="candy", type="integer", nullable=true)
     */
    private $candy;

    /**
     * @var string
     *
     * @ORM\Column(name="quick_moves", type="string", length=150, nullable=true)
     */
    private $quickMoves;

    /**
     * @var string
     *
     * @ORM\Column(name="special_moves", type="string", length=150, nullable=true)
     */
    private $specialMoves;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Type", inversedBy="pokemon")
     * @ORM\JoinTable(name="pokemon_type",
     *   joinColumns={
     *     @ORM\JoinColumn(name="pokemon_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     *   }
     * )
     */
    private $type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Pokemon
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set types
     *
     * @param string $types
     *
     * @return Pokemon
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return string
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set stamina
     *
     * @param integer $stamina
     *
     * @return Pokemon
     */
    public function setStamina($stamina)
    {
        $this->stamina = $stamina;

        return $this;
    }

    /**
     * Get stamina
     *
     * @return integer
     */
    public function getStamina($level = 1)
    {
        return (int)($this->stamina * (1+(($level-1)/10)));
    }

    /**
     * Set attack
     *
     * @param integer $attack
     *
     * @return Pokemon
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get attack
     *
     * @return integer
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * Set defense
     *
     * @param integer $defense
     *
     * @return Pokemon
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get defense
     *
     * @return integer
     */
    public function getDefense()
    {
        return $this->defense;
    }

    /**
     * Set captureRate
     *
     * @param integer $captureRate
     *
     * @return Pokemon
     */
    public function setCaptureRate($captureRate)
    {
        $this->captureRate = $captureRate;

        return $this;
    }

    /**
     * Get captureRate
     *
     * @return integer
     */
    public function getCaptureRate()
    {
        return $this->captureRate;
    }

    /**
     * Set fleeRate
     *
     * @param integer $fleeRate
     *
     * @return Pokemon
     */
    public function setFleeRate($fleeRate)
    {
        $this->fleeRate = $fleeRate;

        return $this;
    }

    /**
     * Get fleeRate
     *
     * @return integer
     */
    public function getFleeRate()
    {
        return $this->fleeRate;
    }

    /**
     * Set candy
     *
     * @param integer $candy
     *
     * @return Pokemon
     */
    public function setCandy($candy)
    {
        $this->candy = $candy;

        return $this;
    }

    /**
     * Get candy
     *
     * @return integer
     */
    public function getCandy()
    {
        return $this->candy;
    }

    /**
     * Set quickMoves
     *
     * @param string $quickMoves
     *
     * @return Pokemon
     */
    public function setQuickMoves($quickMoves)
    {
        $this->quickMoves = $quickMoves;

        return $this;
    }

    /**
     * Get quickMoves
     *
     * @return string
     */
    public function getQuickMoves()
    {
        return $this->quickMoves;
    }

    /**
     * Set specialMoves
     *
     * @param string $specialMoves
     *
     * @return Pokemon
     */
    public function setSpecialMoves($specialMoves)
    {
        $this->specialMoves = $specialMoves;

        return $this;
    }

    /**
     * Get specialMoves
     *
     * @return string
     */
    public function getSpecialMoves()
    {
        return $this->specialMoves;
    }

    /**
     * Add type
     *
     * @param \PokemonBundle\Entity\Type $type
     *
     * @return Pokemon
     */
    public function addType(\PokemonBundle\Entity\Type $type)
    {
        $this->type[] = $type;

        return $this;
    }

    /**
     * Remove type
     *
     * @param \PokemonBundle\Entity\Type $type
     */
    public function removeType(\PokemonBundle\Entity\Type $type)
    {
        $this->type->removeElement($type);
    }

    /**
     * Get type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getType()
    {
        return $this->type;
    }
}

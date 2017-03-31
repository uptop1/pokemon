<?php

namespace PokemonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Place
 *
 * @ORM\Table(name="place")
 * @ORM\Entity
 */
class Place
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
     * @ORM\Column(name="name", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PokemonBundle\Entity\Type", inversedBy="place")
     * @ORM\JoinTable(name="place_pokemon_type",
     *   joinColumns={
     *     @ORM\JoinColumn(name="place_id", referencedColumnName="id", nullable=true)
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
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
     * @return Place
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
     * Add type
     *
     * @param \PokemonBundle\Entity\Type $type
     *
     * @return Place
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

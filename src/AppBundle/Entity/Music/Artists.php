<?php

namespace AppBundle\Entity\Music;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artists
 *
 * @ORM\Table(name="artists")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Music\ArtistsRepository")
 */
class Artists
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="country_id", type="integer")
     */
    private $countryId;

    /**
     * @var int
     *
     * @ORM\Column(name="genre_id", type="integer")
     */
    private $genreId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=127)
     */
    private $name;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     *
     * @return Artists
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set genreId
     *
     * @param integer $genreId
     *
     * @return Artists
     */
    public function setGenreId($genreId)
    {
        $this->genreId = $genreId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return int
     */
    public function getGenreId()
    {
        return $this->genreId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Artists
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
}


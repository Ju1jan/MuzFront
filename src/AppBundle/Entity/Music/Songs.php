<?php

namespace AppBundle\Entity\Music;

use Doctrine\ORM\Mapping as ORM;

/**
 * Songs
 *
 * @ORM\Table(name="songs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Music\SongsRepository")
 */
class Songs
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
     * @ORM\Column(name="artist_id", type="integer")
     */
    private $artistId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=127)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     */
    private $year;


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
     * Set artistId
     *
     * @param integer $artistId
     *
     * @return Songs
     */
    public function setArtistId($artistId)
    {
        $this->artistId = $artistId;

        return $this;
    }

    /**
     * Get artistId
     *
     * @return int
     */
    public function getArtistId()
    {
        return $this->artistId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Songs
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
     * Set year
     *
     * @param integer $year
     *
     * @return Songs
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
}


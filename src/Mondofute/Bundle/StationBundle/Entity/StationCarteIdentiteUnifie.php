<?php

namespace Mondofute\Bundle\StationBundle\Entity;

/**
 * StationCarteIdentiteUnifie
 */
class StationCarteIdentiteUnifie
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stationCarteIdentites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stationCarteIdentites = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add stationCarteIdentite
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite
     *
     * @return StationCarteIdentiteUnifie
     */
    public function addStationCarteIdentite(\Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite)
    {
        $this->stationCarteIdentites[] = $stationCarteIdentite;

        return $this;
    }

    /**
     * Remove stationCarteIdentite
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite
     */
    public function removeStationCarteIdentite(\Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite $stationCarteIdentite)
    {
        $this->stationCarteIdentites->removeElement($stationCarteIdentite);
    }

    /**
     * Get stationCarteIdentites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStationCarteIdentites()
    {
        return $this->stationCarteIdentites;
    }
}

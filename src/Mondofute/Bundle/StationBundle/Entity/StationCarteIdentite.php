<?php

namespace Mondofute\Bundle\StationBundle\Entity;

use Nucleus\ContactBundle\Entity\Moral;

/**
 * StationCarteIdentite
 */
class StationCarteIdentite extends Moral
{
//    /**
//     * @var integer
//     */
//    private $id;

    /**
     * @var integer
     */
    private $codePostal;

    /**
     * @var string
     */
    private $moisOuverture;

    /**
     * @var string
     */
    private $jourOuverture;

    /**
     * @var string
     */
    private $moisFermeture;

    /**
     * @var string
     */
    private $jourFermeture;

    /**
     * @var \Mondofute\Bundle\UniteBundle\Entity\Distance
     */
    private $altitudeVillage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stations;

    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;

    /**
     * @var \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie
     */
    private $stationCarteIdentiteUnifie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stations = new \Doctrine\Common\Collections\ArrayCollection();
    }

//    /**
//     * Get id
//     *
//     * @return integer
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

    /**
     * Get codePostal
     *
     * @return integer
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set codePostal
     *
     * @param integer $codePostal
     *
     * @return StationCarteIdentite
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get moisOuverture
     *
     * @return string
     */
    public function getMoisOuverture()
    {
        return $this->moisOuverture;
    }

    /**
     * Set moisOuverture
     *
     * @param string $moisOuverture
     *
     * @return StationCarteIdentite
     */
    public function setMoisOuverture($moisOuverture)
    {
        $this->moisOuverture = $moisOuverture;

        return $this;
    }

    /**
     * Get jourOuverture
     *
     * @return string
     */
    public function getJourOuverture()
    {
        return $this->jourOuverture;
    }

    /**
     * Set jourOuverture
     *
     * @param string $jourOuverture
     *
     * @return StationCarteIdentite
     */
    public function setJourOuverture($jourOuverture)
    {
        $this->jourOuverture = $jourOuverture;

        return $this;
    }

    /**
     * Get moisFermeture
     *
     * @return string
     */
    public function getMoisFermeture()
    {
        return $this->moisFermeture;
    }

    /**
     * Set moisFermeture
     *
     * @param string $moisFermeture
     *
     * @return StationCarteIdentite
     */
    public function setMoisFermeture($moisFermeture)
    {
        $this->moisFermeture = $moisFermeture;

        return $this;
    }

    /**
     * Get jourFermeture
     *
     * @return string
     */
    public function getJourFermeture()
    {
        return $this->jourFermeture;
    }

    /**
     * Set jourFermeture
     *
     * @param string $jourFermeture
     *
     * @return StationCarteIdentite
     */
    public function setJourFermeture($jourFermeture)
    {
        $this->jourFermeture = $jourFermeture;

        return $this;
    }

    /**
     * Get altitudeVillage
     *
     * @return \Mondofute\Bundle\UniteBundle\Entity\Distance
     */
    public function getAltitudeVillage()
    {
        return $this->altitudeVillage;
    }

    /**
     * Set altitudeVillage
     *
     * @param \Mondofute\Bundle\UniteBundle\Entity\Distance $altitudeVillage
     *
     * @return StationCarteIdentite
     */
    public function setAltitudeVillage(\Mondofute\Bundle\UniteBundle\Entity\Distance $altitudeVillage = null)
    {
        $this->altitudeVillage = $altitudeVillage;

        return $this;
    }

    /**
     * Add station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     *
     * @return StationCarteIdentite
     */
    public function addStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Remove station
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\Station $station
     */
    public function removeStation(\Mondofute\Bundle\StationBundle\Entity\Station $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    /**
     * Get site
     *
     * @return \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param \Mondofute\Bundle\SiteBundle\Entity\Site $site
     *
     * @return StationCarteIdentite
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationCarteIdentiteUnifie
     *
     * @return \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie
     */
    public function getStationCarteIdentiteUnifie()
    {
        return $this->stationCarteIdentiteUnifie;
    }

    /**
     * Set stationCarteIdentiteUnifie
     *
     * @param \Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie $stationCarteIdentiteUnifie
     *
     * @return StationCarteIdentite
     */
    public function setStationCarteIdentiteUnifie(\Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie $stationCarteIdentiteUnifie = null)
    {
        $this->stationCarteIdentiteUnifie = $stationCarteIdentiteUnifie;

        return $this;
    }
}

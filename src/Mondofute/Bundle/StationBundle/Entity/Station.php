<?php

namespace Mondofute\Bundle\StationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Station
 */
class Station
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
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
     * @var string
     */
    private $lienMeteo;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var StationUnifie
     */
    private $stationUnifie;
    /**
     * @var ZoneTouristique
     */
    private $zoneTouristique;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Secteur
     */
    private $secteur;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    private $departement;
    /**
     * @var \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    private $domaine;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Get codePostal
     *
     * @return int
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
     * @return Station
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
     * @return Station
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
     * @return Station
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
     * @return Station
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
     * @return Station
     */
    public function setJourFermeture($jourFermeture)
    {
        $this->jourFermeture = $jourFermeture;

        return $this;
    }

    /**
     * Get lienMeteo
     *
     * @return string
     */
    public function getLienMeteo()
    {
        return $this->lienMeteo;
    }

    /**
     * Set lienMeteo
     *
     * @param string $lienMeteo
     *
     * @return Station
     */
    public function setLienMeteo($lienMeteo)
    {
        $this->lienMeteo = $lienMeteo;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param StationTraduction $traduction
     */
    public function removeTraduction(StationTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Station
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get stationUnifie
     *
     * @return StationUnifie
     */
    public function getStationUnifie()
    {
        return $this->stationUnifie;
    }

    /**
     * Set stationUnifie
     *
     * @param StationUnifie $stationUnifie
     *
     * @return Station
     */
    public function setStationUnifie(StationUnifie $stationUnifie = null)
    {
        $this->stationUnifie = $stationUnifie;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $traductions = $this->getTraductions();
        $this->traductions = new ArrayCollection();
        if (count($traductions) > 0) {
            foreach ($traductions as $traduction) {
                $cloneTraduction = clone $traduction;
                $this->traductions->add($cloneTraduction);
                $cloneTraduction->setStation($this);
            }
        }
    }

    /**
     * Get traductions
     *
     * @return Collection
     */
    public function getTraductions()
    {
        return $this->traductions;
    }

    public function setTraductions($traductions)
    {
        $this->getTraductions()->clear();

        foreach ($traductions as $traduction) {
            $this->addTraduction($traduction);
        }
        return $this;
    }

    /**
     * Add traduction
     *
     * @param StationTraduction $traduction
     *
     * @return Station
     */
    public function addTraduction(StationTraduction $traduction)
    {
        $this->traductions[] = $traduction->setStation($this);

        return $this;
    }

    /**
     * Get zoneTouristique
     *
     * @return ZoneTouristique
     */
    public function getZoneTouristique()
    {
        return $this->zoneTouristique;
    }

    /**
     * Set zoneTouristique
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique $zoneTouristique
     *
     * @return Station
     */
    public function setZoneTouristique(ZoneTouristique $zoneTouristique = null)
    {
        $this->zoneTouristique = $zoneTouristique;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Secteur
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set secteur
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur
     *
     * @return Station
     */
    public function setSecteur(\Mondofute\Bundle\GeographieBundle\Entity\Secteur $secteur = null)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Departement $departement
     *
     * @return Station
     */
    public function setDepartement(\Mondofute\Bundle\GeographieBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return \Mondofute\Bundle\DomaineBundle\Entity\Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param \Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine
     *
     * @return Station
     */
    public function setDomaine(\Mondofute\Bundle\DomaineBundle\Entity\Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }
}

<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * Domaine
 */
class Domaine
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Collection
     */
    private $traductions;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var DomaineUnifie
     */
    private $domaineUnifie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traductions = new ArrayCollection();
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
     * Add traduction
     *
     * @param DomaineTraduction $traduction
     *
     * @return Domaine
     */
    public function addTraduction(DomaineTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDomaine($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param DomaineTraduction $traduction
     */
    public function removeTraduction(DomaineTraduction $traduction)
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
     * @return Domaine
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get domaineUnifie
     *
     * @return DomaineUnifie
     */
    public function getDomaineUnifie()
    {
        return $this->domaineUnifie;
    }

    /**
     * Set domaineUnifie
     *
     * @param DomaineUnifie $domaineUnifie
     *
     * @return Domaine
     */
    public function setDomaineUnifie(DomaineUnifie $domaineUnifie = null)
    {
        $this->domaineUnifie = $domaineUnifie;

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
                $cloneTraduction->setDomaine($this);
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
        $this->traductions = $traductions;
        return $this;
    }
}
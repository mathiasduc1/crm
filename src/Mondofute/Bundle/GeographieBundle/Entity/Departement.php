<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Departement
 */
class Departement
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $traductions;
    /**
     * @var \Mondofute\Bundle\SiteBundle\Entity\Site
     */
    private $site;
    /**
     * @var \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie
     */
    private $departementUnifie;

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
     * Add traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction
     *
     * @return Departement
     */
    public function addTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction)
    {
        $this->traductions[] = $traduction->setDepartement($this);

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction
     */
    public function removeTraduction(\Mondofute\Bundle\GeographieBundle\Entity\DepartementTraduction $traduction)
    {
        $this->traductions->removeElement($traduction);
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
     * @return Departement
     */
    public function setSite(\Mondofute\Bundle\SiteBundle\Entity\Site $site = null)
    {
        $this->site = $site;

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
                $cloneTraduction->setDepartement($this);
            }
        }
    }

    /**
     * Get traductions
     *
     * @return \Doctrine\Common\Collections\Collection
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

    /**
     * Get departementUnifie
     *
     * @return \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie
     */
    public function getDepartementUnifie()
    {
        return $this->departementUnifie;
    }

    /**
     * Set departementUnifie
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie $departementUnifie
     *
     * @return Departement
     */
    public function setDepartementUnifie(
        \Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie $departementUnifie = null
    ) {
        $this->departementUnifie = $departementUnifie;

        return $this;
    }
}
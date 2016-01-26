<?php

namespace Mondofute\Bundle\GeographieBundle\Entity;

/**
 * ProfilUnifie
 */
class ProfilUnifie
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $profils;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profils = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add profil
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     *
     * @return ProfilUnifie
     */
    public function addProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil)
    {
        $this->profils[] = $profil;

        return $this;
    }

    /**
     * Remove profil
     *
     * @param \Mondofute\Bundle\GeographieBundle\Entity\Profil $profil
     */
    public function removeProfil(\Mondofute\Bundle\GeographieBundle\Entity\Profil $profil)
    {
        $this->profils->removeElement($profil);
    }

    /**
     * Get profils
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfils()
    {
        return $this->profils;
    }
}

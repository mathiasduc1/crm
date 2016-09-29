<?php

namespace Mondofute\Bundle\CodePromoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use HiDev\Bundle\CodePromoBundle\Entity\CodePromo as BaseCodePromo;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoApplication;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromoClient;
use Mondofute\Bundle\SiteBundle\Entity\Site;

/**
 * CodePromo
 */
class CodePromo extends BaseCodePromo
{
//    /**
//     * @var int
//     */
//    protected $id;
    /**
     * @var string
     */
    protected $code;
    /**
     * @var Collection
     */
    private $codePromoPeriodeSejours;
    /**
     * @var CodePromoUnifie
     */
    private $codePromoUnifie;
    /**
     * @var Site
     */
    private $site;
    /**
     * @var boolean
     */
    private $actifSite = true;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $codePromoClients;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $codePromoApplications;

    public function __construct()
    {
        parent::__construct();
        $this->codePromoPeriodeSejours = new ArrayCollection();
        $this->codePromoClients = new ArrayCollection();
        $this->codePromoApplications = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add codePromoPeriodeSejour
     *
     * @param CodePromoPeriodeSejour $codePromoPeriodeSejour
     *
     * @return CodePromo
     */
    public function addCodePromoPeriodeSejour(CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours[] = $codePromoPeriodeSejour;

        return $this;
    }

    /**
     * Remove codePromoPeriodeSejour
     *
     * @param CodePromoPeriodeSejour $codePromoPeriodeSejour
     */
    public function removeCodePromoPeriodeSejour(CodePromoPeriodeSejour $codePromoPeriodeSejour)
    {
        $this->codePromoPeriodeSejours->removeElement($codePromoPeriodeSejour);
    }

    /**
     * Get codePromoPeriodeSejours
     *
     * @return Collection
     */
    public function getCodePromoPeriodeSejours()
    {
        return $this->codePromoPeriodeSejours;
    }

    /**
     * Get codePromoUnifie
     *
     * @return CodePromoUnifie
     */
    public function getCodePromoUnifie()
    {
        return $this->codePromoUnifie;
    }

    /**
     * Set codePromoUnifie
     *
     * @param CodePromoUnifie $codePromoUnifie
     *
     * @return CodePromo
     */
    public function setCodePromoUnifie(CodePromoUnifie $codePromoUnifie = null)
    {
        $this->codePromoUnifie = $codePromoUnifie;

        return $this;
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
     * @return CodePromo
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get actifSite
     *
     * @return boolean
     */
    public function getActifSite()
    {
        return $this->actifSite;
    }

    /**
     * Set actifSite
     *
     * @param boolean $actifSite
     *
     * @return CodePromo
     */
    public function setActifSite($actifSite)
    {
        $this->actifSite = $actifSite;

        return $this;
    }

    /**
     * Add codePromoClient
     *
     * @param CodePromoClient $codePromoClient
     *
     * @return CodePromo
     */
    public function addCodePromoClient(CodePromoClient $codePromoClient)
    {
        $this->codePromoClients[] = $codePromoClient->setCodePromo($this);

        return $this;
    }

    /**
     * Remove codePromoClient
     *
     * @param CodePromoClient $codePromoClient
     */
    public function removeCodePromoClient(CodePromoClient $codePromoClient)
    {
        $this->codePromoClients->removeElement($codePromoClient);
    }

    /**
     * Get codePromoClients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCodePromoClients()
    {
        return $this->codePromoClients;
    }

    /**
     * Add codePromoApplication
     *
     * @param CodePromoApplication $codePromoApplication
     *
     * @return CodePromo
     */
    public function addCodePromoApplication(CodePromoApplication $codePromoApplication)
    {
        $this->codePromoApplications[] = $codePromoApplication->setCodePromo($this);

        return $this;
    }

    /**
     * Remove codePromoApplication
     *
     * @param CodePromoApplication $codePromoApplication
     */
    public function removeCodePromoApplication(CodePromoApplication $codePromoApplication)
    {
        $this->codePromoApplications->removeElement($codePromoApplication);
    }

    /**
     * Get codePromoApplications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCodePromoApplications()
    {
        return $this->codePromoApplications;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $codePromoFournisseurs;


    /**
     * Add codePromoFournisseur
     *
     * @param \Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur $codePromoFournisseur
     *
     * @return CodePromo
     */
    public function addCodePromoFournisseur(\Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur $codePromoFournisseur)
    {
        $this->codePromoFournisseurs[] = $codePromoFournisseur;

        return $this;
    }

    /**
     * Remove codePromoFournisseur
     *
     * @param \Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur $codePromoFournisseur
     */
    public function removeCodePromoFournisseur(\Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur $codePromoFournisseur)
    {
        $this->codePromoFournisseurs->removeElement($codePromoFournisseur);
    }

    /**
     * Get codePromoFournisseurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCodePromoFournisseurs()
    {
        return $this->codePromoFournisseurs;
    }
}

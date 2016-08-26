<?php

namespace Mondofute\Bundle\GeographieBundle\Repository;

/**
 * SecteurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SecteurRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des secteurs crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('secteur , traductions')
            ->from('MondofuteGeographieBundle:Secteur', 'secteur')
            ->join('secteur.traductions', 'traductions')
            ->join('secteur.site', 'site')
            ->join('traductions.langue', 'langue')
            ->where("langue.code = '$locale'");
//        ->setParameter('code' , $locale)
        $qb->orderBy('secteur.secteurUnifie', 'ASC');

        return $qb;
    }
}

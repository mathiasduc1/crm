<?php

namespace Mondofute\Bundle\DomaineBundle\Repository;

/**
 * NiveauSkieurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NiveauSkieurRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioin des niveau skieur crm qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ns , nst ')
            ->from('MondofuteDomaineBundle:NiveauSkieur', 'ns')
            ->join('ns.traductions', 'nst')
            ->join('nst.langue', 'l')
            ->where("l.code = '$locale'");
        $qb->orderBy('ns.id', 'ASC');

        return $qb;
    }
}

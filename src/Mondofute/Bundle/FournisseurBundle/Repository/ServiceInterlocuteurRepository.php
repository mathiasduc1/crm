<?php

namespace Mondofute\Bundle\FournisseurBundle\Repository;

/**
 * ServiceInterlocuteurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceInterlocuteurRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traductioins qui sont de la langue locale
    public function getTraductionsByLocale($locale)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('serviceInterlocuteur , traductions ')
            ->from('MondofuteFournisseurBundle:ServiceInterlocuteur', 'serviceInterlocuteur')
            ->join('serviceInterlocuteur.traductions', 'traductions')
            ->join('traductions.langue', 'langue')
            ->where("langue.code = :code")
            ->setParameter('code', $locale)
            ->orderBy('serviceInterlocuteur.id', 'ASC');
        return $qb;
    }
}

<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PrestationAnnexeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestationAnnexeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    // récupérer les traduction des stations crm qui sont de la langue locale
    public function getTraductionsByLocale($locale, $famillePrestationAnnexeId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s , st')
            ->from('MondofutePrestationAnnexeBundle:PrestationAnnexe', 's')
            ->join('s.traductions', 'st')
            ->join('st.langue', 'l')
            ->where("l.code = '$locale'")
        ;
        if(!empty($famillePrestationAnnexeId)){
            $qb
                ->andWhere('s.famillePrestationAnnexe = :famillePrestationAnnexeId ')
                ->setParameter('famillePrestationAnnexeId' , $famillePrestationAnnexeId)
            ;
        }

//        ->setParameter('code' , $locale)
        $qb->orderBy('s.id', 'ASC');

        return $qb;
    }
    /**
     * @return mixed
     */
    public function countTotal()
    {
        return $this->createQueryBuilder('entity')
            ->select('COUNT(entity)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * Get the paginated list of published secteurs
//     *
//     * @param int $page
//     * @param int $maxperpage
//     * @param $locale
//     * @param array $sortbyArray
//     * @param int $site
//     * @return Paginator
//     */
//    public function getList($page = 1, $maxperpage, $locale, $sortbyArray = array(), $site = 1)
//    {
//        $q = $this->createQueryBuilder('unifie')
//            ->select('unifie')
//            ->join('unifie.prestationAnnexes', 'entities')
//            ->join('entities.traductions', 'traductions')
//            ->join('traductions.langue', 'langue')
//            ->where('entities.site = :site')
//            ->setParameter('site', $site)
//            ->andWhere('langue.code = :code')
//            ->setParameter('code', $locale)
//            ->setFirstResult(($page - 1) * $maxperpage)
//            ->setMaxResults($maxperpage);
//
//        foreach ($sortbyArray as $key => $item) {
//            $q
//                ->orderBy($key, $item);
//        }
//
//        return new Paginator($q);
//    }


    /**
     * Get the paginated list of published secteurs
     *
     * @param int $page
     * @param int $maxperpage
     * @param $locale
     * @param array $sortbyArray
     * @param int $site
     * @return Paginator
     */
    public function getList($page = 1, $maxperpage, $locale, $sortbyArray = array(), $site = 1)
    {
        $q = $this->createQueryBuilder('entity')
            ->select('entity')
            ->join('entity.traductions', 'traductions')
            ->join('traductions.langue', 'langue')
            ->andWhere('langue.code = :code')
            ->setParameter('code', $locale)
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }

        return new Paginator($q);
    }

}
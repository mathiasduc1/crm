<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Repository;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;

/**
 * PrestationAnnexeFournisseurUnifieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestationAnnexeFournisseurUnifieRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByCriteria($fournisseurId, FournisseurPrestationAnnexe $prestationAnnex, $stationExists = false)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('prestationAnnexeFournisseurUnifie , prestationAnnexeFournisseurs')
            ->from('MondofuteFournisseurPrestationAffectationBundle:PrestationAnnexeFournisseurUnifie', 'prestationAnnexeFournisseurUnifie')
            ->join('prestationAnnexeFournisseurUnifie.prestationAnnexeFournisseurs', 'prestationAnnexeFournisseurs')
            ->where('prestationAnnexeFournisseurs.fournisseurPrestationAnnexe = :prestationAnnex')
            ->setParameter('prestationAnnex' , $prestationAnnex->getId())
            ->andWhere('prestationAnnexeFournisseurs.fournisseur = :fournisseurId')
            ->setParameter('fournisseurId' ,$fournisseurId )
        ;

        if($stationExists){
            $qb
                ->andWhere('prestationAnnexeFournisseurs.station IS NOT NULL')
            ;
        }
        else
        {
            $qb
                ->andWhere('prestationAnnexeFournisseurs.station IS NULL')
            ;
        }

        $result = $qb->getQuery()->getOneOrNullResult();
//        dump($result);die;
        return $result;
    }

    public function findByFournisseur(Fournisseur $fournisseur , $whereStation = null , $stationUnifieId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('prestationAnnexeFournisseurUnifie, prestationAnnexeFournisseurs')
            ->from('MondofuteFournisseurPrestationAffectationBundle:PrestationAnnexeFournisseurUnifie', 'prestationAnnexeFournisseurUnifie')
            ->join('prestationAnnexeFournisseurUnifie.prestationAnnexeFournisseurs', 'prestationAnnexeFournisseurs')
            ->where('prestationAnnexeFournisseurs.fournisseur = :fournisseurId')
            ->setParameter('fournisseurId' ,$fournisseur->getId() )
        ;

        if(!empty($whereStation))
        {
            $qb
                ->join('prestationAnnexeFournisseurs.station' , 'station')
                ->andWhere('station.id ' . $whereStation )
            ;
        }
        if(!empty($stationUnifieId))
        {
            $qb
                ->join('prestationAnnexeFournisseurs.station' , 'station')
                ->join('station.stationUnifie' , 'stationUnifie')
                ->andWhere('stationUnifie.id = ' . $stationUnifieId )
            ;
        }

        $result = $qb->getQuery()->getResult();
//        dump($result);die;
        return $result;
    }
}

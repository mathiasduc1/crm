<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Repository;

/**
 * FournisseurPrestationAnnexeParamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurPrestationAnnexeParamRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * retourner les prestationAnnexeAnnexe dont le founisseur n'est pas un hébergement
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByFournisseurNotHebergement()
    {
        $qb = $this->createQueryBuilder('entity');
        $qb
            ->select('entity')
            ->join('entity.fournisseurPrestationAnnexe', 'fournisseurPrestationAnnexe')
            ->join('fournisseurPrestationAnnexe.fournisseur', 'fournisseur')
            ->join('fournisseur.types', 'famillePrestationAnnexe')
            ->where('famillePrestationAnnexe.id != 9');

        return $qb;

    }
}

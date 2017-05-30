<?php

namespace Mondofute\Bundle\MotClefBundle\Repository;

/**
 * MotClefTraductionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MotClefTraductionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByLike($like, $langue)
    {
        $q = $this->createQueryBuilder('motClefTraduction')
            ->select('motClefTraduction.id, motClefTraduction.libelle text')
            ->where('motClefTraduction.libelle LIKE :val')
            ->setParameter('val', '%' . $like . '%')
            ->andWhere('motClefTraduction.langue = :langue')
            ->setParameter('langue', $langue);

        return $q->getQuery()->getResult();
    }
}

<?php

namespace Mondofute\Bundle\FournisseurBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;

/**
 * FournisseurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurRepository extends \Doctrine\ORM\EntityRepository
{
    public function getFournisseurDeFournisseur($fournisseurId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fournisseur')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->where("fournisseur.contient = :contient")
            ->setParameter('contient', FournisseurContient::FOURNISSEUR);
        if (!empty($fournisseurId)) {
            $qb->andWhere("fournisseur.id != :id")
//            ->setParameters(array('contient'=> FournisseurContient::FOURNISSEUR , 'id' => $fournisseurId))
                ->setParameter('id', $fournisseurId);
        }
        $qb->orderBy('fournisseur.id', 'ASC');
        return $qb;
    }

    public function rechercherTypeHebergement($enseigne = '')
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fournisseur')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->where("fournisseur.contient = :contient")
            ->setParameter('contient', FournisseurContient::PRODUIT)
            ->join('fournisseur.types', 'types')
            ->andWhere('types.id = :typeId')
            ->setParameter('typeId', 9);
        if (!empty($enseigne)) {
            $qb->andWhere("fournisseur.enseigne LIKE :enseigne")
//            ->setParameters(array('contient'=> FournisseurContient::FOURNISSEUR , 'id' => $fournisseurId))
                ->setParameter('enseigne', '%' . $enseigne . '%');
        }
        $qb->orderBy('fournisseur.enseigne', 'ASC');
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
            ->setFirstResult(($page - 1) * $maxperpage)
            ->setMaxResults($maxperpage);

        foreach ($sortbyArray as $key => $item) {
            $q
                ->orderBy($key, $item);
        }

        return new Paginator($q);
    }

    public function findFournisseurByContient($contient, $fournisseurId = null)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fournisseur.id , fournisseur.enseigne')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->where("fournisseur.contient = :contient")
            ->setParameter('contient', $contient);
        $qb->orderBy('fournisseur.id', 'ASC');

        if (!empty($fournisseurId)) {
            $qb
                ->andWhere('fournisseur.id = :fournisseurId')
                ->setParameter('fournisseurId', $fournisseurId);
        }

        $result = $qb->getQuery()->getResult();
//        dump($result);die;
        return $result;
    }

    public function findWithPrestationAnnexes()
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q
            ->select('fournisseur')
            ->from('MondofuteFournisseurBundle:Fournisseur', 'fournisseur')
            ->join('fournisseur.prestationAnnexes', 'prestationAnnexes');

        $result = $q->getQuery()->getResult();
//        dump($result);die;
        return $result;
    }
}

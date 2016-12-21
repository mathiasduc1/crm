<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Repository;

use Doctrine\DBAL\Types\Type;

/**
 * FournisseurPrestationAnnexeStockFournisseurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FournisseurPrestationAnnexeStockFournisseurRepository extends \Doctrine\ORM\EntityRepository
{
    private $connexion;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->connexion = $this->getEntityManager()->getConnection();
    }

    /**
     * @param $fournisseurHebergementId
     * @param $fournisseurPrestationAnnexeId
     * @param $typePeriodeId
     * @return array
     */
    public function charger($fournisseurPrestationAnnexeId, $typePeriodeId)
    {
        $stocks = [];
//        $em = $this->getEntityManager();
//        $site = $em->getRepository(Site::class)->findOneBy(array('crm' => true));
//        if (isset($em)) {
//            unset($em);
//        }
        $sql = 'SELECT fpasf.periode_id,fpasf.stock FROM fournisseur_prestation_annexe_stock_fournisseur AS fpasf JOIN periode AS p ON p.id=fpasf.periode_id WHERE fpasf.fournisseur_prestation_annexe_id=? AND p.type_id=?';

        $this->connexion->beginTransaction();
        $stmt = $this->connexion->prepare($sql);
        if ($stmt) {
            $retour = $stmt->bindValue(1, intval($fournisseurPrestationAnnexeId, 10), Type::BIGINT);
            if ($retour) {
                $retour = $stmt->bindValue(2, intval($typePeriodeId, 10), Type::BIGINT);
                if ($retour) {
                    $result = $stmt->execute();
                    if (!$result) {
                        $this->connexion->rollBack();
                        $retour = false;
                    } else {
                        while ($result = $stmt->fetch()) {
                            $stocks[] = ['stock' => $result['stock'], 'periode' => $result['periode_id']];
                        }
                    }
                }
            }
        }

        return $stocks;
    }
}

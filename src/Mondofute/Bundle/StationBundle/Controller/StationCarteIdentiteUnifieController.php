<?php

namespace Mondofute\Bundle\StationBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite;
use Mondofute\Bundle\StationBundle\Entity\StationCarteIdentiteUnifie;
use Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\UniteBundle\Entity\UniteDistance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * StationCarteIdentiteUnifie controller.
 *
 */
class StationCarteIdentiteUnifieController extends Controller
{
    /**
     * Lists all StationCarteIdentiteUnifie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stationCarteIdentiteUnifies = $em->getRepository('MondofuteStationBundle:StationCarteIdentiteUnifie')->findAll();

        return $this->render('@MondofuteStation/stationcarteidentiteunifie/index.html.twig', array(
            'stationCarteIdentiteUnifies' => $stationCarteIdentiteUnifies,
        ));
    }

    /**
     * Creates a new StationCarteIdentiteUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

        $sitesAEnregistrer = $request->get('sites');

        $stationCarteIdentiteUnifie = new StationCarteIdentiteUnifie();

        $this->ajouterStationCarteIdentitesDansForm($stationCarteIdentiteUnifie);
        $this->stationCarteIdentitesSortByAffichage($stationCarteIdentiteUnifie);

        $form = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType', $stationCarteIdentiteUnifie, array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // affilier les entités liés
//            $this->affilierEntities($stationCarteIdentiteUnifie);

            $this->supprimerStationCarteIdentites($stationCarteIdentiteUnifie, $sitesAEnregistrer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($stationCarteIdentiteUnifie);
            $em->flush();

            $this->copieVersSites($stationCarteIdentiteUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationCarteIdentite a bien été créé.'
            );

            return $this->redirectToRoute('stationcarteidentite_edit', array('id' => $stationCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationcarteidentiteunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $stationCarteIdentiteUnifie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajouter les stationCarteIdentites qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param StationCarteIdentiteUnifie $entity
     */
    private function ajouterStationCarteIdentitesDansForm(StationCarteIdentiteUnifie $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        foreach ($sites as $site) {
            $siteExiste = false;
            if (!$siteExiste) {
                $stationCarteIdentite = new StationCarteIdentite();
                $stationCarteIdentite->setSite($site);

                $entity->addStationCarteIdentite($stationCarteIdentite);
            }
        }
    }


    /**
     * Classe les stationCarteIdentites par classementAffichage
     * @param StationCarteIdentiteUnifie $entity
     */
    private function stationCarteIdentitesSortByAffichage(StationCarteIdentiteUnifie $entity)
    {

        // Trier les stationCarteIdentites en fonction de leurs ordre d'affichage
        $stationCarteIdentites = $entity->getStationCarteIdentites(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $stationCarteIdentites->getIterator();
        unset($stationCarteIdentites);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        /** @var ArrayIterator $iterator */
        $iterator->uasort(function (StationCarteIdentite $a, StationCarteIdentite $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $stationCarteIdentites = new ArrayCollection(iterator_to_array($iterator));

        // remplacé les stationCarteIdentites par ce nouveau tableau (une fonction 'set' a été créé dans StationCarteIdentite unifié)
        $entity->setStationCarteIdentites($stationCarteIdentites);
    }

    /**
     * retirer de l'entité les stationCarteIdentites qui ne doivent pas être enregistrer
     * @param StationCarteIdentiteUnifie $entity
     * @param array $sitesAEnregistrer
     *
     * @return $this
     */
    private function supprimerStationCarteIdentites(StationCarteIdentiteUnifie $entity, array $sitesAEnregistrer)
    {
        foreach ($entity->getStationCarteIdentites() as $stationCarteIdentite) {
            if (!in_array($stationCarteIdentite->getSite()->getId(), $sitesAEnregistrer)) {
                $stationCarteIdentite->setStationCarteIdentiteUnifie(null);
                $entity->removeStationCarteIdentite($stationCarteIdentite);
            }
        }
        return $this;
    }

    /**
     * Copie dans la base de données site l'entité stationCarteIdentite
     * @param StationCarteIdentiteUnifie $entity
     */
    private function copieVersSites(StationCarteIdentiteUnifie $entity)
    {
//        Boucle sur les stationCarteIdentites afin de savoir sur quel site nous devons l'enregistrer
        /** @var StationCarteIdentite $stationCarteIdentite */
        foreach ($entity->getStationCarteIdentites() as $stationCarteIdentite) {
            if ($stationCarteIdentite->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($stationCarteIdentite->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $stationCarteIdentite->getSite()->getId()));
//                if (!empty($stationCarteIdentite->getZoneTouristique())) {
//                    $zoneTouristique = $emSite->getRepository(ZoneTouristique::class)->findOneBy(array('zoneTouristiqueUnifie' => $stationCarteIdentite->getZoneTouristique()->getZoneTouristiqueUnifie()));
//                } else {
//                    $zoneTouristique = null;
//                }
//                if (!empty($stationCarteIdentite->getSecteur())) {
//                    $secteur = $emSite->getRepository(Secteur::class)->findOneBy(array('secteurUnifie' => $stationCarteIdentite->getSecteur()->getSecteurUnifie()));
//                } else {
//                    $secteur = null;
//                }
//                if (!empty($stationCarteIdentite->getDomaine())) {
//                    $domaine = $emSite->getRepository(Domaine::class)->findOneBy(array('domaineUnifie' => $stationCarteIdentite->getDomaine()->getDomaineUnifie()));
//                } else {
//                    $domaine = null;
//                }
//                if (!empty($stationCarteIdentite->getDepartement())) {
//                    $departement = $emSite->getRepository(Departement::class)->findOneBy(array('departementUnifie' => $stationCarteIdentite->getDepartement()->getDepartementUnifie()));
//                } else {
//                    $departement = null;
//                }

//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
//                if (is_null(($entitySite = $emSite->getRepository(StationCarteIdentiteUnifie::class)->find($entity->getId())))) {
                $entitySite = $emSite->find(StationCarteIdentiteUnifie::class, $entity->getId());
                if (empty($entitySite)) {
                    $entitySite = new StationCarteIdentiteUnifie();
                }
//                if (is_null(($entitySite = $em->getRepository('MondofuteStationCarteIdentiteBundle:StationCarteIdentiteUnifie')->find(array($entity->getId()))))) {
//                    $entitySite = new StationCarteIdentiteUnifie();
//                }


//            Récupération de la stationCarteIdentite sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($stationCarteIdentiteSite = $emSite->getRepository(StationCarteIdentite::class)->findOneBy(array('stationCarteIdentiteUnifie' => $entitySite))))) {
                    $stationCarteIdentiteSite = new StationCarteIdentite();
                }


//            copie des données stationCarteIdentite
                $stationCarteIdentiteSite
                    ->setSite($site)
                    ->setStationCarteIdentiteUnifie($entitySite)
                    ->setCodePostal($stationCarteIdentite->getCodePostal())
                    ->setJourOuverture($stationCarteIdentite->getJourOuverture())
                    ->setMoisOuverture($stationCarteIdentite->getMoisOuverture())
                    ->setJourFermeture($stationCarteIdentite->getJourFermeture())
                    ->setMoisFermeture($stationCarteIdentite->getMoisFermeture());


                if (empty($stationCarteIdentiteSite->getAltitudeVillage())) {
                    $altitudeVillage = new Distance();
                    $altitudeVillage->setValeur($stationCarteIdentite->getAltitudeVillage()->getValeur());
                    $altitudeVillage->setUnite($emSite->find(UniteDistance::class, $stationCarteIdentite->getAltitudeVillage()->getUnite()));
                    $stationCarteIdentiteSite->setAltitudeVillage($altitudeVillage);
                } else {
                    $stationCarteIdentiteSite->getAltitudeVillage()->setValeur($stationCarteIdentite->getAltitudeVillage()->getValeur());
                    $stationCarteIdentiteSite->getAltitudeVillage()->setUnite($emSite->find(UniteDistance::class, $stationCarteIdentite->getAltitudeVillage()->getUnite()));
                }

//            Gestion des traductions
//                foreach ($stationCarteIdentite->getTraductions() as $stationCarteIdentiteTraduc) {
////                récupération de la langue sur le site distant
//                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $stationCarteIdentiteTraduc->getLangue()->getId()));
//
////                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
//                    if (empty(($stationCarteIdentiteTraducSite = $emSite->getRepository(StationCarteIdentiteTraduction::class)->findOneBy(array(
//                        'stationCarteIdentite' => $stationCarteIdentiteSite,
//                        'langue' => $langue
//                    ))))
//                    ) {
//                        $stationCarteIdentiteTraducSite = new StationCarteIdentiteTraduction();
//                    }
//
////                copie des données traductions
//                    $stationCarteIdentiteTraducSite->setLangue($langue)
//                        ->setLibelle($stationCarteIdentiteTraduc->getLibelle())
//                        ->setParking($stationCarteIdentiteTraduc->getParking())
//                        ->setStationCarteIdentite($stationCarteIdentiteSite);
//
////                ajout a la collection de traduction de la stationCarteIdentite distante
//                    $stationCarteIdentiteSite->addTraduction($stationCarteIdentiteTraducSite);
//                }

                $entitySite->addStationCarteIdentite($stationCarteIdentiteSite);
                $emSite->persist($entitySite);
                $emSite->flush();
            }
        }
        $this->ajouterStationCarteIdentiteUnifieSiteDistant($entity->getId(), $entity->getStationCarteIdentites());
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas de stationCarteIdentite a enregistrer
     * @param $idUnifie
     * @param $stationCarteIdentites
     */
    private function ajouterStationCarteIdentiteUnifieSiteDistant($idUnifie, $stationCarteIdentites)
    {
        /** @var ArrayCollection $stationCarteIdentites */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        echo $idUnifie;
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($stationCarteIdentites->matching($criteres)) == 0 && (empty($emSite->getRepository(StationCarteIdentiteUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entity = new StationCarteIdentiteUnifie();
                $emSite->persist($entity);
                $emSite->flush();
                // todo: signaler si l'id est différent de celui de la base CRM
//                echo 'ajouter ' . $site->getLibelle();
            }
        }
    }

    /**
     * Finds and displays a StationCarteIdentiteUnifie entity.
     *
     */
    public function showAction(StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $deleteForm = $this->createDeleteForm($stationCarteIdentiteUnifie);

        return $this->render('@MondofuteStation/stationcarteidentiteunifie/show.html.twig', array(
            'stationCarteIdentiteUnifie' => $stationCarteIdentiteUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a StationCarteIdentiteUnifie entity.
     *
     * @param StationCarteIdentiteUnifie $stationCarteIdentiteUnifie The StationCarteIdentiteUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stationcarteidentite_delete', array('id' => $stationCarteIdentiteUnifie->getId())))
            ->add('delete', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing StationCarteIdentiteUnifie entity.
     *
     */
    public function editAction(Request $request, StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {

//            récupère les sites ayant la région d'enregistrée
            foreach ($stationCarteIdentiteUnifie->getStationCarteIdentites() as $stationCarteIdentite) {
                array_push($sitesAEnregistrer, $stationCarteIdentite->getSite()->getId());
            }
        } else {

//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalStationCarteIdentites = new ArrayCollection();
//          Créer un ArrayCollection des objets de stationCarteIdentites courants dans la base de données
        foreach ($stationCarteIdentiteUnifie->getStationCarteIdentites() as $stationCarteIdentite) {
            $originalStationCarteIdentites->add($stationCarteIdentite);
        }

        $this->ajouterStationCarteIdentitesDansForm($stationCarteIdentiteUnifie);
//        $this->affilierEntities($stationCarteIdentiteUnifie);

        $this->stationCarteIdentitesSortByAffichage($stationCarteIdentiteUnifie);
        $deleteForm = $this->createDeleteForm($stationCarteIdentiteUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\StationBundle\Form\StationCarteIdentiteUnifieType',
            $stationCarteIdentiteUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour', 'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')));

//        dump($editForm);die;

        $editForm->handleRequest($request);
//        dump($stationCarteIdentiteUnifie);die;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->supprimerStationCarteIdentites($stationCarteIdentiteUnifie, $sitesAEnregistrer);

            // Supprimer la relation entre la stationCarteIdentite et stationCarteIdentiteUnifie
            foreach ($originalStationCarteIdentites as $stationCarteIdentite) {
                if (!$stationCarteIdentiteUnifie->getStationCarteIdentites()->contains($stationCarteIdentite)) {

                    //  suppression de la stationCarteIdentite sur le site
                    $emSite = $this->getDoctrine()->getEntityManager($stationCarteIdentite->getSite()->getLibelle());
                    $entitySite = $emSite->find(StationCarteIdentiteUnifie::class, $stationCarteIdentiteUnifie->getId());
                    $stationCarteIdentiteSite = $entitySite->getStationCarteIdentites()->first();
                    $emSite->remove($stationCarteIdentiteSite);
                    $emSite->flush();
//                    dump($stationCarteIdentite);
                    $stationCarteIdentite->setStationCarteIdentiteUnifie(null);
                    $em->remove($stationCarteIdentite);
                }
            }
            $em->persist($stationCarteIdentiteUnifie);
            $em->flush();


            $this->copieVersSites($stationCarteIdentiteUnifie);

            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationCarteIdentite a bien été modifié.'
            );

            return $this->redirectToRoute('stationcarteidentite_edit', array('id' => $stationCarteIdentiteUnifie->getId()));
        }

        return $this->render('@MondofuteStation/stationCarteIdentiteunifie/edit.html.twig', array(
            'entity' => $stationCarteIdentiteUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a StationCarteIdentiteUnifie entity.
     *
     */
    public function deleteAction(Request $request, StationCarteIdentiteUnifie $stationCarteIdentiteUnifie)
    {
        $form = $this->createDeleteForm($stationCarteIdentiteUnifie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            // Parcourir les sites non CRM
            foreach ($sitesDistants as $siteDistant) {
                // Récupérer le manager du site.
                $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                // Récupérer l'entité sur le site distant puis la suprrimer.
                $stationCarteIdentiteUnifieSite = $emSite->find(StationCarteIdentiteUnifie::class, $stationCarteIdentiteUnifie->getId());
                if (!empty($stationCarteIdentiteUnifieSite)) {
                    $emSite->remove($stationCarteIdentiteUnifieSite);
                    $emSite->flush();
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($stationCarteIdentiteUnifie);
            $em->flush();


            $session = $request->getSession();
            $session->start();

            // add flash messages
            /** @var Session $session */
            $session->getFlashBag()->add(
                'success',
                'La stationCarteIdentite a été supprimé avec succès.'
            );

        }

        return $this->redirectToRoute('stationcarteidentite_index');
    }

}

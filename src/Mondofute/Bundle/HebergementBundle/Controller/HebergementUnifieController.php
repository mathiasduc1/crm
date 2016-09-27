<?php

namespace Mondofute\Bundle\HebergementBundle\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mondofute\Bundle\CatalogueBundle\Entity\LogementPeriodeLocatif;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFournisseur;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoHebergement;
use Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoLogement;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeFournisseurUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeHebergementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogementUnifie;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStationUnifie;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\HebergementBundle\Entity\Emplacement;
use Mondofute\Bundle\HebergementBundle\Entity\EmplacementHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Hebergement;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuel;
use Mondofute\Bundle\HebergementBundle\Entity\HebergementVisuelTraduction;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType;
use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Entity\LogementUnifie;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementPeriodeBundle\Entity\LogementPeriode;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Entity\Service;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif;
use Mondofute\Bundle\ServiceBundle\Entity\TarifService;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\UniteBundle\Entity\Distance;
use Mondofute\Bundle\UniteBundle\Entity\Tarif;
use Mondofute\Bundle\UniteBundle\Entity\Unite;
use Mondofute\Bundle\UniteBundle\Entity\UniteTarif;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\CoordonneesGPS;
use Nucleus\MoyenComBundle\Entity\Pays;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HebergementUnifie controller.
 *
 */
class HebergementUnifieController extends Controller
{
    /**
     * Lists all HebergementUnifie entities.
     *
     */
    public function indexAction($page, $maxPerPage)
    {
        $em = $this->getDoctrine()->getManager();

        $count = $em
            ->getRepository('MondofuteHebergementBundle:HebergementUnifie')
            ->countTotal();
        $pagination = array(
            'page' => $page,
            'route' => 'hebergement_hebergement_index',
            'pages_count' => ceil($count / $maxPerPage),
            'route_params' => array(),
            'max_per_page' => $maxPerPage
        );

        $sortbyArray = array(
            'traductions.nom' => 'ASC'
        );

        $unifies = $this->getDoctrine()->getRepository('MondofuteHebergementBundle:HebergementUnifie')
            ->getList($page, $maxPerPage, $this->container->getParameter('locale'), $sortbyArray);

        return $this->render('@MondofuteHebergement/hebergementunifie/index.html.twig', array(
            'hebergementUnifies' => $unifies,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new HebergementUnifie entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        Liste les sites dans l'ordre d'affichage
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        $sitesAEnregistrer = $request->get('sites');

        $entityUnifie = new HebergementUnifie();

        $this->ajouterHebergementsDansForm($entityUnifie);
        $this->hebergementsSortByAffichage($entityUnifie);

        $form = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType', $entityUnifie,
            array('locale' => $request->getLocale()));
        $form->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                }
            }

            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $keyHebergement => $entity) {
                foreach ($entity->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $entity->removeEmplacement($emplacement);
//                        $em->remove($emplacement);
                    } else {
                        if (!empty($emplacement->getDistance2())) {
                            if (empty($emplacement->getDistance2()->getUnite())) {
//                                $em->remove($emplacement->getDistance2());
                                $emplacement->setDistance2(null);
                            }
                        }
                    }
                }
            }

            /** @var FournisseurHebergement $fournisseur */
            foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
                if (empty($fournisseur->getFournisseur())) {
//                    supprime le fournisseurHebergement car plus présent
                    $entityUnifie->removeFournisseur($fournisseur);
                    $em->remove($fournisseur);
                } else {
                    $fournisseur->setHebergement($entityUnifie);
                }
            }
            foreach ($entityUnifie->getServices() as $key => $serviceHebergement) {
                if (empty($request->request->get('hebergement_unifie')['services'][$key]['checkbox'])) {
//                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
////                        dump($serviceHebergementTarif);
//                        $serviceHebergement->removeTarif($serviceHebergementTarif);
//                        $em->remove($serviceHebergementTarif);
//                    }
//                    $serviceHebergement->setHebergementUnifie(null);
                    $entityUnifie->removeService($serviceHebergement);
                    $em->remove($serviceHebergement);
                } else {
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
                        $serviceHebergementTarif->setService($serviceHebergement);
                    }
                }
            }

            // ***** Gestion des Medias *****
            foreach ($request->get('hebergement_unifie')['hebergements'] as $key => $entity) {
                if (!empty($entityUnifie->getHebergements()->get($key)) && $entityUnifie->getHebergements()->get($key)->getSite()->getCrm() == 1) {
                    $entityCrm = $entityUnifie->getHebergements()->get($key);
                    if (!empty($entity['visuels'])) {
                        foreach ($entity['visuels'] as $keyVisuel => $visuel) {
                            /** @var HebergementVisuel $visuelCrm */
                            $visuelCrm = $entityCrm->getVisuels()[$keyVisuel];
                            $visuelCrm->setActif(true);
                            $visuelCrm->setHebergement($entityCrm);
                            foreach ($sites as $site) {
                                if ($site->getCrm() == 0) {
                                    /** @var Hebergement $entitySite */
                                    $entitySite = $entityUnifie->getHebergements()->filter(function (
                                        Hebergement $element
                                    ) use ($site) {
                                        return $element->getSite() == $site;
                                    })->first();
                                    if (!empty($entitySite)) {
//                                      $typeVisuel = (new ReflectionClass($visuelCrm))->getShortName();
                                        $typeVisuel = (new ReflectionClass($visuelCrm))->getName();

                                        /** @var HebergementVisuel $entityVisuel */
                                        $entityVisuel = new $typeVisuel();
                                        $entityVisuel->setHebergement($entitySite);
                                        $entityVisuel->setVisuel($visuelCrm->getVisuel());
                                        $entitySite->addVisuel($entityVisuel);
                                        foreach ($visuelCrm->getTraductions() as $traduction) {
                                            $traductionSite = new HebergementVisuelTraduction();
                                            /** @var HebergementVisuelTraduction $traduction */
                                            $traductionSite->setLibelle($traduction->getLibelle());
                                            $traductionSite->setLangue($traduction->getLangue());
                                            $entityVisuel->addTraduction($traductionSite);
                                        }
                                        if (!empty($visuel['sites']) && in_array($site->getId(), $visuel['sites'])) {
                                            $entityVisuel->setActif(true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            // ***** GESTION DES PRESTATIONS ANNEXE *****
            $this->creationPrestationsAnnnexe($entityUnifie);
            // ***** FIN GESTION DES PRESTATIONS ANNEXE *****

            // ***** GESTION DES CODE PROMO ***
            $this->gestionCodePromoHebergement($entityUnifie);
            // ***** FIN GESTION DES CODE PROMO ***

            $em->persist($entityUnifie);
            try {
                $error = false;
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                $error = true;
            }
            if (!$error) {
                $this->copieVersSites($entityUnifie);
//                die;
                $this->addFlash('success', 'l\'hébergement a bien été créé');
                return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $entityUnifie->getId()));
            }
        }
        $formView = $form->createView();
        return $this->render('@MondofuteHebergement/hebergementunifie/new.html.twig', array(
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'sites' => $sites,
            'langues' => $langues,
            'entity' => $entityUnifie,
            'form' => $formView,
        ));
    }

    /**
     * Ajouter les hébergements qui n'ont pas encore été enregistré pour les sites existant, dans le formulaire
     * @param HebergementUnifie $entityUnifie
     */
    private function ajouterHebergementsDansForm(HebergementUnifie $entityUnifie)
    {
        /** @var Hebergement $entity */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository('MondofuteLangueBundle:Langue')->findAll();
        $emplacements = $em->getRepository(Emplacement::class)->findAll();
        foreach ($sites as $site) {
            $siteExiste = false;
            foreach ($entityUnifie->getHebergements() as $entity) {
                if ($entity->getSite() == $site) {
                    $siteExiste = true;
                    foreach ($langues as $langue) {

//                        vérifie si $langue est présent dans les traductions sinon créé une nouvelle traduction pour l'ajouter à la région
                        if ($entity->getTraductions()->filter(function (HebergementTraduction $element) use (
                            $langue
                        ) {
                            return $element->getLangue() == $langue;
                        })->isEmpty()
                        ) {
                            $traduction = new HebergementTraduction();
                            $traduction->setLangue($langue);
                            $entity->addTraduction($traduction);
                        }
                    }
                    /** @var Emplacement $emplacement */
                    foreach ($emplacements as $emplacement) {
                        if ($entity->getEmplacements()->filter(function (EmplacementHebergement $element) use (
                            $emplacement
                        ) {
                            return $element->getTypeEmplacement() == $emplacement;
                        })->isEmpty()
                        ) {
                            $emplacementHebergement = new EmplacementHebergement();
                            $emplacementHebergement->setTypeEmplacement($emplacement);
                            $entity->addEmplacement($emplacementHebergement);
                        }

                    }
                    $entity->triEmplacements($this->get('translator'));
                }
            }
            if (!$siteExiste) {
//                si l'hébergement n'existe pas on créer un nouvel hébergemùent
                $entity = new Hebergement();
//                création d'une adresse
                $adresse = new Adresse();
//                $adresse->setDateCreation();
                $entity->addMoyenCom($adresse);

                $entity->setSite($site);

                // ajout des traductions
                foreach ($langues as $langue) {
                    $traduction = new HebergementTraduction();
                    $traduction->setLangue($langue);
                    $entity->addTraduction($traduction);
                }
                foreach ($emplacements as $emplacement) {
                    $emplacementHebergement = new EmplacementHebergement();
                    $emplacementHebergement->setTypeEmplacement($emplacement);
                    $entity->addEmplacement($emplacementHebergement);
                }
                $entity->triEmplacements($this->get('translator'));
                $entityUnifie->addHebergement($entity);
            }
        }
    }

    /**
     * Classe les departements par classementAffichage
     * @param HebergementUnifie $entityUnifie
     */
    private function hebergementsSortByAffichage(HebergementUnifie $entityUnifie)
    {
        /** @var ArrayIterator $iterator */

        // Trier les hébergements en fonction de leurs ordre d'affichage
        $entities = $entityUnifie->getHebergements(); // ArrayCollection data.

        // Recueillir un itérateur de tableau.
        $iterator = $entities->getIterator();
        unset($entities);

        // trier la nouvelle itération, en fonction de l'ordre d'affichage
        $iterator->uasort(function (Hebergement $a, Hebergement $b) {
            return ($a->getSite()->getClassementAffichage() < $b->getSite()->getClassementAffichage()) ? -1 : 1;
        });

        // passer le tableau trié dans une nouvelle collection
        $entities = new ArrayCollection(iterator_to_array($iterator));
        $this->traductionsSortByLangue($entities);

        // remplacé les hébergements par ce nouveau tableau (une fonction 'set' a été créé dans Station unifié)
        $entityUnifie->setHebergements($entities);
    }

    /**
     * Classe les traductions par rapport à leurs id
     * @param $entities
     */
    private function traductionsSortByLangue($entities)
    {
        /** @var ArrayIterator $iterator */
        /** @var Hebergement $entity */
        foreach ($entities as $entity) {
            $traductions = $entity->getTraductions();
            $iterator = $traductions->getIterator();
            // trier la nouvelle itération, en fonction de l'ordre d'affichage
            $iterator->uasort(function (HebergementTraduction $a, HebergementTraduction $b) {
                return ($a->getLangue()->getId() < $b->getLangue()->getId()) ? -1 : 1;
            });

            // passer le tableau trié dans une nouvelle collection
            $traductions = new ArrayCollection(iterator_to_array($iterator));
            $entity->setTraductions($traductions);
        }
    }

    private function creationPrestationsAnnnexe(HebergementUnifie $entityUnifie)
    {
        /** @var Station $stationRef */
        /** @var PrestationAnnexeHebergement $itemPrestationAnnexeHebergement */
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
        /** @var PrestationAnnexeFournisseurUnifie $prestationAnnexeFournisseurUnifie */
        /** @var PrestationAnnexeFournisseur $prestationAnnexeFournisseur */
        /** @var PrestationAnnexeHebergement $PrestationAnnexeHebergement */
        /** @var PrestationAnnexeStation $prestationAnnexeStation */
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var PrestationAnnexeStationUnifie $prestationAnnexeStationUnifie */
        /** @var Hebergement $hebergement */
        /** @var Hebergement $hebergementRef */
        $em = $this->getDoctrine()->getManager();
        $hebergementRef = $entityUnifie->getHebergements()->first();
        $stationRef = $hebergementRef->getStation();
        $prestationAnnexeHebergementUnifies = new ArrayCollection();
        $siteActifCollection = new ArrayCollection();

        // *** prestation annexe station ***
        $prestationAnnexeStationUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeStationUnifie::class)->findByStationUnifie($hebergementRef->getStation()->getStationUnifie()));

        foreach ($prestationAnnexeStationUnifies as $prestationAnnexeStationUnifie) {
            foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeStation = $prestationAnnexeStationUnifie->getPrestationAnnexeStations()->filter(function (PrestationAnnexeStation $element) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setFournisseurPrestationAnnexe($prestationAnnexeStation->getFournisseurPrestationAnnexe())
                        ->setActif($prestationAnnexeStation->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeStation->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }
        // *** fin prestation annexe station ***

        // *** prestation annexe fournisseur ***
        /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
        $sites = $em->getRepository(Site::class)->findAll();

        $whereStationUnifie = $stationRef->getStationUnifie()->getId();
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(), null, $whereStationUnifie));
            if (!$prestationAnnexeFournisseurUnifies->isEmpty()) {
                foreach ($sites as $site) {
                    foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
                        $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (PrestationAnnexeFournisseur $element) use ($site) {
                            return $element->getSite() == $site;
                        })->first();
                        if (!empty($prestationAnnexeFournisseur)) {
                            foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                                $prestationAnnexeHebergement = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->filter(function (PrestationAnnexeHebergement $element) use ($prestationAnnexeFournisseur) {
                                    return (
                                        $element->getFournisseurPrestationAnnexe() == $prestationAnnexeFournisseur->getFournisseurPrestationAnnexe()
                                        and $element->getSite() == $prestationAnnexeFournisseur->getSite()
                                    );
                                })->first();
                                if (!empty($prestationAnnexeHebergement)) {
                                    if ($prestationAnnexeFournisseur->getActif()) {
                                        $prestationAnnexeHebergement->setActif($prestationAnnexeFournisseur->getActif());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        $whereStationUnifie = $stationRef->getStationUnifie()->getId();
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(), null, $whereStationUnifie));

            foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (PrestationAnnexeFournisseur $element) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setFournisseurPrestationAnnexe($prestationAnnexeFournisseur->getFournisseurPrestationAnnexe())
                        ->setActif($prestationAnnexeFournisseur->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeFournisseur->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }


        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            $whereStation = ' IS NULL';
            $prestationAnnexeFournisseurUnifies = new ArrayCollection($em->getRepository(PrestationAnnexeFournisseurUnifie::class)->findByFournisseur($fournisseurHebergement->getFournisseur(), $whereStation));

            foreach ($prestationAnnexeFournisseurUnifies as $prestationAnnexeFournisseurUnifie) {
//                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                $prestationAnnexeHebergementUnifie = new PrestationAnnexeHebergementUnifie();
                $prestationAnnexeHebergementUnifies->add($prestationAnnexeHebergementUnifie);
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    $prestationAnnexeFournisseur = $prestationAnnexeFournisseurUnifie->getPrestationAnnexeFournisseurs()->filter(function (PrestationAnnexeFournisseur $element) use ($hebergement) {
                        return $element->getSite() == $hebergement->getSite();
                    })->first();
                    $prestationAnnexeHebergement = new PrestationAnnexeHebergement();
                    $prestationAnnexeHebergementUnifie->addPrestationAnnexeHebergement($prestationAnnexeHebergement);
                    $prestationAnnexeHebergement
                        ->setHebergement($hebergement)
                        ->setFournisseur($fournisseurHebergement->getFournisseur())
                        ->setFournisseurPrestationAnnexe($prestationAnnexeFournisseur->getFournisseurPrestationAnnexe())
                        ->setActif($prestationAnnexeFournisseur->getActif())
                        ->setSite($hebergement->getSite());
                    if ($prestationAnnexeFournisseur->getActif() && !$siteActifCollection->contains($hebergement->getSite())) {
                        $siteActifCollection->add($hebergement->getSite());
                    }
                }
            }
        }

        $tabPrestationAnnexeHebergements = new ArrayCollection();
        $tabPrestationAnnexeHebergementUnifies = new ArrayCollection();
        foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
            foreach ($prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements() as $prestationAnnexeHebergement) {
                $itemPrestationAnnexeHebergement = $tabPrestationAnnexeHebergements->filter(function (PrestationAnnexeHebergement $element) use ($prestationAnnexeHebergement) {
                    return (
                        $element->getFournisseur() == $prestationAnnexeHebergement->getFournisseur()
                        and $element->getFournisseurPrestationAnnexe() == $prestationAnnexeHebergement->getFournisseurPrestationAnnexe()
                        and $element->getHebergement() == $prestationAnnexeHebergement->getHebergement()
                        and $element->getSite() == $prestationAnnexeHebergement->getSite()
                    );
                })->first();
                if (false === $itemPrestationAnnexeHebergement) {
                    $tabPrestationAnnexeHebergements->add($prestationAnnexeHebergement);
                } else {
                    if ($itemPrestationAnnexeHebergement->getActif()) {
                        $prestationAnnexeHebergement->setActif(true);
                    }
                }
            }
        }

        foreach ($tabPrestationAnnexeHebergements as $itemPrestationAnnexeHebergement) {
            if (!$tabPrestationAnnexeHebergementUnifies->contains($itemPrestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie())) {
                $tabPrestationAnnexeHebergementUnifies->add($itemPrestationAnnexeHebergement->getPrestationAnnexeHebergementUnifie());
            }
        }

        foreach ($tabPrestationAnnexeHebergementUnifies as $tabPrestationAnnexeHebergementUnifie) {
            $em->persist($tabPrestationAnnexeHebergementUnifie);
        }

    }

    /**
     * @param HebergementUnifie $entityUnifie
     */
    private function gestionCodePromoHebergement($entityUnifie)
    {
        /** @var Hebergement $hebergement */
        /** @var FournisseurHebergement $fournisseurHebergement */
        /** @var CodePromoFournisseur $codePromoFournisseur */
        $em = $this->getDoctrine()->getManager();

        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
            if (empty($fournisseurHebergement->getId())) {
                $fournisseur = $fournisseurHebergement->getFournisseur();
                $codePromoFournisseurs = new ArrayCollection($em->getRepository(CodePromoFournisseur::class)->findBy(array('fournisseur' => $fournisseur->getId(), 'type' => 1)));
                foreach ($entityUnifie->getHebergements() as $hebergement) {
                    foreach ($codePromoFournisseurs as $codePromoFournisseur) {
                        if($codePromoFournisseur->getCodePromo()->getSite() == $hebergement->getSite())
                        {
                            $codePromoHebergement = new CodePromoHebergement();
                            $em->persist($codePromoHebergement);
                            $codePromoHebergement
                                ->setCodePromo($codePromoFournisseur->getCodePromo())
                                ->setHebergement($hebergement)
                                ->setFournisseur($codePromoFournisseur->getFournisseur());
                        }
                    }
                }
            }
        }
    }

    /**
     * Copie dans la base de données site l'entité hébergement
     * @param HebergementUnifie $entityUnifie
     */
    private function copieVersSites(HebergementUnifie $entityUnifie, $originalHebergementVisuels = null)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var HebergementTraduction $entityTraduc */
//        Boucle sur les hébergements afin de savoir sur quel site nous devons l'enregistrer
        /** @var Hebergement $entity */
        foreach ($entityUnifie->getHebergements() as $entity) {
            if ($entity->getSite()->getCrm() == false) {

//            Récupération de l'entity manager du site vers lequel nous souhaitons enregistrer
                $emSite = $this->getDoctrine()->getManager($entity->getSite()->getLibelle());
                $site = $emSite->getRepository(Site::class)->findOneBy(array('id' => $entity->getSite()->getId()));
//                $region = $emSite->getRepository(Region::class)->findOneBy(array('regionUnifie' => $departement->getRegion()->getRegionUnifie()->getId()));
                if (!empty($entity->getStation())) {
                    $stationSite = $emSite->getRepository(Station::class)->findOneBy(array('stationUnifie' => $entity->getStation()->getStationUnifie()->getId()));
                } else {
                    $stationSite = null;
                }
                if (!empty($entity->getTypeHebergement())) {
//                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $entity->getTypeHebergement()->getTypeHebergementUnifie()->getId()));
                    $typeHebergementSite = $emSite->getRepository(TypeHebergement::class)->findOneBy(array('typeHebergementUnifie' => $entity->getTypeHebergement()->getTypeHebergementUnifie()));
                } else {
                    $typeHebergementSite = null;
                }
//            GESTION EntiteUnifie
//            récupère la l'entité unifie du site ou creer une nouvelle entité unifie
                if (is_null($entityUnifieSite = $emSite->find(HebergementUnifie::class, $entityUnifie))) {
                    $new = true;
                    $entityUnifieSite = new HebergementUnifie();
                    $entityUnifieSite->setId($entityUnifie->getId());
                    $metadata = $emSite->getClassMetadata(get_class($entityUnifieSite));
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

                /** @var FournisseurHebergement $fournisseur */
                /** @var FournisseurHebergement $fournisseurSite */
//                supprime les fournisseurHebergement du site distant
                if (!empty($entityUnifieSite->getFournisseurs())) {
                    foreach ($entityUnifieSite->getFournisseurs() as $fournisseurSite) {
                        $present = false;
                        foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
                            if ($fournisseurSite->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId() && $fournisseurSite->getHebergement()->getId() == $fournisseur->getHebergement()->getId()) {
                                $present = true;
                            }
                        }
                        if ($present == false) {
                            // *** suppression des code promo logement ***
                            foreach ($entityUnifieSite->getHebergements() as $hebergement)
                            {
                                $codePromoHebergements = $emSite->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement->getId() , 'fournisseur' => $fournisseurSite->getFournisseur()->getId()));
                                foreach ($codePromoHebergements as $codePromoHebergement){
                                    $emSite->remove($codePromoHebergement);
                                }
                            }
                            // *** fin suppression des code promo logement ***
                            $entityUnifieSite->removeFournisseur($fournisseurSite);
                            $emSite->remove($fournisseurSite);
                        }
                    }
                }
//                copie des services hebergement vers les sites distants
                /** @var ServiceHebergement $service */
                foreach ($entityUnifie->getServices() as $service) {
                    if (empty($serviceSite = $emSite->getRepository(ServiceHebergement::class)->findOneBy(array(
                        'hebergementUnifie' => $entityUnifie->getId(),
                        'service' => $service->getId(),
                    )))
                    ) {
                        $serviceSite = new ServiceHebergement();
                        $serviceSite->setHebergementUnifie($entityUnifieSite);
                        $entityUnifieSite->addService($serviceSite);

                    }
                    $serviceSite->setService($emSite->getRepository(Service::class)->find($service->getService()->getId()));

                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($service->getTarifs() as $serviceHebergementTarif) {
                        if (empty($serviceHebergementTarifSite = $emSite->getRepository(ServiceHebergementTarif::class)->find(
                            $serviceHebergementTarif->getId()
                        ))
                        ) {
                            $serviceHebergementTarifSite = new ServiceHebergementTarif();
                            $serviceSite->addTarif($serviceHebergementTarifSite);
                        }

                        if (empty(($tarifSite = $serviceHebergementTarifSite->getTarif()))) {
                            $tarifSite = new Tarif();
                        }
                        /** @var Tarif $tarifSite */
                        $tarifSite->setUnite($emSite->getRepository(UniteTarif::class)->find($serviceHebergementTarif->getTarif()->getUnite()->getId()))
                            ->setValeur($serviceHebergementTarif->getTarif()->getValeur());
                        $serviceHebergementTarifSite->setService($serviceSite)
                            ->setTarif($tarifSite)
                            ->setTypePeriode($emSite->getRepository(TypePeriode::class)->find($serviceHebergementTarif->getTypePeriode()->getId()));
                        $emSite->persist($tarifSite);
                        $emSite->persist($serviceHebergementTarifSite);
                    }
                    $emSite->persist($serviceSite);
                }
//                balaye les fournisseurHebergement et copie les données
                foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
                    if (empty($fournisseurSite = $emSite->getRepository(FournisseurHebergement::class)->findOneBy(array(
                        'fournisseur' => $fournisseur->getFournisseur(),
                        'hebergement' => $fournisseur->getHebergement()
                    )))
                    ) {
//                        initialise un objet
                        $fournisseurSite = new FournisseurHebergement();
                    }
                    foreach ($fournisseurSite->getReceptions() as $receptionSite) {
                        $fournisseurSite->removeReception($receptionSite);
                    }
                    foreach ($fournisseur->getReceptions() as $reception) {
                        if (empty($receptionSite = $emSite->getRepository(Reception::class)->find($reception))) {

                        } else {
                            $fournisseurSite->addReception($receptionSite);
                        }
                    }
                    /** @var FournisseurHebergementTraduction $traduction */
                    foreach ($fournisseur->getTraductions() as $traduction) {
                        if (empty($fournisseurHebergementTraduction = $emSite->getRepository(FournisseurHebergementTraduction::class)->findOneBy(array(
                            'fournisseurHebergement' => $traduction->getFournisseurHebergement(),
                            'langue' => $traduction->getLangue()
                        )))
                        ) {
                            $fournisseurHebergementTraduction = new FournisseurHebergementTraduction();
                            $fournisseurHebergementTraduction->setLangue($emSite->getRepository(Langue::class)->findOneBy(array('id' => $traduction->getLangue()->getId())));
                            $fournisseurHebergementTraduction->setFournisseurHebergement($fournisseurSite);
                        }
                        $fournisseurHebergementTraduction->setAcces($traduction->getAcces());
                        $fournisseurSite->addTraduction($fournisseurHebergementTraduction);
                    }
                    $this->dupliqueFounisseurHebergement($fournisseur, $fournisseurSite, $emSite);
                    $fournisseurSite->setHebergement($entityUnifieSite)
                        ->setFournisseur($emSite->getRepository(Fournisseur::class)->findOneBy(array('id' => $fournisseur->getFournisseur()->getId())));
                    $fournisseurSite->setRemiseClef($emSite->getRepository(RemiseClef::class)->findOneBy(array('id' => $fournisseur->getRemiseClef()->getId())));
                    $entityUnifieSite->addFournisseur($fournisseurSite);
                }
//            Récupération de l'hébergement sur le site distant si elle existe sinon créer une nouvelle entité
                if (empty(($entitySite = $emSite->getRepository(Hebergement::class)->findOneBy(array('hebergementUnifie' => $entityUnifieSite))))) {
                    $entitySite = new Hebergement();
                }

                $classementSite = !empty($entitySite->getClassement()) ? $entitySite->getClassement() : clone $entity->getClassement();
                /** @var Adresse $adresse */
                /** @var CoordonneesGPS $coordonneesGPSSite */
                /** @var Adresse $adresseSite */
                $adresse = $entity->getMoyenComs()->first();
                if (!empty($entitySite->getMoyenComs())) {
                    $adresseSite = $entitySite->getMoyenComs()->first();
//                    $adresseSite->setDateModification(new DateTime());
                } else {
                    $adresseSite = new Adresse();
//                    $adresseSite->setDateCreation();
                    $adresseSite->setCoordonneeGps(new CoordonneesGPS());
                    $entitySite->addMoyenCom($adresseSite);
                }
                $adresseSite->setVille($adresse->getVille());
                $adresseSite->setAdresse1($adresse->getAdresse1());
                $adresseSite->setAdresse2($adresse->getAdresse2());
                $adresseSite->setAdresse3($adresse->getAdresse3());
                $adresseSite->setCodePostal($adresse->getCodePostal());
                $adresseSite->setPays($emSite->find(Pays::class, $adresse->getPays()));
                $adresseSite->getCoordonneeGps()
                    ->setLatitude($adresse->getCoordonneeGps()->getLatitude())
                    ->setLongitude($adresse->getCoordonneeGps()->getLongitude())
                    ->setPrecis($adresse->getCoordonneeGps()->getPrecis());
                if (!empty($classementSite->getUnite())) {
                    $uniteSite = $emSite->getRepository(Unite::class)->findOneBy(array('id' => $entity->getClassement()->getUnite()->getId()));
                } else {
                    $uniteSite = null;
                }
                $classementSite->setValeur($entity->getClassement()->getValeur());
                $classementSite->setUnite($uniteSite);

//            copie des données hébergement
                $entitySite
                    ->setSite($site)
                    ->setStation($stationSite)
                    ->setTypeHebergement($typeHebergementSite)
                    ->setClassement($classementSite)
                    ->setHebergementUnifie($entityUnifieSite)
                    ->setActif($entity->getActif());
//                GESTION DES EMPLACEMENTS
                $this->gestionEmplacementsSiteDistant($site, $entity, $entitySite);

//            Gestion des traductions
                foreach ($entity->getTraductions() as $entityTraduc) {
//                récupération de la langue sur le site distant
                    $langue = $emSite->getRepository(Langue::class)->findOneBy(array('id' => $entityTraduc->getLangue()->getId()));

//                récupération de la traduction sur le site distant ou création d'une nouvelle traduction si elle n'existe pas
                    if (empty(($entityTraducSite = $emSite->getRepository(HebergementTraduction::class)->findOneBy(array(
                        'hebergement' => $entitySite,
                        'langue' => $langue
                    ))))
                    ) {
                        $entityTraducSite = new HebergementTraduction();
                    }

//                copie des données traductions
                    $entityTraducSite->setLangue($langue)
                        ->setActivites($entityTraduc->getActivites())
                        ->setAvisMondofute($entityTraduc->getActivites())
                        ->setBienEtre($entityTraduc->getBienEtre())
                        ->setNom($entityTraduc->getNom())
                        ->setPourLesEnfants($entityTraduc->getPourLesEnfants())
                        ->setRestauration($entityTraduc->getRestauration())
                        ->setHebergement($entityTraduc->getHebergement());

//                ajout a la collection de traduction de l'hébergement
                    $entitySite->addTraduction($entityTraducSite);
                }

                // ********** GESTION DES MEDIAS **********

                $entityVisuels = $entity->getVisuels(); // ce sont les hebegementVisuels ajouté

                // si il y a des Medias pour l'hebergement de référence
                if (!empty($entityVisuels) && !$entityVisuels->isEmpty()) {
                    // si il y a des medias pour l'hébergement présent sur le site
                    // (on passera dans cette condition, seulement si nous sommes en edition)
                    if (!empty($entitySite->getVisuels()) && !$entitySite->getVisuels()->isEmpty()) {
                        // on ajoute les hébergementVisuels dans un tableau afin de travailler dessus
                        $entityVisuelSites = new ArrayCollection();
                        foreach ($entitySite->getVisuels() as $entityvisuelSite) {
                            $entityVisuelSites->add($entityvisuelSite);
                        }
                        // on parcourt les hébergmeentVisuels de la base
                        /** @var HebergementVisuel $entityVisuel */
                        foreach ($entityVisuels as $entityVisuel) {
                            // *** récupération de l'hébergementVisuel correspondant sur la bdd distante ***
                            // récupérer l'hebergementVisuel original correspondant sur le crm
                            /** @var ArrayCollection $originalHebergementVisuels */
                            $originalHebergementVisuel = $originalHebergementVisuels->filter(function (HebergementVisuel $element) use ($entityVisuel) {
                                return $element->getVisuel() == $entityVisuel->getVisuel();
                            })->first();
                            unset($entityVisuelSite);
                            if ($originalHebergementVisuel !== false) {
                                $tab = new ArrayCollection();
                                foreach ($originalHebergementVisuels as $item) {
                                    if (!empty($item->getId())) {
                                        $tab->add($item);
                                    }
                                }
                                $keyoriginalVisuel = $tab->indexOf($originalHebergementVisuel);

                                $entityVisuelSite = $entityVisuelSites->get($keyoriginalVisuel);
                            }
                            // *** fin récupération de l'hébergementVisuel correspondant sur la bdd distante ***

                            // si l'hebergementVisuel existe sur la bdd distante, on va le modifier
                            /** @var HebergementVisuel $entityVisuelSite */
                            if (!empty($entityVisuelSite)) {
                                // Si le visuel a été modifié
                                // (que le crm_ref_id est différent de de l'id du visuel de l'hebergementVisuel du crm)
                                if ($entityVisuelSite->getVisuel()->getMetadataValue('crm_ref_id') != $entityVisuel->getVisuel()->getId()) {
                                    $cloneVisuel = clone $entityVisuel->getVisuel();
                                    $cloneVisuel->setMetadataValue('crm_ref_id', $entityVisuel->getVisuel()->getId());
                                    $cloneVisuel->setContext('hebergement_visuel_' . $entity->getSite()->getLibelle());

                                    // on supprime l'ancien visuel
                                    $emSite->remove($entityVisuelSite->getVisuel());
                                    $this->deleteFile($entityVisuelSite->getVisuel());

                                    $entityVisuelSite->setVisuel($cloneVisuel);
                                }

                                $entityVisuelSite->setActif($entityVisuel->getActif());

                                // on parcourt les traductions
                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($entityVisuel->getTraductions() as $traduction) {
                                    // on récupère la traduction correspondante
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    /** @var ArrayCollection $traductionSites */
                                    $traductionSites = $entityVisuelSite->getTraductions();

                                    unset($traductionSite);
                                    if (!$traductionSites->isEmpty()) {
                                        // on récupère la traduction correspondante en fonction de la langue
                                        $traductionSite = $traductionSites->filter(function (HebergementVisuelTraduction $element) use ($traduction) {
                                            return $element->getLangue()->getId() == $traduction->getLangue()->getId();
                                        })->first();
                                    }
                                    // si une traduction existe pour cette langue, on la modifie
                                    if (!empty($traductionSite)) {
                                        $traductionSite->setLibelle($traduction->getLibelle());
                                    } // sinon on en cré une
                                    else {
                                        $traductionSite = new HebergementVisuelTraduction();
                                        $traductionSite->setLibelle($traduction->getLibelle())
                                            ->setLangue($emSite->find(Langue::class, $traduction->getLangue()->getId()));
                                        $entityVisuelSite->addTraduction($traductionSite);
                                    }
                                }
                            } // sinon on va le créer
                            else {
                                $this->createHebergementVisuel($entityVisuel, $entitySite, $emSite);
                            }
                        }
                    } // sinon si l'hébergement de référence n'a pas de medias
                    else {
                        // on lui cré alors les medias
                        // on parcours les medias de l'hebergement de référence
                        /** @var HebergementVisuel $entityVisuel */
                        foreach ($entityVisuels as $entityVisuel) {
                            $this->createHebergementVisuel($entityVisuel, $entitySite, $emSite);
                        }
                    }
                } // sinon on doit supprimer les medias présent pour l'hébergement correspondant sur le site distant
                else {
                    if (!empty($entityVisuelSites)) {
                        /** @var HebergementVisuel $entityVisuelSite */
                        foreach ($entityVisuelSites as $entityVisuelSite) {
                            $entityVisuelSite->setHebergement(null);
                            $emSite->remove($entityVisuelSite->getVisuel());
                            $this->deleteFile($entityVisuelSite->getVisuel());
                            $emSite->remove($entityVisuelSite);
                        }
                    }
                }
                // ********** FIN GESTION DES MEDIAS **********

                // ********** GESTION DES PRESTATIONS ANNEXE AFFECTATION **********
                // *** prestationAnnexeHebergements ***
                if (!empty($new)) {
                    $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByHebergementUnifie($entityUnifie->getId());
                    /** @var PrestationAnnexeHebergementUnifie $prestationAnnexeHebergementUnifie */
                    /** @var PrestationAnnexeHebergement $prestationAnnexeHebergement */
                    foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                        $prestationAnnexeHebergementUnifieSite = new PrestationAnnexeHebergementUnifie();
                        $emSite->persist($prestationAnnexeHebergementUnifieSite);
                        $prestationAnnexeHebergementUnifieSite->setId($prestationAnnexeHebergementUnifie->getId());
                        $metadata = $emSite->getClassMetadata(get_class($prestationAnnexeHebergementUnifieSite));
                        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                        $prestationAnnexeHebergement = $prestationAnnexeHebergementUnifie->getPrestationAnnexeHebergements()->filter(function (PrestationAnnexeHebergement $element) use ($site) {
                            return $element->getSite()->getId() == $site->getId();
                        })->first();

                        $prestationAnnexeHebergementSite = new PrestationAnnexeHebergement();
                        $prestationAnnexeHebergementUnifieSite->addPrestationAnnexeHebergement($prestationAnnexeHebergementSite);
                        $prestationAnnexeHebergementSite
                            ->setFournisseur($emSite->find(Fournisseur::class, $prestationAnnexeHebergement->getFournisseur()))
                            ->setHebergement($entitySite)
                            ->setActif($prestationAnnexeHebergement->getActif())
                            ->setSite($emSite->find(Site::class, $site))
                            ->setFournisseurPrestationAnnexe($emSite->find(FournisseurPrestationAnnexe::class, $prestationAnnexeHebergement->getFournisseurPrestationAnnexe()));
                    }
                }
                // *** fin prestationAnnexeHebergements ***
                // ********** FIN GESTION DES PRESTATIONS ANNEXE AFFECTATION **********


                // *** gestion code promo hebergement ***
                /** @var FournisseurHebergement $fournisseurHebergement */
                foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
                    $fournisseur = $fournisseurHebergement->getFournisseur();
                    $codePromoHebergements = new ArrayCollection($em->getRepository(CodePromoHebergement::class)->findBySite($fournisseur->getId(), $site->getId()));
                    $codePromoHebergementSites = new ArrayCollection($emSite->getRepository(CodePromoHebergement::class)->findBySite($fournisseur->getId(), $site->getId()));
                    if (!empty($codePromoHebergements) && !$codePromoHebergements->isEmpty()) {
                        /** @var CodePromoHebergement $codePromoHebergement */
                        foreach ($codePromoHebergements as $codePromoHebergement) {
                            $codePromoHebergementSite = $codePromoHebergementSites->filter(function (CodePromoHebergement $element) use ($codePromoHebergement) {
                                return $element->getId() == $codePromoHebergement->getId();
                            })->first();
                            if (false === $codePromoHebergementSite) {
                                $codePromoHebergementSite = new CodePromoHebergement();
//                            $entitySite->addCodePromoFournisseurPrestationAnnex($codePromoHebergementSite);
                                $emSite->persist($codePromoHebergementSite);
                                $codePromoHebergementSite
                                    ->setId($codePromoHebergement->getId());

                                $metadata = $emSite->getClassMetadata(get_class($codePromoHebergementSite));
                                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            }

                            $codePromo = $emSite->getRepository(CodePromo::class)->findOneBy(array('codePromoUnifie' => $codePromoHebergement->getCodePromo()->getCodePromoUnifie()));

                            $codePromoHebergementSite
                                ->setCodePromo($codePromo)
                                ->setFournisseur($emSite->find(Fournisseur::class, $codePromoHebergement->getFournisseur()))
                                ->setHebergement($entitySite);
                        }
                    }

                    if (!empty($codePromoHebergementSites) && !$codePromoHebergementSites->isEmpty()) {
                        /** @var CodePromoHebergement $codePromoHebergement */
                        foreach ($codePromoHebergementSites as $codePromoHebergementSite) {
                            $codePromoHebergement = $codePromoHebergements->filter(function (CodePromoHebergement $element) use ($codePromoHebergementSite) {
                                return $element->getId() == $codePromoHebergementSite->getId();
                            })->first();
                            if (false === $codePromoHebergement) {
//                            $entitySite->removeCodePromoHebergement($codePromoHebergementSite);
                                $emSite->remove($codePromoHebergementSite);
                            }
                        }
                    }
                }
                // *** fin gestion code promo hebergement ***

                $entityUnifieSite->addHebergement($entitySite);
                $emSite->persist($entityUnifieSite);
                $emSite->flush();
            }
        }
        $this->ajouterHebergementUnifieSiteDistant($entityUnifie->getId(), $entityUnifie);
    }

    /**
     * @param $fournisseur
     * @param $fournisseurSite
     */
    public function dupliqueFounisseurHebergement(
        FournisseurHebergement $fournisseur,
        FournisseurHebergement $fournisseurSite,
        $emSite
    )
    {
//        récupération des données fournisseur
        $adresseFournisseur = $fournisseur->getAdresse();
        $telFixeFournisseur = $fournisseur->getTelFixe();
        $telMobileFournisseur = $fournisseur->getTelMobile();
        /** @var CoordonneesGPS $coordonneesGPSFournisseur */
        $coordonneesGPSFournisseur = $fournisseur->getAdresse()->getCoordonneeGps();

//        récupération des données fournisseurSite
        $adresseFournisseurSite = $fournisseurSite->getAdresse();
        $coordonneesGPSFournisseurSite = $fournisseurSite->getAdresse()->getCoordonneeGps();
        $telFixeFournisseurSite = $fournisseurSite->getTelFixe();
        $telMobileFournisseurSite = $fournisseurSite->getTelMobile();

//                    Copie des données du fournisseurHebergement
        $coordonneesGPSFournisseurSite->setLatitude($coordonneesGPSFournisseur->getLatitude())
            ->setLongitude($coordonneesGPSFournisseur->getLongitude())
            ->setPrecis($coordonneesGPSFournisseur->getPrecis());
        $adresseFournisseurSite->setAdresse1($adresseFournisseur->getAdresse1())
            ->setAdresse2($adresseFournisseur->getAdresse2())
            ->setAdresse3($adresseFournisseur->getAdresse3())
            ->setCodePostal($adresseFournisseur->getCodePostal())
            ->setVille($adresseFournisseur->getVille())
            ->setPays($emSite->find(Pays::class, $adresseFournisseur->getPays()))
            ->setCoordonneeGps($coordonneesGPSFournisseurSite);
        $telFixeFournisseurSite->setNumero($telFixeFournisseur->getNumero());
        $telMobileFournisseurSite
            ->setSmsing($telMobileFournisseur->getSmsing())
            ->setNumero($telMobileFournisseur->getNumero());
    }

    public function gestionEmplacementsSiteDistant(Site $site, Hebergement $entity, Hebergement $entitySite)
    {
        /** @var EmplacementHebergement $emplacement */
        /** @var EmplacementHebergement $emplacementSite */
//        Suppression des emplacements qui ne sont plus présents
        $emSite = $this->getDoctrine()->getManager($site->getLibelle());
        $emplacementsSite = $emSite->getRepository(EmplacementHebergement::class)->findBy(array('hebergement' => $entitySite));
        foreach ($emplacementsSite as $emplacementSite) {
            $present = 0;
            foreach ($entity->getEmplacements() as $emplacement) {
                if ($emplacementSite->getTypeEmplacement() == $emplacement->getTypeEmplacement()) {
                    $present = 1;
                }
            }
            if ($present == 0) {
                $emSite->remove($emplacementSite);
            }
        }

        foreach ($entity->getEmplacements() as $emplacement) {
            if (!empty(($distance1 = $emplacement->getDistance1()))) {
                $uniteSite1 = $emSite->getRepository(Unite::class)->find($distance1->getUnite());
            } else {
                $uniteSite1 = null;
            }
            if (!empty(($distance2 = $emplacement->getDistance2()))) {
                $uniteSite2 = $emSite->getRepository(Unite::class)->find($distance2->getUnite());
            } else {
                $uniteSite2 = null;
            }
            $typeEmplacementSite = $emSite->getRepository(Emplacement::class)->find($emplacement->getTypeEmplacement());
            if (empty(($emplacementSite = $emSite->getRepository(EmplacementHebergement::class)->findOneBy(array(
                'typeEmplacement' => $typeEmplacementSite,
                'hebergement' => $entitySite
            ))))
            ) {
                $emplacementSite = new EmplacementHebergement();
                if (!empty($distance1)) {
                    $distanceSite1 = new Distance();
                }
                if (!empty($distance2)) {
                    $distanceSite2 = new Distance();
                }
            } else {
                if (!empty($distance1)) {
                    if (empty(($distanceSite1 = $emplacementSite->getDistance1()))) {
                        $distanceSite1 = new Distance();
                    }
                } else {
                    if (!empty(($distanceSite1 = $emplacementSite->getDistance1()))) {
                        $emSite->remove($distanceSite1);
                        $distanceSite1 = null;
                    }
                }
                if (!empty($distance2)) {
                    if (empty(($distanceSite2 = $emplacementSite->getDistance2()))) {
                        $distanceSite2 = new Distance();
                    }
                } else {
                    if (!empty(($distanceSite2 = $emplacementSite->getDistance2()))) {
                        $emSite->remove($distanceSite2);
                        $distanceSite2 = null;
                    }
                }
            }
            if (!empty($distance1)) {
                $distanceSite1->setValeur($distance1->getValeur());
                $distanceSite1->setUnite($uniteSite1);
                $emplacementSite->setDistance1($distanceSite1);
            }
            if (!empty($distance2)) {
                $distanceSite2->setValeur($distance2->getValeur());
                $distanceSite2->setUnite($uniteSite2);
                $emplacementSite->setDistance2($distanceSite2);
            }

            $emplacementSite->setTypeEmplacement($typeEmplacementSite)
                ->setDistance1($distanceSite1)
                ->setTypeEmplacement($typeEmplacementSite)
                ->setDistance2($distanceSite2);
            $entitySite->addEmplacement($emplacementSite);
        }
        $emSite->flush();
    }

    private function deleteFile($visuel)
    {
        if (file_exists($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg')) {
            unlink($this->container->getParameter('chemin_media') . $visuel->getContext() . '/0001/01/thumb_' . $visuel->getId() . '_reference.jpg');
        }
    }

    /**
     * Création d'un nouveau hebergementVisuel
     * @param HebergementVisuel $entityVisuel
     * @param Hebergement $entitySite
     * @param EntityManager $emSite
     */
    private function createHebergementVisuel(HebergementVisuel $entityVisuel, Hebergement $entitySite, EntityManager $emSite)
    {
        /** @var HebergementVisuel $entityVisuelSite */
        // on récupère la classe correspondant au visuel (photo ou video)
        $typeVisuel = (new ReflectionClass($entityVisuel))->getName();
        // on cré un nouveau HebergementVisuel on fonction du type
        $entityVisuelSite = new $typeVisuel();
        $entityVisuelSite->setHebergement($entitySite);
        $entityVisuelSite->setActif($entityVisuel->getActif());
        // on lui clone l'image
        $cloneVisuel = clone $entityVisuel->getVisuel();

        // **** récupération du visuel physique ****
        $pool = $this->container->get('sonata.media.pool');
        $provider = $pool->getProvider($cloneVisuel->getProviderName());
        $provider->getReferenceImage($cloneVisuel);

        // c'est ce qui permet de récupérer le fichier lorsqu'il est nouveau todo:(à mettre en variable paramètre => parameter.yml)
//        $cloneVisuel->setBinaryContent(__DIR__ . "/../../../../../web/uploads/media/" . $provider->getReferenceImage($cloneVisuel));
        $cloneVisuel->setBinaryContent($this->container->getParameter('chemin_media') . $provider->getReferenceImage($cloneVisuel));

        $cloneVisuel->setProviderReference($entityVisuel->getVisuel()->getProviderReference());
        $cloneVisuel->setName($entityVisuel->getVisuel()->getName());
        // **** fin récupération du visuel physique ****

        // on donne au nouveau visuel, le context correspondant en fonction du site
        $cloneVisuel->setContext('hebergement_visuel_' . $entitySite->getSite()->getLibelle());
        // on lui attache l'id de référence du visuel correspondant sur la bdd crm
        $cloneVisuel->setMetadataValue('crm_ref_id', $entityVisuel->getVisuel()->getId());

        $entityVisuelSite->setVisuel($cloneVisuel);

        $entitySite->addVisuel($entityVisuelSite);
        // on ajoute les traductions correspondante
        foreach ($entityVisuel->getTraductions() as $traduction) {
            $traductionSite = new HebergementVisuelTraduction();
            $traductionSite->setLibelle($traduction->getLibelle())
                ->setLangue($emSite->find(Langue::class, $traduction->getLangue()));
            $entityVisuelSite->addTraduction($traductionSite);
        }
    }

    /**
     * Ajoute la reference site unifie dans les sites n'ayant pas d'hébergement a enregistrer
     * @param $idUnifie
     * @param $entityUnifie
     */
    private function ajouterHebergementUnifieSiteDistant($idUnifie, HebergementUnifie $entityUnifie)
    {
        /** @var ArrayCollection $entities */
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        //        récupération
        $sites = $em->getRepository('MondofuteSiteBundle:Site')->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $criteres = Criteria::create()
                ->where(Criteria::expr()->eq('site', $site));
            if (count($entityUnifie->getHebergements()->matching($criteres)) == 0 && (empty($emSite->getRepository(HebergementUnifie::class)->findBy(array('id' => $idUnifie))))) {
                $entityUnifie = new HebergementUnifie();
//                foreach ($entityUnifie->getFournisseurs() as $fournisseur) {
//                    $entityUnifie->addFournisseur($fournisseur);
//                }
                $emSite->persist($entityUnifie);
                $emSite->flush();
            }
        }
    }

    /**
     * Finds and displays a HebergementUnifie entity.
     *
     */
    public function showAction(HebergementUnifie $entityUnifie)
    {
        $deleteForm = $this->createDeleteForm($entityUnifie);
        return $this->render('@MondofuteHebergement/hebergementunifie/show.html.twig', array(
            'hebergementUnifie' => $entityUnifie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a HebergementUnifie entity.
     *
     * @param HebergementUnifie $entityUnifie The HebergementUnifie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(HebergementUnifie $entityUnifie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hebergement_hebergement_delete',
                array('id' => $entityUnifie->getId())))
            ->add('delete', SubmitType::class, array('label' => 'Supprimer'))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function chargerListeServicesFournisseurAction(Request $request, $idFournisseur)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            /** @var ArrayCollection $liste */
            $liste = $em->getRepository(ListeService::class)->chargerParFournisseur($idFournisseur)->getQuery()->getArrayResult();
//            $listeArray = $liste->toArray();
//            $serializer = $this->container->get('serializer');
            $response = new Response();
//            $data = $serializer->serialize($liste,'json');
            $data = json_encode($liste); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        } else {
            return new Response();
        }
    }

    public function chargerServicesXMLAction(Request $request, $idListeService, $idHebergementUnifie = null)
    {
        if ($request->isXmlHttpRequest()) {
//        $enseigne = $request->get('enseigne');
            $em = $this->getDoctrine()->getManager();
            if ($idHebergementUnifie <= 0) {
                $entityUnifie = new HebergementUnifie();
//            $services = $em->getRepository(Service::class)->findBy(array('listeService'=>$idListeService));

            } else {
                $entityUnifie = $em->getRepository(HebergementUnifie::class)->find($idHebergementUnifie);
                if (empty($entityUnifie->getListeService()) || ($entityUnifie->getListeService()->getId() != $idListeService)) {
                    $entityUnifie->getServices()->clear();
                }
            }
            $entityUnifie->setListeService($em->getRepository(ListeService::class)->find($idListeService));
            $this->genererServiceHebergements($entityUnifie);
            $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
                $entityUnifie, array('locale' => $request->getLocale()));
            $html = $this->render('@MondofuteHebergement/hebergementunifie/tableau_services_hebergement.html.twig',
                array('form' => $editForm->createView()));
//        $fournisseurs = $em->getRepository('MondofuteFournisseurBundle:Fournisseur')->rechercherTypeHebergement($enseigne)->getQuery()->getArrayResult();

//            $response = new Response();
//
//            $data = json_encode(null); // formater le résultat de la requête en json
//
//            $response->headers->set('Content-Type', 'application/json');
//            $response->setContent($data);

            return $html;
        }
        return new Response();
    }

    public function genererServiceHebergements(HebergementUnifie $entityUnifie)
    {
        $services = new ArrayCollection();
        /** @var ServiceHebergement $serv */
        foreach ($entityUnifie->getServices() as $serv) {
            $services->add($serv->getService());
        }
        /** @var Service $service */
        if (!empty($entityUnifie->getListeService())) {
            foreach ($entityUnifie->getListeService()->getServices() as $service) {
                if (!($services->contains($service))) {
                    $serviceHebergement = new ServiceHebergement();
                    $serviceHebergement->setService($service);
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var TarifService $tarifService */
                    foreach ($service->getTarifs() as $tarifService) {
                        $tarifHebergement = new ServiceHebergementTarif();
                        $tarifHebergement->setService($serviceHebergement);
                        $tarifHebergement->setTypePeriode($tarifService->getTypePeriode());
                        $tarif = new Tarif();
                        $tarif->setUnite($tarifService->getTarif()->getUnite())
                            ->setValeur($tarifService->getTarif()->getValeur());
                        $tarifHebergement->setTarif($tarif);
                        $serviceHebergement->addTarif($tarifHebergement);
                    }
                    $entityUnifie->addService($serviceHebergement);
                }
            }
        }
    }

    /**
     * Displays a form to edit an existing HebergementUnifie entity.
     *
     */
    public function editAction(Request $request, HebergementUnifie $entityUnifie)
    {

        $em = $this->getDoctrine()->getManager();
//        $typePeriodes = $em->getRepository(TypePeriode::class)->findAll();
//        $typePeriodes = new ArrayCollection();
//        $periodes = $em->getRepository(Periode::class)->findAll();
        $sites = $em->getRepository(Site::class)->findBy(array(), array('classementAffichage' => 'asc'));
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('id' => 'ASC'));

        $originalServices = new ArrayCollection();
        $originalTarifs = new ArrayCollection();
        /** @var ServiceHebergement $serviceHebergement */
        foreach ($entityUnifie->getServices() as $serviceHebergement) {
            foreach ($serviceHebergement->getTarifs() as $originalTarif) {
                $originalTarifs->add($originalTarif);
            }
            $originalServices->add($serviceHebergement);
        }

        $this->genererServiceHebergements($entityUnifie);

//        si request(site) est null nous sommes dans l'affichage de l'edition sinon nous sommes dans l'enregistrement
        $sitesAEnregistrer = array();
        if (empty($request->get('sites'))) {
//            récupère les sites ayant la région d'enregistrée
            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $entity) {
                if ($entity->getActif()) {
                    array_push($sitesAEnregistrer, $entity->getSite()->getId());
                }
            }
        } else {
//            récupère les sites cochés
            $sitesAEnregistrer = $request->get('sites');
        }

        $originalHebergementVisuels = new ArrayCollection();
        $originalVisuels = new ArrayCollection();
//          Créer un ArrayCollection des objets d'hébergements courants dans la base de données
        /** @var Hebergement $entity */
        foreach ($entityUnifie->getHebergements() as $entity) {
            // si l'hebergement est celui du CRM
            if ($entity->getSite()->getCrm() == 1) {
                // on parcourt les hebergementVisuel pour les comparer ensuite
                /** @var HebergementVisuel $entityVisuel */
                foreach ($entity->getVisuels() as $entityVisuel) {
                    // on ajoute les visuel dans la collection de sauvegarde
                    $originalHebergementVisuels->add($entityVisuel);
                    $originalVisuels->add($entityVisuel->getVisuel());
                }
            }
        }

        $this->ajouterHebergementsDansForm($entityUnifie);
//        $this->dispacherDonneesCommune($departementUnifie);
        $this->hebergementsSortByAffichage($entityUnifie);
        $deleteForm = $this->createDeleteForm($entityUnifie);

        $editForm = $this->createForm('Mondofute\Bundle\HebergementBundle\Form\HebergementUnifieType',
            $entityUnifie, array('locale' => $request->getLocale()))
            ->add('submit', SubmitType::class, array(
                'label' => 'mettre.a.jour',
                'attr' => array('onclick' => 'copieNonPersonnalisable();remplirChampsVide();')
            ));

        // *** récupération originals fournisseurHebergement ***
        $originalFournisseurHebergements = new ArrayCollection();
        foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement)
        {
            $originalFournisseurHebergements->add($fournisseurHebergement);
        }
        // *** fin récupération originals gestion fournisseurHebergement ***

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($entityUnifie->getHebergements() as $entity) {
                if (false === in_array($entity->getSite()->getId(), $sitesAEnregistrer)) {
                    $entity->setActif(false);
                } else {
                    $entity->setActif(true);
                }
            }

            // ************* gestion des services *************
            /** @var ServiceHebergement $serviceHebergement */
            foreach ($originalServices as $originalService) {
                if (!($entityUnifie->getServices()->contains($originalService)) || empty($entityUnifie->getServices()) || empty($originalService->getService())) {
                    /** @var ServiceHebergement $originalService */
                    /** @var ServiceHebergementTarif $originalTarif */
                    foreach ($originalTarifs as $originalTarif) {
                        if ($originalTarif->getService() == $originalService) {
//                            $originalTarifs->remove($originalTarif);
                            $em->remove($originalTarif);
                            $this->deleteTarifSites($originalTarif);
                        }
                    }
                    $em->remove($originalService);
                    $this->deleteServiceSites($originalService);
                }
            }

            //  *** gestion des tarifs ***
            foreach ($originalTarifs as $originalTarif) {
                $effacer = true;
                foreach ($entityUnifie->getServices() as $serviceHebergement) {
                    if ($serviceHebergement->getTarifs()->contains($originalTarif)) {
                        $effacer = false;
                    }
                }
                if ($effacer == true) {
                    $em->remove($originalTarif);
                    $this->deleteTarifSites($originalTarif);
                }
            }
            // *** fin gestion des tarifs ***
            foreach ($entityUnifie->getServices() as $key => $serviceHebergement) {
                if (empty($request->request->get('hebergement_unifie')['services'][$key]['checkbox'])) {
                    $entityUnifie->removeService($serviceHebergement);
                    $em->remove($serviceHebergement);
                    $this->deleteServiceSites($serviceHebergement);
                } else {
                    $serviceHebergement->setHebergementUnifie($entityUnifie);
                    /** @var ServiceHebergementTarif $serviceHebergementTarif */
                    foreach ($serviceHebergement->getTarifs() as $serviceHebergementTarif) {
                        $serviceHebergementTarif->setService($serviceHebergement);
                    }
                }
            }
            // ************* fin gestion des services *************

            // ************* suppression visuels *************
            // ** CAS OU L'ON SUPPRIME UN "HEBERGEMENT VISUEL" **
            // on récupère les HebergementVisuel de l'hébergementCrm pour les mettre dans une collection
            // afin de les comparer au originaux.
            /** @var Hebergement $entityCrm */
            $entityCrm = $entityUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 1;
            })->first();
            $entitySites = $entityUnifie->getHebergements()->filter(function (Hebergement $element) {
                return $element->getSite()->getCrm() == 0;
            });
            $newHebergementVisuels = new ArrayCollection();
            foreach ($entityCrm->getVisuels() as $entityVisuel) {
                $newHebergementVisuels->add($entityVisuel);
            }
            /** @var HebergementVisuel $originalHebergementVisuel */
            foreach ($originalHebergementVisuels as $key => $originalHebergementVisuel) {

                if (false === $newHebergementVisuels->contains($originalHebergementVisuel)) {
                    $originalHebergementVisuel->setHebergement(null);
                    $em->remove($originalHebergementVisuel->getVisuel());
                    $this->deleteFile($originalHebergementVisuel->getVisuel());
                    $em->remove($originalHebergementVisuel);
                    // on doit supprimer l'hébergementVisuel des autres sites
                    // on parcourt les hebergement des sites
                    /** @var Hebergement $entitySite */
                    foreach ($entitySites as $entitySite) {
                        $entityVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(
                            array(
                                'hebergement' => $entitySite,
                                'visuel' => $originalHebergementVisuel->getVisuel()
                            ));
                        if (!empty($entityVisuelSite)) {
                            $emSite = $this->getDoctrine()->getEntityManager($entityVisuelSite->getHebergement()->getSite()->getLibelle());
                            $entitySite = $emSite->getRepository(Hebergement::class)->findOneBy(
                                array(
                                    'hebergementUnifie' => $entityVisuelSite->getHebergement()->getHebergementUnifie()
                                ));
                            $entityVisuelSiteSites = new ArrayCollection($emSite->getRepository(HebergementVisuel::class)->findBy(
                                array(
                                    'hebergement' => $entitySite
                                ))
                            );
                            $entityVisuelSiteSite = $entityVisuelSiteSites->filter(function (HebergementVisuel $element)
                            use ($entityVisuelSite) {
//                            return $element->getVisuel()->getProviderReference() == $entityVisuelSite->getVisuel()->getProviderReference();
                                return $element->getVisuel()->getMetadataValue('crm_ref_id') == $entityVisuelSite->getVisuel()->getId();
                            })->first();
                            if (!empty($entityVisuelSiteSite)) {
                                $emSite->remove($entityVisuelSiteSite->getVisuel());
                                $this->deleteFile($entityVisuelSiteSite->getVisuel());
                                $entityVisuelSiteSite->setHebergement(null);
                                $emSite->remove($entityVisuelSiteSite);
                                $emSite->flush();
                            }
                            $entityVisuelSite->setHebergement(null);
                            $em->remove($entityVisuelSite->getVisuel());
                            $this->deleteFile($entityVisuelSite->getVisuel());
                            $em->remove($entityVisuelSite);
                        }
                    }
                }
            }
            // ************* fin suppression visuels *************

            // ************* gestion des emplacements *************
            /** @var Hebergement $entity */
            foreach ($entityUnifie->getHebergements() as $keyHebergement => $entity) {
                foreach ($entity->getEmplacements() as $keyEmplacement => $emplacement) {
                    if (empty($request->request->get('hebergement_unifie')['hebergements'][$keyHebergement]['emplacements'][$keyEmplacement]['checkbox'])) {
                        $entity->removeEmplacement($emplacement);
                        $em->remove($emplacement);
                    } else {
                        if (!empty($emplacement->getDistance2())) {
                            if (empty($emplacement->getDistance2()->getUnite())) {
                                $em->remove($emplacement->getDistance2());
                                $emplacement->setDistance2(null);
                            }
                        }
                    }
                }
            }
            // ************* fin gestion des emplacements *************

            // *** gestion suppression fournisseurs hebergement ***
            foreach ($originalFournisseurHebergements as $originalFournisseurHebergement)
            {
                if(false === $entityUnifie->getFournisseurs()->contains($originalFournisseurHebergement))
                {
                    // *** suppression des code promo logement ***
                    foreach ($entityUnifie->getHebergements() as $hebergement)
                    {
                        $codePromoHebergements = $em->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement->getId() , 'fournisseur' => $fournisseurHebergement->getFournisseur()->getId()));
                        foreach ($codePromoHebergements as $codePromoHebergement){
                            $em->remove($codePromoHebergement);
                        }
                    }
                    // *** fin suppression des code promo logement ***
                    $em->remove($originalFournisseurHebergement);
                }
            }
//            /** @var FournisseurHebergement $fournisseurHebergement */
//            foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement) {
//                if (empty($fournisseurHebergement->getFournisseur())) {
//                    //  supprime le fournisseurHebergement car plus présent
//                    $entityUnifie->removeFournisseur($fournisseurHebergement);
//                    $em->remove($fournisseurHebergement);
//                } else {
//                    $fournisseurHebergement->setHebergement($entityUnifie);
//                }
//            }
            // *** fin gestion suppression des fournisseurs hebergement ***

            // ***** Gestion des Medias *****
            // CAS D'UN NOUVEAU 'HEBERGEMENT VISUEL' OU DE MODIFICATION D'UN "HEBERGEMENT VISUEL"
            /** @var HebergementVisuel $entityVisuel */
            // tableau pour la suppression des anciens visuels
            $visuelToRemoveCollection = new ArrayCollection();
            $keyCrm = $entityUnifie->getHebergements()->indexOf($entityCrm);
            // on parcourt les hebergementVisuels de l'hebergement crm
            foreach ($entityCrm->getVisuels() as $key => $entityVisuel) {
                // on active le nouveau hebergementVisuel (CRM) => il doit être toujours actif
                $entityVisuel->setActif(true);
                // parcourir tout les sites
                /** @var Site $site */
                foreach ($sites as $site) {
                    // sauf  le crm (puisqu'on l'a déjà renseigné)
                    // dans le but de créer un hebegrementVisuel pour chacun
                    if ($site->getCrm() == 0) {
                        // on récupère l'hébegergement du site
                        /** @var Hebergement $entitySite */
                        $entitySite = $entityUnifie->getHebergements()->filter(function (Hebergement $element) use (
                            $site
                        ) {
                            return $element->getSite() == $site;
                        })->first();
                        // si hébergement existe
                        if (!empty($entitySite)) {
                            // on réinitialise la variable
                            unset($entityVisuelSite);
                            // s'il ne s'agit pas d'un nouveau hebergementVisuel
                            if (!empty($entityVisuel->getId())) {
                                // on récupère l'hebergementVisuel pour le modifier
                                $entityVisuelSite = $em->getRepository(HebergementVisuel::class)->findOneBy(array(
                                    'hebergement' => $entitySite,
                                    'visuel' => $originalVisuels->get($key)
                                ));
                            }
                            // si l'hebergementVisuel est un nouveau ou qu'il n'éxiste pas sur le base crm pour le site correspondant
                            if (empty($entityVisuel->getId()) || empty($entityVisuelSite)) {
                                // on récupère la classe correspondant au visuel (photo ou video)
                                $typeVisuel = (new ReflectionClass($entityVisuel))->getName();
                                // on créé un nouveau HebergementVisuel on fonction du type
                                /** @var HebergementVisuel $entityVisuelSite */
                                $entityVisuelSite = new $typeVisuel();
                                $entityVisuelSite->setHebergement($entitySite);
                            }
                            // si l'hébergemenent visuel existe déjà pour le site
                            if (!empty($entityVisuelSite)) {
                                if ($entityVisuelSite->getVisuel() != $entityVisuel->getVisuel()) {
//                                    // si l'hébergementVisuelSite avait déjà un visuel
//                                    if (!empty($entityVisuelSite->getVisuel()) && !$visuelToRemoveCollection->contains($entityVisuelSite->getVisuel()))
//                                    {
//                                        // on met l'ancien visuel dans un tableau afin de le supprimer plus tard
//                                        $visuelToRemoveCollection->add($entityVisuelSite->getVisuel());
//                                    }
                                    // on met le nouveau visuel
                                    $entityVisuelSite->setVisuel($entityVisuel->getVisuel());
                                }
                                $entitySite->addVisuel($entityVisuelSite);

                                /** @var HebergementVisuelTraduction $traduction */
                                foreach ($entityVisuel->getTraductions() as $traduction) {
                                    /** @var HebergementVisuelTraduction $traductionSite */
                                    $traductionSites = $entityVisuelSite->getTraductions();
                                    $traductionSite = null;
                                    if (!$traductionSites->isEmpty()) {
                                        $traductionSite = $traductionSites->filter(function (
                                            HebergementVisuelTraduction $element
                                        ) use ($traduction) {
                                            return $element->getLangue() == $traduction->getLangue();
                                        })->first();
                                    }
                                    if (empty($traductionSite)) {
                                        $traductionSite = new HebergementVisuelTraduction();
                                        $traductionSite->setLangue($traduction->getLangue());
                                        $entityVisuelSite->addTraduction($traductionSite);
                                    }
                                    $traductionSite->setLibelle($traduction->getLibelle());
                                }
                                // on vérifie si l'hébergementVisuel doit être actif sur le site ou non
                                if (!empty($request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites']) &&
                                    in_array($site->getId(),
                                        $request->get('hebergement_unifie')['hebergements'][$keyCrm]['visuels'][$key]['sites'])
                                ) {
                                    $entityVisuelSite->setActif(true);
                                } else {
                                    $entityVisuelSite->setActif(false);
                                }
                            }
                        }
                    }
                    // on est dans l'hebergementVisuel CRM
                    // s'il s'agit d'un nouveau média
                    elseif (empty($entityVisuel->getVisuel()->getId()) && !empty($originalVisuels->get($key))) {
                        // on stocke  l'ancien media pour le supprimer après le persist final
                        $visuelToRemoveCollection->add($originalVisuels->get($key));
                    }
                }
            }
            // ***** Fin Gestion des Medias *****

            $this->gestionCodePromoHebergement($entityUnifie);

            $em->persist($entityUnifie);

            try {
                $error = false;
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                $error = true;
            }
            if (!$error) {
                $this->copieVersSites($entityUnifie, $originalHebergementVisuels);

                // on parcourt les médias à supprimer
                if (!empty($visuelToRemoveCollection)) {
                    foreach ($visuelToRemoveCollection as $item) {
                        if (!empty($item)) {
                            $this->deleteFile($item);
                            $em->remove($item);
                        }
                    }
                    $em->flush();
                }

                $this->addFlash('success', 'L\'hébergement a bien été modifié');
                return $this->redirectToRoute('hebergement_hebergement_edit', array('id' => $entityUnifie->getId()));
            }
        }
//        $this->chargerCatalogue($entityUnifie);
//        dump($entityUnifie);
//        die;
        return $this->render('@MondofuteHebergement/hebergementunifie/edit.html.twig', array(
            'entity' => $entityUnifie,
            'sites' => $sites,
            'langues' => $langues,
            'sitesAEnregistrer' => $sitesAEnregistrer,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    private function deleteTarifSites(ServiceHebergementTarif $tarif)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            $tarifSite = $emSite->find(ServiceHebergementTarif::class,
                $tarif->getId());
            if (!empty($tarifSite)) {
                $tarifSite->setService(null);
                $emSite->remove($tarifSite);
//                $listeServiceSite->setFournisseur(null);
//                $emSite->remove($listeServiceSite);
            }

        }
    }

    private function deleteServiceSites(ServiceHebergement $service)
    {
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->chargerSansCrmParClassementAffichage();
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getManager($site->getLibelle());
            if (!empty($service->getId())) {
                $serviceSite = $emSite->find(ServiceHebergement::class,
                    $service->getId());
                if (!empty($serviceSite)) {
                    $serviceSite->setHebergementUnifie(null);
                    $emSite->remove($serviceSite);
//                $listeServiceSite->setFournisseur(null);
//                $emSite->remove($listeServiceSite);
                }
            }

        }
    }

    /**
     * @param HebergementUnifie $hebergementUnifie
     */
    public function chargerCatalogue($hebergementUnifie)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var FournisseurHebergement $fournisseurHebergement */
        foreach ($hebergementUnifie->getFournisseurs() as $fournisseurHebergement) {
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                /** @var LogementPeriode $logementPeriode */
                foreach ($logement->getPeriodes() as $logementPeriode) {
                    $em->getRepository(LogementPeriode::class)->chargerLocatif($logementPeriode);
                }
            }
        }
    }
    public function chargerFournisseursStockslogementLocatif($fournisseurHebergements){
        $em = $this->getDoctrine()->getManager();
        foreach ($fournisseurHebergements as $fournisseurHebergement) {
            /** @var Logement $logement */
            foreach ($fournisseurHebergement->getLogements() as $logement) {
                /** @var LogementPeriode $logementPeriode */
                foreach ($logement->getPeriodes() as $logementPeriode) {
                    $em->getRepository(LogementPeriode::class)->chargerLocatif($logementPeriode);
                }
            }
        }
    }
    public function creerTableauxStocksHebergementPeriodeAction(Request $request, $idPeriode, $idHebergementUnifie){
        ini_set('memory_limit','1G');
        ini_set('max_execution_time',300);
        set_time_limit(300);
//        echo ini_get('max_execution_time');
//        die;
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
        $em = $this->getDoctrine()->getManager();
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
        echo memory_get_usage();
        $typePeriode = $em->getRepository(TypePeriode::class)->findOneBy(array('id'=>$idPeriode));
        echo memory_get_usage();
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
        $fournisseurHebergements = new ArrayCollection();
//        $fournisseurHebergements = $em->getRepository(FournisseurHebergement::class)->findBy(array('hebergement'=>$idHebergementUnifie));
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
        $fournisseurHebergements = $em->getRepository(FournisseurHebergement::class)->chargerPourStocks($idHebergementUnifie);
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
//        $this->chargerFournisseursStockslogementLocatif($fournisseurHebergements);
//        $time = new \DateTime();
//        echo $time->format('H:i:s');
        echo memory_get_usage();
//        die;
//        dump($fournisseurHebergements);
//        die;
//        error_log('mémoire : '.memory_get_usage());

        return $this->render('@MondofuteHebergement/hebergementunifie/hebergement_stocks.html.twig', array(
            'fournisseurHebergements' => $fournisseurHebergements,
            'typePeriode' => $typePeriode
        ));
    }
    /**
     * Deletes a HebergementUnifie entity.
     *
     */
    public function deleteAction(Request $request, HebergementUnifie $entityUnifie)
    {
        /** @var HebergementUnifie $entityUnifieSite */
        try {
            $form = $this->createDeleteForm($entityUnifie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $sitesDistants = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                // Parcourir les sites non CRM
                foreach ($sitesDistants as $siteDistant) {
                    // Récupérer le manager du site.
                    $emSite = $this->getDoctrine()->getManager($siteDistant->getLibelle());
                    // Récupérer l'entité sur le site distant puis la suprrimer.
                    $entityUnifieSite = $emSite->find(HebergementUnifie::class, $entityUnifie->getId());
                    if (!empty($entityUnifieSite)) {
                        if (!empty($entityUnifieSite->getHebergements())) {
                            /** @var Hebergement $entitySite */
                            foreach ($entityUnifieSite->getHebergements() as $entitySite) {
//                                $entitySite->setClassement(null);
                                if (!empty($entitySite->getMoyenComs())) {
                                    foreach ($entitySite->getMoyenComs() as $moyenComSite) {
                                        $entitySite->removeMoyenCom($moyenComSite);
                                        $emSite->remove($moyenComSite);
                                    }
                                }

                                // si il y a des visuels pour l'entité, les supprimer
                                if (!empty($entitySite->getVisuels())) {
                                    /** @var HebergementVisuel $entityVisuelSite */
                                    foreach ($entitySite->getVisuels() as $entityVisuelSite) {
                                        $visuelSite = $entityVisuelSite->getVisuel();
                                        $entityVisuelSite->setVisuel(null);
                                        if (!empty($visuelSite)) {
                                            $emSite->remove($visuelSite);
                                            $this->deleteFile($visuelSite);
                                        }
                                    }
                                }
                            }
                            $emSite->flush();

                            foreach ($entityUnifieSite->getHebergements() as $hebergement) {
                                $codePromoHebergements = $emSite->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement));
                                foreach ($codePromoHebergements as $codePromoHebergement) {
                                    $emSite->remove($codePromoHebergement);
                                }
                            }

                            /** @var FournisseurHebergement $fournisseurHebergement */
                            foreach ($entityUnifieSite->getFournisseurs() as $fournisseurHebergement){
                                /** @var Logement $logement */
                                foreach ($fournisseurHebergement->getLogements() as $logement)
                                {

                                    $codePromoLogements = $emSite->getRepository(CodePromoLogement::class)->findBy(array('logement' => $logement));
                                    foreach ($codePromoLogements as $codePromoLogement)
                                    {
                                        $emSite->remove($codePromoLogement);
                                    }

                                    /** @var LogementPeriode $logementPeriode */
                                    foreach ($logement->getPeriodes() as $logementPeriode)
                                    {
                                        // *** suprression logement periode locatif  ***
                                        $logementPeriodeLocatif = $emSite->getRepository(LogementPeriodeLocatif::class )->findOneBy(array(
                                            'logement' => $logement,
                                            'periode' => $logementPeriode->getPeriode()->getId(),
                                            ));
                                        if(!empty($logementPeriodeLocatif))
                                        {
                                            $emSite->remove($logementPeriodeLocatif);
                                        }
                                        // *** fin suprression logement periode locatif  ***
                                    }
                                }
                            }
                        }
                        $emSite->remove($entityUnifieSite);
                        $emSite->flush();
                    }
                }
                if (!empty($entityUnifie)) {

                    $prestationAnnexeHebergementUnifies = $em->getRepository(PrestationAnnexeHebergementUnifie::class)->findByHebergement($entityUnifie->getHebergements()->first()->getId());
                    foreach ($prestationAnnexeHebergementUnifies as $prestationAnnexeHebergementUnifie) {
                        $em->remove($prestationAnnexeHebergementUnifie);
                    }

                    if (!empty($entityUnifie->getHebergements())) {
                        /** @var Hebergement $entity */
                        foreach ($entityUnifie->getHebergements() as $entity) {
//                            $entity->setClassement(null);
                            if (!empty($entity->getMoyenComs())) {
                                foreach ($entity->getMoyenComs() as $moyenCom) {
                                    $entity->removeMoyenCom($moyenCom);
                                    $em->remove($moyenCom);
                                }
                            }

                            // si il y a des visuels pour l'entité, les supprimer
                            if (!empty($entity->getVisuels())) {
                                /** @var HebergementVisuel $entityVisuel */
                                foreach ($entity->getVisuels() as $entityVisuel) {
                                    $visuel = $entityVisuel->getVisuel();
                                    $entityVisuel->setVisuel(null);
                                    $em->remove($visuel);
                                    $this->deleteFile($visuel);
                                }
                            }

                        }
                        $em->flush();
                    }

                    /** @var Hebergement $hebergement */
                    foreach ($entityUnifie->getHebergements() as $hebergement) {
                        $codePromoHebergements = $em->getRepository(CodePromoHebergement::class)->findBy(array('hebergement' => $hebergement));
                        foreach ($codePromoHebergements as $codePromoHebergement) {
                            $em->remove($codePromoHebergement);
                        }
                    }
                    /** @var FournisseurHebergement $fournisseurHebergement */
                    foreach ($entityUnifie->getFournisseurs() as $fournisseurHebergement){
                        foreach ($fournisseurHebergement->getLogements() as $logement)
                        {
                            $codePromoLogements = $em->getRepository(CodePromoLogement::class)->findBy(array('logement' => $logement));
                            foreach ($codePromoLogements as $codePromoLogement)
                            {
                                $em->remove($codePromoLogement);
                            }
                            /** @var LogementPeriode $logementPeriode */
                            foreach ($logement->getPeriodes() as $logementPeriode)
                            {
                                // *** suprression logement periode locatif  ***
                                $logementPeriodeLocatif = $em->getRepository(LogementPeriodeLocatif::class )->findOneBy(array(
                                    'logement' => $logement,
                                    'periode' => $logementPeriode->getPeriode()->getId(),
                                ));
                                if(!empty($logementPeriodeLocatif))
                                {
                                    $em->remove($logementPeriodeLocatif);
                                }
                                // *** fin suprression logement periode locatif  ***
                            }
                        }
                    }
                }

                $em->remove($entityUnifie);
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $except) {
            /** @var ForeignKeyConstraintViolationException $except */
            switch ($except->getCode()) {
                case 0:
                    $this->addFlash('error',
                        'Impossible de supprimer l\'hébergement, il est utilisé par une autre entité');
                    break;
                default:
                    $this->addFlash('error', 'une erreur inconnue');
                    break;
            }
            return $this->redirect($request->headers->get('referer'));
        }
        $this->addFlash('success', 'L\'hébergement a bien été supprimé');
        return $this->redirectToRoute('hebergement_hebergement_index');
    }
}

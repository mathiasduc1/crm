<?php

namespace Mondofute\Bundle\UtilisateurBundle\Controller;


use FOS\UserBundle\Model\UserInterface;
use Mondofute\Bundle\CoreBundle\Entity\User;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\UtilisateurBundle\Entity\UtilisateurUser;
use Nucleus\MoyenComBundle\Entity\Email;
use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;

/**
 * Utilisateur controller.
 *
 */
class UtilisateurController extends Controller
{
    /**
     * Lists all Utilisateur entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $utilisateurs = $em->getRepository('MondofuteUtilisateurBundle:UtilisateurUser')->findAll();

        return $this->render('@MondofuteUtilisateur/utilisateur/index.html.twig', array(
            'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * Creates a new Utilisateur entity.
     *
     */
    public function newAction(Request $request)
    {
//        $this->container->parameters['session.storage.options']['domain'];
//        dump($this->container->getParameter("fos_user.model_manager_name"));
//        dump($this->container->getParameter("fos_user.model_manager_name"));
//        $this->container->setParameter("fos_user.model_manager_name" ,  "skifute");
//        die;

        /** @var Site $site */
        $utilisateurUser = new UtilisateurUser();

        $utilisateur = new Utilisateur();

        $utilisateurUser->setUtilisateur($utilisateur);
        $this->addMoyenComs($utilisateur);
        $form = $this->createForm('Mondofute\Bundle\UtilisateurBundle\Form\UtilisateurUserType', $utilisateurUser);
        $form->add('submit', SubmitType::class, array('label' => 'Enregistrer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateurUser->setEnabled(true);
            $utilisateurUser->setRoles(array(UserInterface::ROLE_SUPER_ADMIN));

            $em = $this->getDoctrine()->getEntityManager();
            
            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
//                $moyenCom->setDateCreation();
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $utilisateurUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }
//            $utilisateur->setDateCreation();

            if (!$this->loginExist($utilisateurUser)) {
                $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
                foreach ($sites as $site) {
                    $utilisateurUserClone = clone $utilisateurUser;
                    $utilisateurClone = clone $utilisateur;
                    $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());

                    $this->dupliquerMoyenComs($utilisateur);

                    $utilisateurUserClone->setUtilisateur($utilisateurClone);

                    $emSite->persist($utilisateurClone);
                    $emSite->persist($utilisateurUserClone);

                    $emSite->flush();
                }

                $em->persist($utilisateur);
                $em->persist($utilisateurUser);

                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'L\'utilisateur ' . $utilisateurUser->getUsername() . ' a bien été créé.'
                );

                return $this->redirectToRoute('utilisateur_edit', array('id' => $utilisateurUser->getId()));
            }

        }


//        return $this->container
//            ->get('pugx_multi_user.registration_manager')
//            ->register('Mondofute\Bundle\UtilisateurBundle\Entity\UtilisateurUser')
//            ;

        return $this->render('@MondofuteUtilisateur/utilisateur/new.html.twig', array(
            'utilisateur' => $utilisateurUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Utilisateur $utilisateur
     */
    private function addMoyenComs(Utilisateur $utilisateur)
    {
        $utilisateur
            ->addMoyenCom(new Email());
    }

    private function loginExist(UtilisateurUser $utilisateurUser)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $utilisateurUserByUsername = $em->getRepository(UtilisateurUser::class)->findOneBy(array('username' => $utilisateurUser->getUsername()));
        $utilisateurUserByMail = $em->getRepository(UtilisateurUser::class)->findOneBy(array('email' => $utilisateurUser->getEmail()));
        if ((!empty($utilisateurUserByUsername) && $utilisateurUser != $utilisateurUserByUsername)
            ||
            (!empty($utilisateurUserByMail) && $utilisateurUser != $utilisateurUserByMail)
        ) {
            $this->addFlash(
                'error',
                'Le Login/Email ' . $utilisateurUser->getUsername() . ' éxiste déjà.'
            );
//die;
            return true;
        }
        return false;
    }

    private function dupliquerMoyenComs(Utilisateur $utilisateur)
    {
        foreach ($utilisateur->getMoyenComs() as $moyenCom) {
            $typeComm = (new ReflectionClass($moyenCom))->getShortName();
            switch ($typeComm) {
                case 'Email':
                    /** @var Email $moyenCom */
                    $newMoyenCom = new Email();
                    $newMoyenCom
                        ->setAdresse($moyenCom->getAdresse());
                    break;
                default:
                    break;
            }
            if (!empty($newMoyenCom)) {
//                $newMoyenCom->setDateCreation($moyenCom->getDateCreation());
//                $newMoyenCom->setDateCreation();
                $utilisateur->removeMoyenCom($moyenCom);
                $utilisateur->addMoyenCom($newMoyenCom);
            }
        }
    }

    /**
     * Finds and displays a Utilisateur entity.
     *
     */
    public function showAction(UtilisateurUser $utilisateurUser)
    {
        $deleteForm = $this->createDeleteForm($utilisateurUser);

        return $this->render('@MondofuteUtilisateur/utilisateur/show.html.twig', array(
            'utilisateur' => $utilisateurUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Utilisateur entity.
     *
     * @param Utilisateur $utilisateur The Utilisateur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UtilisateurUser $utilisateurUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_delete', array('id' => $utilisateurUser->getId())))
            ->add('Supprimer', SubmitType::class)
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Utilisateur entity.
     *
     */
    public function editAction(Request $request, UtilisateurUser $utilisateurUser)
    {
        $utilisateur = $utilisateurUser->getUtilisateur();
        $deleteForm = $this->createDeleteForm($utilisateurUser);
        $editForm = $this->createForm('Mondofute\Bundle\UtilisateurBundle\Form\UtilisateurUserType', $utilisateurUser)
            ->add('submit', SubmitType::class, array('label' => 'Mettre à jour'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

//            $dateModification = new DateTime();

//            $utilisateur->setDateModification($dateModification);
            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
//                $moyenCom->setDateModification($dateModification);
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                if ($typeComm == 'Email' && empty($login)) {
                    $login = $moyenCom->getAdresse();
                    $utilisateurUser
                        ->setUsername($login)
                        ->setEmail($login);
                }
            }

            if (!$this->loginExist($utilisateurUser)) {

                $em = $this->getDoctrine()->getManager();

                $this->majSites($utilisateurUser);

                $em->persist($utilisateurUser);
                $em->persist($utilisateur);
                $em->flush();

                // add flash messages
                $this->addFlash(
                    'success',
                    'Le utilisateur ' . $utilisateurUser->getUsername() . ' a bien été modifié.'
                );

                return $this->redirectToRoute('utilisateur_edit', array('id' => $utilisateurUser->getId()));
            }
        }

        return $this->render('@MondofuteUtilisateur/utilisateur/edit.html.twig', array(
            'utilisateur' => $utilisateurUser,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function majSites(UtilisateurUser $utilisateurUser)
    {
        $utilisateur = $utilisateurUser->getUtilisateur();
        /** @var Site $site */
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
        foreach ($sites as $site) {
            $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
            $utilisateurUserSite = $emSite->find(UtilisateurUser::class, $utilisateurUser);
            $utilisateurSite = $utilisateurUserSite->getUtilisateur();
            $utilisateurSite
//                ->setPassword($utilisateur->getPassword())
//                ->setLogin($utilisateur->getPassword())
                ->setNom($utilisateur->getNom())
                ->setPrenom($utilisateur->getPrenom());
//            $utilisateurSite->setDateModification($utilisateur->getDateModification());
            $utilisateurUserSite->setPassword($utilisateurUser->getPassword());
            $utilisateurUserSite->setEnabled($utilisateurUser->isEnabled());

            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $typeComm = (new ReflectionClass($moyenCom))->getShortName();

                $moyenComSite = $utilisateurSite->getMoyenComs()->filter(function ($element) use ($typeComm) {
                    $typeCommSite = (new ReflectionClass($element))->getShortName();
                    return $typeCommSite == $typeComm;
                });
                switch ($typeComm) {
                    case 'Email':
                        /** @var Email $email */
                        foreach ($moyenComSite as $key => $email) {
                            $moyenComSite->get($key)
                                ->setAdresse($moyenCom->getAdresse())//                                ->setDateModification($moyenCom->getDateModification())
                            ;

                            $utilisateurUserSite
                                ->setUsername($moyenCom->getAdresse())
                                ->setEmail($moyenCom->getAdresse());
                        }

//                        $typeComm = (new ReflectionClass($moyenCom))->getShortName();
//
//                        if ($typeComm == 'Email' && empty($login)) {
//                            $login = $moyenCom->getAdresse();
//                            $utilisateurUser
//                                ->setUsername($login)
//                                ->setEmail($login);
//                        }
                        break;
                    default:
                        break;
                }
            }

            $emSite->persist($utilisateurSite);
            $emSite->persist($utilisateurUserSite);
            $emSite->flush();
        }
    }

    /**
     * Deletes a Utilisateur entity.
     *
     */
    public function deleteAction(Request $request, UtilisateurUser $utilisateurUser)
    {
        $utilisateur = $utilisateurUser->getUtilisateur();
        $form = $this->createDeleteForm($utilisateurUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sites = $em->getRepository(Site::class)->findBy(array('crm' => 0));
            foreach ($sites as $site) {
                $emSite = $this->getDoctrine()->getEntityManager($site->getLibelle());
//                dump($utilisateurUser->getId());
//                die();
//                $utilisateurUserSite = $emSite->find(User::class, $utilisateurUser);
                $utilisateurUserSite = $emSite->find(UtilisateurUser::class, $utilisateurUser);

                if (!empty($utilisateurUserSite)) {
                    $utilisateurSite = $utilisateurUserSite->getUtilisateur();
                    foreach ($utilisateurSite->getMoyenComs() as $moyenComSite) {
                        $utilisateurSite->removeMoyenCom($moyenComSite);
                        $emSite->remove($moyenComSite);
                    }

                    $emSite->flush();

                    $emSite->remove($utilisateurSite);
                    $emSite->remove($utilisateurUserSite);
                    $emSite->flush();
                }

            }

            foreach ($utilisateur->getMoyenComs() as $moyenCom) {
                $utilisateur->removeMoyenCom($moyenCom);
                $em->remove($moyenCom);
            }

            $em->flush();

            $em->remove($utilisateur);
            $em->remove($utilisateurUser);
            $em->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }
}

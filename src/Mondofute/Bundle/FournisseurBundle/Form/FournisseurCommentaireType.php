<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use HiDev\Bundle\AuteurBundle\Entity\Auteur;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurCommentaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'property' => 'id',
//                'label_attr' => [
//                    'style' => 'display:none',
//                ],
//                'attr' => [
//                    'style' => 'display:none',
//                ],
                'empty_value' => 'aucun',
            ))
            ->add('commentaireParent', EntityType::class, array(
                'class' => FournisseurCommentaire::class,
                'property' => 'id',
//                'label_attr' => [
//                    'style' => 'display:none',
//                ],
//                'attr' => [
//                    'style' => 'display:none',
//                ],
                'empty_value' => 'aucun',
            ))
            ->add('auteur', EntityType::class, array(
                'class' => Auteur::class,
                'property' => 'id',
//                'label_attr' => [
//                    'style' => 'display:none',
//                ],
//                'attr' => [
//                    'style' => 'display:none',
//                ],
                'empty_value' => 'aucun',
            ))
            ->add('contenu');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\FournisseurCommentaire'
        ));
    }

    public function getName()
    {
        return $this->getBlockPrefix(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_fournisseurbundle_fournisseurcommentaire';
    }

}

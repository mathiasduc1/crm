<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement;
use Mondofute\Bundle\HebergementBundle\Repository\TypeHebergementRepository;
use Mondofute\Bundle\MotClefBundle\Entity\MotClef;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Repository\StationRepository;
use Mondofute\Bundle\UniteBundle\Form\ClassementHebergementType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('traductions', CollectionType::class, array(
                'entry_type' => HebergementTraductionType::class,
            ))
            ->add('station', EntityType::class, array(
                'class' => Station::class,
                'placeholder' => '--- Veuillez choisir une station ---',
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (StationRepository $st) use ($locale) {
                    return $st->getTraductionsByLocale($locale);
                },
            ))
            ->add('typeHebergement', EntityType::class, array(
                'class' => TypeHebergement::class,
                'placeholder' => '--- Veuillez choisir un type d\'hébergement ---',
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (TypeHebergementRepository $st) use ($locale) {
                    return $st->getTraductionsByLocale($locale);
                },
            ))
            ->add('moyenComs'
                , 'Infinite\FormBundle\Form\Type\PolyCollectionType',
                array(
                    'types' => array(
                        'nucleus_moyencombundle_adresse'
                    ),
                ))
            ->add('emplacements', CollectionType::class,
                array(
                    'entry_type' => EmplacementHebergementType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ))
            ->add('classement', ClassementHebergementType::class, array('locale' => $locale))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('visuels', 'Infinite\FormBundle\Form\Type\PolyCollectionType', array(
                'types' => array(
                    'Mondofute\Bundle\HebergementBundle\Form\HebergementVideoType',
                    'Mondofute\Bundle\HebergementBundle\Form\HebergementPhotoType',
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ))
            ->add('motClefs', EntityType::class, array(
                'class' => MotClef::class,
                "choice_label" => "libelle",
                'multiple'  => true,
//                'expanded'  => true,
                'attr' => [
//                    'class' => 'js-mot-clef-multiple'
                ],
                'required' => false,
                )
            )
        ;
            ))
            ->add('motClefs', EntityType::class, array(
                'class' => MotClef::class,
                "choice_label" => "libelle",
                'multiple'  => true,
//                'expanded'  => true,
                'attr' => [
//                    'class' => 'js-mot-clef-multiple'
                ],
                'required' => false,
                )
            )
        ;
            ))
            ->add('motClefs', EntityType::class, array(
                'class' => MotClef::class,
                "choice_label" => "libelle",
                'multiple'  => true,
//                'expanded'  => true,
                'attr' => [
//                    'class' => 'js-mot-clef-multiple'
                ],
                'required' => false,
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\Hebergement',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options); // TODO: Change the autogenerated stub
        /** @var FormView $viewChild */
        foreach ($view->children['classement']->children as $child) {
            $child->vars['attr'] = array('data-unique_block_prefix' => $child->vars['unique_block_prefix']);
        }
    }
}

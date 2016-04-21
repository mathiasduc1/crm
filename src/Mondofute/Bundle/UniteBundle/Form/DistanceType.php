<?php

namespace Mondofute\Bundle\UniteBundle\Form;

use Mondofute\Bundle\UniteBundle\Entity\UniteDistance;
use Mondofute\Bundle\UniteBundle\Repository\UniteDistanceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistanceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('valeur', IntegerType::class)
            ->add('unite',
                EntityType::class,
                array(
                    'class' => UniteDistance::class,
                    'placeholder' => '--- Veuillez choisir une unité ---',
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (UniteDistanceRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\UniteBundle\Entity\Distance',
            'locale' => 'fr_FR'
        ));
    }
}
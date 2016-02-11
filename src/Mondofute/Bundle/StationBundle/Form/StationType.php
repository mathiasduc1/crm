<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Repository\ZoneTouristiqueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('zoneTouristique', EntityType::class, array('class' => ZoneTouristique::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir une zone touristique ---",
                'query_builder' => function (ZoneTouristiqueRepository $rr) use ($locale) {
                    return $rr->getTraductionsZoneTouristiquesByLocale($locale);
                },
            ))
            ->add('codePostal', IntegerType::class, array('attr' => array('min' => 0)))
            ->add('jourOuverture')
            ->add('moisOuverture')
            ->add('jourFermeture')
            ->add('moisFermeture')
            ->add('lienMeteo')
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationTraductionType::class,
            ))
//            ->add('site', HiddenType::class, array( 'property_path' => 'site.id' , 'data_class' => Site::class ));//'mapped' => false ,
//            ->add('site', HiddenType::class, array( 'property_path' => 'site.id' ));//'mapped' => false ,
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\Station',
            'locale' => 'fr_FR',
        ));
    }
}
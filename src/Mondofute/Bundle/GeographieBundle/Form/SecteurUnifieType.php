<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecteurUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secteurs', CollectionType::class, array('entry_type' => SecteurType::class));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\SecteurUnifie',
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options); // TODO: Change the autogenerated stub
//        dump($view);die;
        foreach ($view->children['secteurs'] as $secteur) {
//            dump($secteur);
            if ($secteur->vars['value']->getSite()->getCrm() == 1) {
                $secteurCrm = $secteur;
            } else {
                foreach ($secteur->children['images'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {

//                        dump($secteur->vars['value']->getSite()->getId());
                        $siteId = $secteur->vars['value']->getSite()->getId();
//                    $secteurCrm->children['images']->children[$key]
                        $secteurCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
//                        dump($secteurCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr']);
                    }
                }
                foreach ($secteur->children['photos'] as $key => $photo) {
                    if ($photo->vars['value']->getActif() == true) {

                        $siteId = $secteur->vars['value']->getSite()->getId();
//                    $secteurCrm->children['photos']->children[$key]
                        $secteurCrm->children['photos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }

        }
//        die;
    }

}

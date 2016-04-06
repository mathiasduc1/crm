<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Mondofute\Bundle\SiteBundle\MondofuteSiteBundle(),
            new Mondofute\Bundle\AccueilBundle\MondofuteAccueilBundle(),
            new Mondofute\Bundle\LangueBundle\MondofuteLangueBundle(),
            new Mondofute\Bundle\GeographieBundle\MondofuteGeographieBundle(),
            new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
            new Mondofute\Bundle\StationBundle\MondofuteStationBundle(),
            new Mondofute\Bundle\DomaineBundle\MondofuteDomaineBundle(),
            new Mondofute\Bundle\FournisseurBundle\MondofuteFournisseurBundle(),
            new Mondofute\Bundle\UniteBundle\MondofuteUniteBundle(),
            new Mondofute\Bundle\DescriptionForfaitSkiBundle\MondofuteDescriptionForfaitSkiBundle(),
            new Mondofute\Bundle\ChoixBundle\MondofuteChoixBundle(),
            new Mondofute\Bundle\CatalogueBundle\MondofuteCatalogueBundle(),
            new Mondofute\Bundle\HebergementBundle\MondofuteHebergementBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}

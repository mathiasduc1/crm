<?php

namespace Mondofute\Bundle\AccueilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MondofuteAccueilBundle:Default:index.html.twig');
    }
}

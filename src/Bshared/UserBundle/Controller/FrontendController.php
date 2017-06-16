<?php

namespace Bshared\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function indexAction()
    {
        return $this->render('BsharedUserBundle:Frontend:index.html.twig');
    }
}

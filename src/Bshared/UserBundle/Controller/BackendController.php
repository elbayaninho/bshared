<?php

namespace Bshared\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackendController extends Controller
{
    public function indexAction()
    {
        return $this->render('BsharedUserBundle:Backend:index.html.twig');
    }
}

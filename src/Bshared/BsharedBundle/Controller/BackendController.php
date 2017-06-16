<?php

namespace Bshared\BsharedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackendController extends Controller
{
    public function adminAction()
    {
        return $this->render('BsharedBsharedBundle:Backend:admin.html.twig');
    }
    
    /**
     * Ceci est une action sans route
     * @return type
     */
    public function tasksAction()
    {
        return $this->render('BsharedBsharedBundle:Includes:Backend/tasks.html.twig');
    }
}

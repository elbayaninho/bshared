<?php

namespace Bshared\BsharedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function indexAction()
    {
        return $this->render('BsharedBsharedBundle:Frontend:index.html.twig');
    }

    public function documentAction()
    {
        $em = $this->getDoctrine()->getManager();
        $documents = $em->getRepository("BsharedBsharedBundle:Document")->findLatest();

//        die(dump($documents));

        return $this->render('BsharedBsharedBundle:Includes:Frontend/service.html.twig', array(
            'documents' => $documents
        ));
    }
}

<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CatalogController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Catalog:index.html.twig', array(
            // ...
        ));
    }

    public function getAction()
    {
        return $this->render('AppBundle:Catalog:get.html.twig', array(
            // ...
        ));
    }

}

<?php

namespace CrossOverApp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CrossOverAppUserBundle:Default:index.html.twig');
    }
}

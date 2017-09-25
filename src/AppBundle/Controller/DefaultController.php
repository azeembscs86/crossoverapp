<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use CrossOverApp\UserBundle\Entity\User;
use CrossOverApp\UserBundle\Form\Type\RegistrationType;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
         $entity = new User();
        $form = $this->createForm(new RegistrationType(), $entity);
        return $this->render('CrossOverAppUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView()));
    }
}

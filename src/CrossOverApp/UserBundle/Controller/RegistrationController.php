<?php

namespace CrossOverApp\UserBundle\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use CrossOverApp\UserBundle\Entity\User;
use CrossOverApp\UserBundle\Form\Type\RegistrationType;
use CrossOverApp\UserBundle\Form\Type\UserPasswordResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller {

    public function indexAction() {
        return $this->render('CrossOverAppUserBundle:Default:index.html.twig');
    }

    //----------------------User Registration---------------------------------------

    public function registrationAction() {
        $entity = new User();
        $form = $this->createForm(new RegistrationType(), $entity);
        return $this->render('CrossOverAppUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView()));
    }

    //-----------------------Create New User--------------------------------------------------

    public function registrationCreateAction() {
        try {
            $entity = new User();
            $form = $this->createForm(new RegistrationType(), $entity);
            $form->bind($this->getRequest());
            if ($this->isDuplicateEmail(Null, $entity->getEmail())) {
                $form->get('email')->addError(new FormError('This email has already taken.'));
            }
            if ($form->isValid()) {
                $entity->setCreatedAt(new \DateTime('now'));
                $entity->setUpdatedAt(new \DateTime('now'));
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->sendEmail($entity);
                return $this->render('CrossOverAppUserBundle:Registration:registration_success.html.twig', array('email' => $entity->getEmail()));
            } else {

                return $this->render('CrossOverAppUserBundle:Registration:registration.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            $form->addError(new FormError('Some thing went wrong'));
            return $this->render('CrossOverAppUserBundle:Registration:registration.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }
    
     //---------------------------user password reset form------------------------------------
     public function passwordResetFormAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CrossOverAppUserBundle:User')->find($id);
        $form = $this->createForm(new UserPasswordResetType(), $entity);
        return $this->render('CrossOverAppUserBundle:Registration:passwordResetForm.html.twig', array(
                    'form' => $form->createView(), 'entity' => $entity));
    }
    
    //-------------------------update user password--------------------------------------------------------
    public function passwordUpdateAction(Request $request, $id) {
         try {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrossOverAppUserBundle:User')->find($id);
            $form = $this->createForm(new UserPasswordResetType(), $entity);
            if (!$entity) {
                throw $this->createNotFoundException('Authentication expired or link not found.');
            }
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $entity->setUpdatedAt(new \DateTime('now'));
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($data->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                 return $this->redirect($this->generateUrl('login'));
            } else {
                return $this->render('CrossOverAppUserBundle:Registration:passwordResetForm.html.twig', array(
                            'form' => $form->createView()));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            $form->addError(new FormError('Something went wrong.'));
            return $this->render('CrossOverAppUserBundle:Registration:passwordResetForm.html.twig', array(
                        'form' => $form->createView()));
        }
    }
    
    //----------------------send registration email to user--------------------------------------------
    public function sendEmail($entity)
    {
        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance()
        ->setSubject('Thank you for Registering with us')
        ->setFrom('accounts@crossover.com')
        ->setTo($entity->getEmail())
        ->setBody(
        $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'CrossOverAppUserBundle::Emails/registration.html.twig',
                array('entity' => $entity)
            ),
            'text/html'
        );
    $mailer->send($message);
    return true;
    }
    
    //-------------------------Private  methods to chek whether email already exits or not------------------------------
    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('CrossOverAppUserBundle:User')->isDuplicateEmail($id, $email);
    }

    
}

<?php

namespace CrossOverApp\UserBundle\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use CrossOverApp\UserBundle\Entity\User;
use CrossOverApp\UserBundle\Entity\News;
use CrossOverApp\UserBundle\Form\Type\RegistrationType;
use CrossOverApp\UserBundle\Form\Type\NewsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller {

    
    //------------------------------New Article list--------------------------------------
    public function indexAction($page_number = 1) {       
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrossOverAppUserBundle:User')->find($id);        
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        $limit = 10;
        $newsObj = $this->getDoctrine()->getRepository('CrossOverAppUserBundle:News');
        $rec_count = count($newsObj->countAllRecord($user->getId()));
        $cur_page = $page_number;
        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }        
        $entity = $this->getDoctrine()
                ->getRepository('CrossOverAppUserBundle:News')
                ->findAllNews($user->getId(),$page_number, $limit);
        return $this->render('CrossOverAppUserBundle:News:index.html.twig', array(
                    'news' => $entity
                ));
    }
    
    //-------------------add new news-----------------------------------------
    public function newAction()
    {
        $entity = new News();
        $form = $this->createForm(new NewsType(), $entity);
        return $this->render('CrossOverAppUserBundle:News:new.html.twig', array(
                    'form' => $form->createView()));
       
    }
    
    //--------------------------create new article------------------------------
    public function createNewAction(Request $request)
    {
         try {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrossOverAppUserBundle:User')->find($id);        
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        $entity = new News();
        $form = $this->createForm(new NewsType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {  
            $entity->setUser($user);
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload(); //----- file upload method 
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('news_site_index'));
        }else
        {
            return $this->render('CrossOverAppUserBundle:News:new.html.twig', array(
                    'form' => $form->createView()));
        }
      }catch (\Doctrine\DBAL\DBALException $e) {
            $form->addError(new FormError('Some thing went wrong'));
            return $this->render('CrossOverAppUserBundle:News:new.html.twig', array(
                    'form' => $form->createView()));
        }
        
    }

    //---------------------------new detail page-------------------------------------
    public function showAction($id)
    {
        $entity = $this->getDoctrine()
                ->getRepository('CrossOverAppUserBundle:News')
                ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find News.');
        }

        return $this->render('CrossOverAppUserBundle:News:show.html.twig', array(
                    'news' => $entity
                ));
    }
    
    //------------------delete news article------------------------------------------
    public function deleteAction($id)
    {
            try{
                $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CrossOverAppUserBundle:News')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find News.');
        }
      
            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash(
            'Success',
            'News Article Has been deleted!'
        );
       return $this->redirect($this->generateUrl('news_site_index'));
        }catch (\Doctrine\DBAL\DBALException $e)
        {
             $this->get('session')->setFlash(
            'warning',
            'This News cannot be deleted!'
        );
             return $this->redirect($this->getRequest()->headers->get('referer'));
             
        }
        
    }
    
    //--------------------generate PDF File---------------------------------------
    public function generatePDFDocumentAction()
    {    
      $em = $this->getDoctrine()->getManager();
      $newsObj = $em->getRepository('CrossOverAppUserBundle:News')->findlatestNewArticle($page_number=1, $limit=10);  
      $html = $this->renderView('CrossOverAppUserBundle:News:psf.html.twig',array('news' => $newsObj));
      return   $this->returnPDFResponseFromHTML($html);
    }
    
    
    //------------------------latest 10 RSS FEED----------------------------------
    public function generateRssFeedsAction()
    {
     $em = $this->getDoctrine()->getManager();
     $newsObj = $em->getRepository('CrossOverAppUserBundle:News')->findlatestNewArticle($page_number=1, $limit=10);
     $response = new Response($this->renderView('CrossOverAppUserBundle:News:rss_feed.xml.twig',array('news'=>$newsObj)));
     $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
     return $response;
    }
    
    //-------------------------------generet pdf Document-------------------------------------------
    public function returnPDFResponseFromHTML($html){
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Cross Over News Articles');
        $pdf->SetTitle(('Cross Over News Articles'));
        $pdf->SetSubject('Cross Over News Articles Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        $filename = 'ourcodeworld_pdf_demo';        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }
}

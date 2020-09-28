<?php

namespace TransactionApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /*
     * @Route("/")
     */
//    public function indexAction()
//    {
//        return $this->render('TransactionApiBundle:Default:index.html.twig');
//    }

    /**
     * @Route("/pay")
     */
    public function indexPayAction()
    {
        return $this->render('TransactionApiBundle:Transaction:transaction.html.twig');
    }


}

<?php

namespace AppBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('TransactionApiBundle:Default:index.html.twig');
        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
    }

    /**
     * @Route("/testing/zedeka")
     */

    public function testingZed(){
        $URL="https://api.smszedekaa.com/api/v2/SendSMS?ApiKey=ISnqx7tbigE7OQxnGnsBuY4xrZC3m2Uj7wRpbOuIjtk=&ClientId=54911dcd-e69c-4030-9328-4b848c64c4db&SenderId=KYA&Message=HELLO&MobileNumbers=22893643212";
        $guzzleClient = new Client();
        $response = $guzzleClient->request('GET', $URL);


        return  new JsonResponse(0);
    }
}

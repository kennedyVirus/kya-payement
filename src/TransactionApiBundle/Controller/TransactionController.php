<?php

namespace TransactionApiBundle\Controller;

use AppBundle\Controller\BaseController;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SysSecurityBundle\Entity\LicenceKey;
use SysSecurityBundle\Entity\Verification;
use Paydunya\Paydunya;
use Paydunya_Setup;
use Paydunya\Setup;
use Paydunya_Checkout_Store;
use Paydunya_Checkout_Invoice;

//require('vendor/paydunya-php-master/paydunya.php');

//require ('../paydunya-php-master/paydunya.php');


class TransactionController extends BaseController
{
    /**
     * @Route("/test/zedeka")
     */

    public function testzedeka(Request  $request){
        $res=$this->sendZedekaMessage('22893643212','just a test from kya');
        return new JsonResponse(0);
    }

    /*
    * @Kya sol design payment  init
    * @Payement via Flooz or T-money
    */

    /**
     * @Route("/8004064b17546e4380ce83d1be75b50dkfj2015/api/kya/paygate/payment/init")
     */

    public function initPaygatePaymentAction(Request $request) {
       $json_data = $request->getContent();
        $data = json_decode($json_data,true);
        $url='';


        $amount=0;

        if(
            isset($data["type"]) && $data["type"]!=null &&
            isset($data["amount_category"]) && $data["amount_category"]!=null
        ){
            $amount=$this->getAmountToPay($data["type"],$data["amount_category"]);
        }

        $paygate_token=BaseController::PAYGATE_AUTH_TOKEN;
        $paygate_transaction_url=BaseController::PAYGATE_TRANSACTION_URL;

        $saveTempClient=$this->savePaygateTempClient($data);

        if(!($saveTempClient['status'])){
            //return error

            return new Response($this->serialize($this->errorResponseBlob('client not found')));
        }


        $transaction=$this->initPaygateTransaction($saveTempClient['clientId'],$data['transaction_phone_number'],$amount,$data['type'],$data['amount_category']);

        $description=$transaction->getDetails();
        $identifier=$transaction->getId();

        if($transaction->getPaymentMode()==1){
            //t-money
            $url= "".$paygate_transaction_url.$paygate_token."&amount=".$amount."&description=".urlencode($description)."&identifier=".$identifier;


            return new Response($this->serialize($this->okResponseBlob([
                "url" => $url,
                "type" => 1
            ])));

        }

        if($transaction->getPaymentMode()==2){
            //flooz

            $client=new Client();
            $response = $client->post(BaseController::PAYGATE_INIT_PAY_URL, [
                'json' => [
                    'auth_token' => BaseController::PAYGATE_AUTH_TOKEN,
                    'phone_number' => $data["transaction_phone_number"],
                    'amount' => $amount,
                    'identifier' => $identifier,
                ],
            ]);
            $res = $response->getBody()->getContents();

            $dat = json_decode($res,true);
            if ($dat["status"] != 0) { // if error from paygate set transaction -1 ==>failure
                $trans = $this->TransactionRepo()->find($transaction->getId());
                $trans->setState(-1);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return new Response($this->serialize($this->errorResponseBlob()));
            }
            return new Response($this->serialize($this->okResponseBlob([
                "url" => $url,
                "type" => 2
            ])));
        }

    }

    /**
     * @Route("/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paygate/payment/confirm")
     */

    public function paygateTransactionCallBackAction(Request $request){

        $json_data = $request->getContent();
        $data = json_decode($json_data,true);

        /*
         * tx_reference
         * payment_reference
         * amount
         * datetime
         */

        $payment_reference='';
        if(isset($data["payment_reference"])){
            $payment_reference=$data["payment_reference"];
        }
        $fs = new Filesystem();
        $fs->appendToFile('callback_logs.txt', 'identifier: '. $data["identifier"].' '. 'payment:' .$data["payment_method"].' '.'tx_reference:'.$data["tx_reference"].' '.'payment_reference:'.$payment_reference.' '.'datetime:'.$data['datetime']);

        $transaction = $this->TransactionRepo()->find(intval($data["identifier"]));

        if ($transaction != null) {
            // set transaction to confirmed
            $transaction->setState(1);
            $transaction->setUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //generate key

            $licence_key=$this->generateRandomString(12).$this->generateRandomNumber(4);

            $key=new LicenceKey();
            $key->setName($licence_key);
            $key->setType($transaction->getType());
            $key->setAmountCategory($transaction->getAmountCategory());
            $key->setPrice($transaction->getAmount());
            $delay=$this->getDelay($transaction->getAmountCategory());
            $key->setDelay($delay*86400);
            $key->setUsed(0);
            $key->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
            $key->setUpdatedAt(new \DateTime());

            $em->persist($key);
            $em->flush();

            //save verification

            $verification=new Verification();
            $verification->setPhoneNumber($transaction->getUsername());
            $verification->setState(0);
            $verification->setCode($licence_key);
            $verification->setLicenceKeyId($key->getId());
            $verification->setTransactionCode("".$data["tx_reference"].$this->generateRandomNumber(4));
            $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
            $verification->setUpdatedAt(new \DateTime());

            $em->persist($verification);
            $em->flush();

            //send licence key

            $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " .$licence_key;

            $result=$this->sendZedekaMessage("228".$transaction->getUsername(),$licence_key_to_send);

            $request->getSession()->getFlashBag()->add('transaction_success', 'Transaction éffectuée avec succès');

            return $this->redirectToRoute('homepage');


           // return new Response($this->serialize($this->okResponseBlob('Operation successful')));
        }else  {
            $request->getSession()->getFlashBag()->add('transaction_error', 'Une erreur est survenue lors de la transaction');

            return $this->redirectToRoute('homepage');

            //return new Response($this->serialize($this->errorResponseBlob('Invalid parameters')));
        }
    }

    /**
     * @Route("/8004064b17546e4380ce83d1be75b50dkfj2015/api/kya/paydunya/payment/init")
     */
    public function initPayDunayTransactionAction(Request $request){
        $json_data = $request->getContent();
        $data = json_decode($json_data,true);
        $url='';

        $amount=0;

        if(
            isset($data["type"]) && $data["type"]!=null &&
            isset($data["amount_category"]) && $data["amount_category"]!=null
        ){
            $amount=$this->getAmountToPay($data["type"],$data["amount_category"]);
        }

        $amount=50;

        $saveTempClient=$this->savePaydunyaTempClient($data);

        if(!($saveTempClient['status'])){
            //return error
            return new Response($this->serialize($this->errorResponseBlob('client not found')));
        }

        $transaction=$this->initPayDunyaTransaction($saveTempClient['clientId'],$data["email"],$amount,$data['type'],$data['amount_category']);

        $description=$transaction->getDetails();
        $identifier=$transaction->getId();

        Paydunya_Setup::setMasterKey(BaseController::PAYDUNYA_KEY_MAIN);
        Paydunya_Setup::setPublicKey(BaseController::TEST_PAYDUNYA_KEY_PUBLIC);
        Paydunya_Setup::setPrivateKey(BaseController::TEST_PAYDUNYA_KEY_PRIVATE);
        Paydunya_Setup::setToken(BaseController::TEST_PAYDUNYA_TOKEN);
        Paydunya_Setup::setMode('test');

        //Configuration des informations de votre service/entreprise

        Paydunya_Checkout_Store::setName("KYA-ENERGY GROUP"); // Seul le nom est requis
        Paydunya_Checkout_Store::setTagline("Possédez votre energie");
        Paydunya_Checkout_Store::setPhoneNumber("+228 70 45 34 81 / 99 90 33 46 / 90 17 25 24");
        Paydunya_Checkout_Store::setPostalAddress("08 BP 81101, Lomé - Togo");
        Paydunya_Checkout_Store::setWebsiteUrl("http://www.kya-energy.com");
        Paydunya_Checkout_Store::setLogoUrl("http://www.kya-energy.com/logo.png");
        Paydunya_Checkout_Store::setCallbackUrl("http://www.pay.kya-energy.com/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paydunya/payment/confirm");

        $invoice=new Paydunya_Checkout_Invoice();
        $invoice->addChannel('card');
        $invoice->setDescription($description);
        $invoice->setTotalAmount($amount);
        $invoice->addItem("KYA SOL DESIGN licence key for enterprise", 1, $amount, $amount, "KYA SOL DESIGN licence key for enterprise");
        $invoice->setCancelUrl("http://www.kya-pay.kya-energy.com");
        $invoice->setReturnUrl("http://www.kya-pay.kya-energy.com/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paydunya/payment/return");
        $invoice->setCallbackUrl("http://www.kya-pay.kya-energy.com/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paydunya/payment/confirm");
        $invoice->addCustomData("identifier", $identifier);

        if($invoice->create()){
            $url=$invoice->getInvoiceUrl();
        }

        return new Response($this->serialize($this->okResponseBlob([
            "url" => $url
        ])));
    }

    /**
     * @Route("/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paydunya/payment/confirm")
     */

    public function paydunyaTransactionCallBackAction(Request $request){

        $json_data = $request->getContent();
        $data = json_decode($json_data,true);

//        $licence_key=$this->generateRandomString(12).$this->generateRandomNumber(4);
//
//        $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;
//
//
//        // $result=$this->sendZedekaMessage("228".$transaction->getPhoneNumber(),$licence_key_to_send);
//        //$result=$this->sendZedekaMessage("22893643212",$licence_key_to_send);
//        $result=$this->sendLicenceCodeByEmail("jfkvirus@gmail.com",$licence_key_to_send);

        /*
         * tx_reference
         * payment_reference
         * amount
         * datetime
         */
        //Prenez votre MasterKey, hashez la et comparez le résultat au hash reçu par IPN
        if($data['data']['hash'] === hash('sha512', BaseController::PAYDUNYA_KEY_MAIN)) {

            if ($data['data']['status'] == "completed") {

                $transaction = $this->TransactionRepo()->find(intval($data['data']['custom_data'][0]["identifier"]));

                if ($transaction != null) {
                    // set transaction to confirmed
                    $transaction->setState(1);
                    $transaction->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    //generate key

                    $licence_key=$this->generateRandomString(12).$this->generateRandomNumber(4);

                    $key=new LicenceKey();
                    $key->setName($licence_key);
                    $key->setType($transaction->getType());
                    $key->setAmountCategory($transaction->getAmountCategory());
                    $key->setPrice($transaction->getAmount());
                    $delay=$this->getDelay($transaction->getAmountCategory());
                    $key->setDelay($delay*86400);
                    $key->setUsed(0);
                    $key->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
                    $key->setUpdatedAt(new \DateTime());

                    $em->persist($key);
                    $em->flush();

                    //save verification

                    $ref=substr($data['data']['token'],6);

                    $verification=new Verification();
                    $verification->setEmail($transaction->getUsername());
                   // $verification->setPhoneNumber($transaction->getPhoneNumber());
                    $verification->setState(0);
                    $verification->setLicenceKeyId($key->getId());
                    $verification->setCode($licence_key);
                    $verification->setTransactionCode($ref.$this->generateRandomNumber(4));
                    $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
                    $verification->setUpdatedAt(new \DateTime());

                    $em->persist($verification);
                    $em->flush();

                    //send licence key

                    $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;

                     $res=$this->sendZedekaMessage("22893643212",$licence_key_to_send);

                    $result=$this->sendLicenceCodeByEmail("".$transaction->getEmail(),$licence_key_to_send);

                    return new Response($this->serialize($this->okResponseBlob('Operation successful')));
                }
            }
        } else {
            die("Cette requête n'a pas été émise par PayDunya");
        }
    }

    /**
     * @Route("/8004064b17546e4380ce83d1be75b50dkfj/api/kya/paydunya/payment/return")
     */

    public function paydunyaTransactionReturnUrlAction(Request $request){

        $token=$request->query->get('token');

         $invoice=new \Paydunya_Checkout_Invoice();



          if($invoice->confirm($token)){


        return new RedirectResponse("http://www.kya-pay.kya-energy.com");


//           if($invoice->getStatus()=="completed"){
//        $transaction = $this->TransactionRepo()->find(intval($invoice->getCustomData("identifier")));
//        if ($transaction != null) {
//            // set transaction to confirmed
//            $transaction->setState(1);
//            $transaction->setUpdatedAt(new \DateTime());
//            $em = $this->getDoctrine()->getManager();
//            $em->flush();
//
//            //generate key
//
//            $licence_key = $this->generateRandomString(12) . $this->generateRandomNumber(4);

//            $key = new LicenceKey();
//            $key->setName($licence_key);
//            $key->setType($transaction->getType());
//            $key->setAmountCategory($transaction->getAmountCategory());
//            $key->setPrice($transaction->getAmount());
//            $delay = $this->getDelay($transaction->getAmountCategory());
//            $key->setDelay($delay * 86400);
//            $key->setUsed(0);
//            $key->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//            $key->setUpdatedAt(new \DateTime());
//
//            $em->persist($key);
//            $em->flush();
//
//            //save verification
//
//            $verification = new Verification();
//            $verification->setEmail($transaction->getEmail());
//            $verification->setPhoneNumber($transaction->getPhoneNumber());
//            $verification->setState(0);
//            $verification->setLicenceKeyId($key->getId());
//            $verification->setTransactionCode($token . $this->generateRandomNumber(4));
//            $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//
//            $em->persist($verification);
//            $em->flush();

            //send licence key

//            $licence_key_to_send = "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;
//
//
//            // $result=$this->sendZedekaMessage("228".$transaction->getPhoneNumber(),$licence_key_to_send);
//            $result = $this->sendZedekaMessage("22893643212", $licence_key_to_send);
//
//
//            return new RedirectResponse("http://localhost:8000/kya-sol-design");

            //   }

        }else{
              return new RedirectResponse("http://www.kya-pay.kya-energy.com/pay");

          }

          }
//        try {
//            //Prenez votre MasterKey, hashez la et comparez le résultat au hash reçu par IPN
//            if($data['data']['hash'] === hash('sha512', BaseController::PAYDUNYA_KEY_MAIN)) {
//
//                if ($data['data']['status'] == "completed") {
//
//                    $transaction = $this->TransactionRepo()->find(intval($data['data']['custom_data'][0]["identifier"]));
//
//                    if ($transaction != null) {
//                        // set transaction to confirmed
//                        $transaction->setState(1);
//                        $transaction->setUpdatedAt(new \DateTime());
//                        $em = $this->getDoctrine()->getManager();
//                        $em->flush();
//
//                        //generate key
//
//                        $licence_key=$this->generateRandomString(12).$this->generateRandomNumber(4);
//
//                        $key=new LicenceKey();
//                        $key->setName($licence_key);
//                        $key->setType($transaction->getType());
//                        $key->setAmountCategory($transaction->getAmountCategory());
//                        $key->setPrice($transaction->getAmount());
//                        $delay=$this->getDelay($transaction->getAmountCategory());
//                        $key->setDelay($delay*86400);
//                        $key->setUsed(0);
//                        $key->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//                        $key->setUpdatedAt(new \DateTime());
//
//                        $em->persist($key);
//                        $em->flush();
//
//                        //save verification
//
//                        $verification=new Verification();
//                        $verification->setEmail($transaction->getEmail());
//                        $verification->setPhoneNumber($transaction->getPhoneNumber());
//                        $verification->setState(0);
//                        $verification->setLicenceKeyId($key->getId());
//                        $verification->setTransactionCode($data["tx_reference"].$this->generateRandomNumber(4));
//                        $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//
//                        $em->persist($verification);
//                        $em->flush();
//
//                        //send licence key
//
//                        $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;
//
//
//
//                        $result=$this->sendZedekaMessage("228".$transaction->getPhoneNumber(),$licence_key_to_send);
//
//
//                        return new \Symfony\Component\HttpFoundation\Response($this->serialize($this->okResponseBlob('Operation successful')));
//                    }
//
//                }
//
//            } else {
//                die("Cette requête n'a pas été émise par PayDunya");
//            }
//        } catch(\Exception $e) {
//            die();
//        }


//
//        $payment_reference='';
//        if(isset($data["payment_reference"])){
//            $payment_reference=$data["payment_reference"];
//        }
//        $fs = new Filesystem();
//        $fs->appendToFile('callback_logs.txt', 'identifier: '. $data["identifier"].' '. 'payment:' .$data["payment_method"].' '.'tx_reference:'.$data["tx_reference"].' '.'payment_reference:'.$payment_reference.' '.'datetime:'.$data['datetime']);
//
//        $transaction = $this->TransactionRepo()->find(intval($data["identifier"]));
//
//        if ($transaction != null) {
//            // set transaction to confirmed
//            $transaction->setState(1);
//            $transaction->setUpdatedAt(new \DateTime());
//            $em = $this->getDoctrine()->getManager();
//            $em->flush();
//
//            //generate key
//
//            $licence_key=$this->generateRandomString(12).$this->generateRandomNumber(4);
//
//            $key=new LicenceKey();
//            $key->setName($licence_key);
//            $key->setType($transaction->getType());
//            $key->setAmountCategory($transaction->getAmountCategory());
//            $key->setPrice($transaction->getAmount());
//            $delay=$this->getDelay($transaction->getAmountCategory());
//            $key->setDelay($delay*86400);
//            $key->setUsed(0);
//            $key->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//            $key->setUpdatedAt(new \DateTime());
//
//            $em->persist($key);
//            $em->flush();
//
//            //save verification
//
//            $verification=new Verification();
//            $verification->setEmail($transaction->getEmail());
//            $verification->setPhoneNumber($transaction->getPhoneNumber());
//            $verification->setState(0);
//            $verification->setLicenceKeyId($key->getId());
//            $verification->setTransactionCode($data["tx_reference"].$this->generateRandomNumber(4));
//            $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
//
//            $em->persist($verification);
//            $em->flush();
//
//            //send licence key
//
//            $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;
//
//
//
//            $result=$this->sendZedekaMessage("228".$transaction->getPhoneNumber(),$licence_key_to_send);
//
//
//            return new \Symfony\Component\HttpFoundation\Response($this->serialize($this->okResponseBlob('Operation successful')));
//        }
//        else  {
//            return new \Symfony\Component\HttpFoundation\Response($this->serialize($this->errorResponseBlob('Invalid parameters')));
//        }


}

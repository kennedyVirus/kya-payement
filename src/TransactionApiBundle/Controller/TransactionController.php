<?php

namespace TransactionApiBundle\Controller;

use AppBundle\Controller\BaseController;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SysSecurityBundle\Entity\LicenceKey;
use SysSecurityBundle\Entity\Verification;

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

        $amount=5;
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
            $verification->setPhoneNumber($transaction->getPhoneNumber());
            $verification->setState(0);
            $verification->setLicenceKeyId($key->getId());
            $verification->setTransactionCode($data["tx_reference"].$this->generateRandomNumber(4));
            $verification->setCreatedAt(strtotime(date('Y-m-d H:i:s')));

            $em->persist($verification);
            $em->flush();

            //send licence key

            $licence_key_to_send= "<%23>%20CLE%20ACTIVATION%20KYA%20SOL%20DESIGN%20: " . $licence_key;

            $result=$this->sendZedekaMessage("228".$transaction->getPhoneNumber(),$licence_key_to_send);

            return new Response($this->serialize($this->okResponseBlob('Operation successful')));
        }else  {
            return new Response($this->serialize($this->errorResponseBlob('Invalid parameters')));
        }
    }

}

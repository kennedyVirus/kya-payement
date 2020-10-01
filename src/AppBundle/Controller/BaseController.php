<?php

namespace AppBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use SysSecurityBundle\Entity\Client;
use TransactionApiBundle\Entity\NoPersist\ResponseBlob;
use TransactionApiBundle\Entity\Transaction;

class BaseController extends Controller
{
    const TRANSACTION_OK = 200;
    const TRANSACTION_ERROR = 500;
    const TRANSACTION_CRITICAL = 300;

    const PAYGATE_INIT_PAY_URL = "https://paygateglobal.com/api/v1/pay";

    const PAYGATE_AUTH_TOKEN = "d2db21ea-fbca-485c-b8b3-1c68d79b8d47";

    const PAYGATE_TRANSACTION_URL="https://paygateglobal.com/v1/page?token=";

    const PAYDUNYA_INIT_PAY_URL_TEST = "https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create";

    const PAYDUNYA_INIT_PAY_URL = "https://app.paydunya.com/api/v1/checkout-invoice/create";

    const PAYDUNYA_KEY_MAIN="mjNKMu5j-BYVf-65gV-3VgP-ICXvDQTJrDRM";

    const PAYDUNYA_KEY_PUBLIC="live_public_ERUYpTCR2uTDbdQsLxyggYzkHyC";

    const PAYDUNYA_KEY_PRIVATE="live_private_2rPdO1S4CggwsnPhw5L6QB6p7t6";

    const PAYDUNYA_TOKEN="ue13DeFG2cd2S5EVu4SQ";

    /*test*/
    const TEST_PAYDUNYA_KEY_PUBLIC="test_public_PhuzsPxH3OEXE4DWLDzZ5ZOMhIk";

    const TEST_PAYDUNYA_KEY_PRIVATE="test_private_PVf8js5xf3KK1VdJsk5yHetTFwC";

    const TEST_PAYDUNYA_TOKEN="XxigbD3oXGMUV4pjkKyA";

    /*===end test ======*/

    const SMS_ZEDEKA_HOST="api.smszedekaa.com";

    const SMS_ZEDEKA_API_KEY="ISnqx7tbigE7OQxnGnsBuY4xrZC3m2Uj7wRpbOuIjtk=";

    const SMS_ZEDEKA_CLIENT_ID="54911dcd-e69c-4030-9328-4b848c64c4db";

    const SMS_ZEDEKA_SENDER="KYA";

    const KYA_STUDENT_AMOUNT_DAY=1000;
    const KYA_STUDENT_AMOUNT_WEEK=5000;
    const KYA_STUDENT_AMOUNT_MONTH=15000;
//    const KYA_STUDENT_AMOUNT_TRIMESTER=10000;
//    const KYA_STUDENT_AMOUNT_SEMESTER=18500;
//    const KYA_STUDENT_AMOUNT_ANNUAL=35000;


    const KYA_ACADEMIC_AMOUNT_MONTH=25000;
    const KYA_ACADEMIC_AMOUNT_TRIMESTER=70000;
    const KYA_ACADEMIC_AMOUNT_SEMESTER=130000;
    const KYA_ACADEMIC_AMOUNT_ANNUAL=250000;


    const KYA_ENTERPRISE_AMOUNT_MONTH=40000;
    const KYA_ENTERPRISE_AMOUNT_TRIMESTER=100000;
    const KYA_ENTERPRISE_AMOUNT_SEMESTER=180000;
    const KYA_ENTERPRISE_AMOUNT_ANNUAL=300000;


    protected function ClientRepo () {
        return $this->getDoctrine()->getRepository("SysSecurityBundle:Client");
    }

    protected function VerificationRepo () {
        return $this->getDoctrine()->getRepository("SysSecurityBundle:Verification");
    }

    protected function ClientLoginRepo () {
        return $this->getDoctrine()->getRepository("SysSecurityBundle:ClientLogin");
    }

    protected function TransactionRepo () {
        return $this->getDoctrine()->getRepository("TransactionApiBundle:Transaction");
    }

    protected function LicenceKeyRepo () {
        return $this->getDoctrine()->getRepository("SysSecurityBundle:LicenceKey");
    }

    protected function errorResponseBlob($message = "",$error=-1)
    {
        $response = new ResponseBlob();
        $response->setError($error);
        $response->setMessage($message);
        return $response;
    }

    protected function okResponseBlob($data)
    {
        $response = new ResponseBlob();
        $response->setError(0);
        $response->setMessage("");
        $response->setData($data);
        return $response;
    }


    protected function serialize($data)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize($data, 'json');

    }

    protected function deserialize($data, $obj)
    {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->deserialize($data, $obj, 'json');
    }
    protected function getNowTimeStamp () {
        $date = new \DateTime();
        return $date->getTimestamp();
    }

    function generateRandomString($length){
        $str= ''.$this->getNowTimeStamp();
        $res = sha1($str);
        $total='';
        $value2='';
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if(strlen($res)>6){
            $value1=substr($res,1,$length);

            while (strlen($total)< $length){
                $value2.=$this->generate_string($permitted_chars,$length);
                $result=$value1.$value2;
                $total.=$result;
            }

            if(strlen($total)>$length){
                $total=substr($total,1,$length);
            }
            return $total;
        }
    }
    function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    function generateRandomNumber($length){
        $st=mt_rand(1000,9999);
        return $st;
    }

    /* create log entries for transactions */

    public function logTransactions($data,$status,$response,$client_id,$type,LoggerInterface $logger)
    {
        if($status == self::TRANSACTION_OK) {
            $logger->info('Client: '.$client_id. ' Type:'. $type ,[
                'data' => $data,
                'response' => $response
            ]);
        }

        if($status == self::TRANSACTION_ERROR) {
            $logger->error('Client: '.$client_id. ' Type:'. $type ,[
                'data' => $data,
                'response' => $response
            ]);
        }

        if($status == self::TRANSACTION_CRITICAL) {
            $logger->critical('Client: '.$client_id. ' Type:'. $type ,[
                'data' => $data,
                'response' => $response
            ]);
        }
    }


    /*
     * INIT paygate transaction
     */

    public function initPaygateTransaction($client_id, $phone_number=null, $amount,$type,$amount_category){

        $transaction = new Transaction();


        $details = "Achat Clé d'activation de KYA SOL DESIGN";
        $source=0;

        if (preg_match("#^[9,7]{1}[0-3]{1}[0-9]{6}$#", $phone_number))
        {
            $details = "Achat Clé d'activation de KYA SOL DESIGN à travers T-Money.";
            $source=1;
        } else {
            $details = "Achat Clé d'activation de KYA SOL DESIGN à travers Flooz.";
            $source=2;
        }

        $transaction->setDetails($details);
        $transaction->setClientId($client_id);
        $transaction->setAmount(intval($amount));
        $transaction->setState(0);
        $transaction->setPaymentMode($source);
        $transaction->setProvider('PAYGATE');
        $transaction->setType($type);
        $transaction->setAmountCategory($amount_category);
        $transaction->setUsername($phone_number);
        $transaction->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
        $transaction->setUpdatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        return $transaction;
    }


    public function getAmountToPay($licence_type,$amount_category){
        $amount=0;
        if(intval($licence_type)==1){
            //enterprise
            switch (intval($amount_category)){
                case 3:
                    $amount=BaseController::KYA_ENTERPRISE_AMOUNT_MONTH;
                    break;
                case 4:
                    $amount=BaseController::KYA_ENTERPRISE_AMOUNT_TRIMESTER;
                    break;
                case 5:
                    $amount=BaseController::KYA_ENTERPRISE_AMOUNT_SEMESTER;
                    break;
                case 6:
                    $amount=BaseController::KYA_ENTERPRISE_AMOUNT_ANNUAL;
                    break;
            }
        }

        if(intval($licence_type)==2){
            //academic
            switch (intval($amount_category)){
                case 3:
                    $amount=BaseController::KYA_ACADEMIC_AMOUNT_MONTH;
                    break;
                case 4:
                    $amount=BaseController::KYA_ACADEMIC_AMOUNT_TRIMESTER;
                    break;
                case 5:
                    $amount=BaseController::KYA_ACADEMIC_AMOUNT_SEMESTER;
                    break;
                case 6:
                    $amount=BaseController::KYA_ACADEMIC_AMOUNT_ANNUAL;
                    break;
            }
        }

        if(intval($licence_type)==3){
            //students
            switch (intval($amount_category)){
                case 1:
                    $amount=BaseController::KYA_STUDENT_AMOUNT_DAY;
                    break;
                case 2:
                    $amount=BaseController::KYA_STUDENT_AMOUNT_WEEK;
                    break;
                case 3:
                    $amount=BaseController::KYA_STUDENT_AMOUNT_MONTH;
                    break;
            }
        }

        return $amount;
    }

    public function getDelay($amount_category){
        $delay=0;

        switch (intval($amount_category)){
            case 1:
                $delay=1;
                break;
            case 2:
                $delay=7;
                break;
            case 3:
                $delay=30;
                break;
            case 4:
                $delay=90;
                break;
            case 5:
                $delay=180;
                break;
            case 6:
                $delay=360;
                break;
        }


        return $delay;
    }


    public function savePaygateTempClient($data){
        if(
            isset($data["first_name"]) && $data["first_name"]!=null &&
            isset($data["last_name"]) && $data["last_name"]!=null &&
            isset($data["address"]) && $data["address"]!=null &&
            isset($data["country"]) && $data["country"]!=null &&
            isset($data["city"]) && $data["city"]!=null &&
            isset($data["phone_number"]) && $data["phone_number"]!=null
        ){
            /*save client personal infos as temporary client*/

            $temp_client=new Client();

            $temp_client->setFirstName($data["first_name"]);
            $temp_client->setLastName($data["last_name"]);
            $temp_client->setAddress($data["address"]);
            $temp_client->setCountry($data["country"]);
            $temp_client->setCity($data["city"]);
            $temp_client->setPhoneNumber($data["phone_number"]);

            if(isset($data["email"]) && $data["email"]!=null){
                $temp_client->setEmail($data["email"]);
            }

            if(isset($data["job_title"]) && $data["job_title"]!=null){
                $temp_client->setJobTitle($data["job_title"]);
            }

            if(isset($data["organisation"]) && $data["organisation"]!=null){
                $temp_client->setOrganisation($data["organisation"]);
            }

            $temp_client->setUsername($data["phone_number"]);

            $temp_client->setStatus(0);
            $temp_client->setCategory(0);
            $temp_client->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
            $temp_client->setUpdatedAt(new \DateTime());

            $em=$this->getDoctrine()->getManager();
            $em->persist($temp_client);
            $em->flush();

            $dat=[];
            $dat["status"]=true;
            $dat["clientId"]=$temp_client->getId();

            return $dat;
        }else{
            $dat=[];
            $dat["status"]=false;
            $dat["clientId"]=null;

            return $dat;
        }

    }

    public function savePaydunyaTempClient($data){
        if(
            isset($data["first_name"]) && $data["first_name"]!=null &&
            isset($data["last_name"]) && $data["last_name"]!=null &&
            isset($data["address"]) && $data["address"]!=null &&
            isset($data["country"]) && $data["country"]!=null &&
            isset($data["city"]) && $data["city"]!=null &&
            isset($data["email"]) && $data["email"]!=null
        ){
            /*save client personal infos as temporary client*/

            $temp_client=new Client();

            $temp_client->setFirstName($data["first_name"]);
            $temp_client->setLastName($data["last_name"]);
            $temp_client->setAddress($data["address"]);
            $temp_client->setCountry($data["country"]);
            $temp_client->setCity($data["city"]);
            $temp_client->setEmail($data["email"]);
            $temp_client->setUsername($data["email"]);

            if(isset($data["phone_number"]) && $data["phone_number"]!=null){
                $temp_client->setPhoneNumber($data["phone_number"]);
            }

            if(isset($data["job_title"]) && $data["job_title"]!=null){
                $temp_client->setJobTitle($data["job_title"]);
            }

            if(isset($data["organisation"]) && $data["organisation"]!=null){
                $temp_client->setOrganisation($data["organisation"]);
            }

            $temp_client->setStatus(0);
            $temp_client->setCategory(1);
            $temp_client->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
            $temp_client->setUpdatedAt(new \DateTime());

            $em=$this->getDoctrine()->getManager();
            $em->persist($temp_client);
            $em->flush();

            $dat=[];
            $dat["status"]=true;
            $dat["clientId"]=$temp_client->getId();

            return $dat;
        }
        $dat=[];
        $dat["status"]=false;
        $dat["clientId"]=null;

        return $dat;
    }

    public function initPayDunyaTransaction($client_id,$email,$amount,$type,$amount_category){

        $transaction = new Transaction();
        $details = "Achat Clé d'activation de KYA SOL DESIGN à travers Carte Bancaire";
        $source=3;

        $transaction->setDetails($details);
        $transaction->setClientId($client_id);
        $transaction->setAmount(intval($amount));
        $transaction->setState(0);
        $transaction->setPaymentMode($source);
        $transaction->setProvider('PAYDUNYA');
        $transaction->setType($type);
        $transaction->setUsername($email);
        $transaction->setAmountCategory($amount_category);
        $transaction->setCreatedAt(strtotime(date('Y-m-d H:i:s')));
        $transaction->setUpdatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        return $transaction;
    }


    public function sendZedekaMessage($destination,$body="ok"){
        $host=BaseController::SMS_ZEDEKA_HOST;
        $ApiKey=BaseController::SMS_ZEDEKA_API_KEY;
        $ClientId=BaseController::SMS_ZEDEKA_CLIENT_ID;
        $SenderId=BaseController::SMS_ZEDEKA_SENDER;
        $MobileNumber=$destination;

        $URL="https://".$host."/api/v2/SendSMS?ApiKey=".$ApiKey."&ClientId=".$ClientId."&SenderId=".$SenderId."&Message=".$body."&MobileNumbers=".$MobileNumber."";

        // $URL="https://api.smszedekaa.com/api/v2/SendSMS?ApiKey=ISnqx7tbigE7OQxnGnsBuY4xrZC3m2Uj7wRpbOuIjtk=&ClientId=54911dcd-e69c-4030-9328-4b848c64c4db&SenderId=KYA&Message=HELLO&MobileNumbers=22893643212";

        $guzzleClient = new \GuzzleHttp\Client();
        $response = $guzzleClient->request('GET', $URL);

        return $response;
    }

    public function sendLicenceCodeByEmail($destination,$body="ok"){
        $transport = \Swift_MailTransport::newInstance();

        //$path = __DIR__.'/../../web/assets/asset/img/mail-cover.jpg';

        $mailer = new \Swift_Mailer($transport);
        $now = (new \DateTime('now'))->format('d/m H:i:s');
        // Create a message
        $message = (new \Swift_Message('Votre Licence pour payement du '.$now));
        //$bannerImg = $message->embed(\Swift_Image::fromPath($path));
        $message
            //->setFrom(['noreplykabadelivery@kya-energy.com' => 'KYA-ENERGY-GROUP'])
            ->setFrom(['jfkvirus@gmail.com' => 'KYA-ENERGY-GROUP'])
            ->setTo([
                $destination
            ])
            ->setBody(
                $this->renderView(
                    'TransactionApiBundle:Default:mail.html.twig',
                    array('code' => $body)
                )
            )
            ->setContentType('text/html');

        // Send the message
        $result = $mailer->send($message);

        return 0;
    }
}

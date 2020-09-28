<?php
/**
 * Created by PhpStorm.
 * User: jfkvi
 * Date: 27/09/2020
 * Time: 14:40
 */

namespace TransactionApiBundle\Entity\NoPersist;


class ResponseBlob
{
    private $error;


    private $message;


    private $data;




    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }


    public function getError()
    {
        return $this->error;
    }


    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }


    public function getMessage()
    {
        return $this->message;
    }


    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }


    public function getData()
    {
        return $this->data;
    }
}
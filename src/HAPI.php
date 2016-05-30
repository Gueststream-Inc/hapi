<?php
/**
 * Created by PhpStorm.
 * User: houghtelin
 * Date: 5/27/16
 * Time: 4:32 PM
 */

namespace Gueststream;

class HAPI
{
    private $apiKey;
    private $clientId;

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function hat($cc)
    {
        $apiKey = $this->getApiKey();
        $clientId = $this->getClientId();

        $epoch_milliseconds = time() * 1000;
        $time = number_format($epoch_milliseconds, 0, '.', '');

        $obj = new \stdClass();
        $obj->tokenType = "CC";
        $obj->value = $cc;

        $fields_string = json_encode($obj);

        $digest = hash("sha256", $time . $apiKey);

        $url = "https://sensei.homeaway.com/tokens?time=$time&digest=$digest&clientId=$clientId";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        $results = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($results, true);

        switch ($res['cardType']) {
            case "VISA":
                $thetype = "VI";
                break;
            case "MASTERCARD":
                $thetype = "MC";
                break;

            case "AMEX":
                $thetype = "AX";
                break;
            case "DINERS":
                $thetype = "DN";
                break;

            case "DISCOVER":
                $thetype = "DS";
                break;
        }

        if (isset($res['msg'])) {
            $this->error = $res['msg'];

            return false;
        }

        $obj = new \stdClass();
        $obj->masked = $res['maskedValue'];
        $obj->token = $res['@id'];
        $obj->cardcode = $thetype;

        return $obj;
    }
}

<?php

namespace STI\SemantifyIt;

use Exception;


/**
 * Class SemantifyIt
 */
class SemantifyIt
{

    /**
     * variable for websiteApiKey
     *
     * @param string $websiteApiKey ;
     */
    private $websiteApiKey;

    /**
     * website secret
     */
    private $websiteApiSecret;


    /**
     * variable for Url
     *
     * @param string $websiteKey ;
     */
    private $live_server = "https://semantify.it/api";

    private $staging_server = "https://staging.semantify.it/api";

    private $live = true;

    /**
     *
     * var for displayin errors or not
     *
     * true  => errors are shown
     * false => errors are hidden
     *
     * @var boolean
     */
    private $error = false;


    /**
     * @return int
     */
    public function getLive()
    {
        return $this->live;
    }

    /**
     * @param int $live
     */
    public function setLive($live)
    {
        $this->live = $live;
    }


    /**
     *
     * fet error reporting value
     *
     * @return boolean
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * showing errors
     * true  => errors are shown
     * false => errors are hidden
     *
     * @param boolean $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }


    /**
     * @return mixed
     */
    public function getWebsiteApiSecret()
    {
        if($this->websiteApiSecret==""){
            throw new Exception("Caught problem: no API secret saved!");
        }

        return $this->websiteApiSecret;
    }

    /**
     * @param mixed $websiteApiSecret
     */
    public function setWebsiteApiSecret($websiteApiSecret)
    {
        $this->websiteApiSecret = $websiteApiSecret;
    }

    /**
     * getter for websiteApiKey
     *
     * @return string
     */
    public function getWebsiteApiKey()
    {
        //return ""
        if (($this->error) && (($this->websiteApiKey=="") || ($this->websiteApiKey=="0"))){
            throw new Exception("Caught problem: no API key saved!");
            echo "Caught problem: no API key saved!<br>";
        }
        return $this->websiteApiKey;
    }

    /**
     * setter for websiteApiKey
     *
     * @param string $websiteApiKey
     */
    public function setWebsiteApiKey($websiteApiKey)
    {
        $this->websiteApiKey = $websiteApiKey;
    }


    /**
     * SemantifyIt constructor.
     *
     * @param string $key
     */
    public function __construct($key = "", $secret = "")
    {
        if ($key != "") {
            $this->setWebsiteApiKey($key);
        }

        if ($secret != "") {
            $this->setWebsiteApiSecret($secret);
        }

        if(!function_exists('curl_version')) {
                die("No curl library installed! API will not work.");
        }

    }


    /**
     *
     * transport layer for api
     *
     * @param       $type
     * @param       path
     * @param array $params
     * @return string
     */
    private function transport($type, $path, $params = array(), $settings = array())
    {

        $headers = array();

        /** url with server and path */
        $url = $this->live_server . '/' . $path;
        //if it is in staging server than switch to staging api
        if ($this->live == false) {
            $url = $this->staging_server . '/' . $path;
        }

        /** add support for headers */
        if(!empty($settings["headers"])){
            $headers = $settings["headers"];
        }


        switch ($type) {

            case "GET":
                try {
                    $fullurl = $url . (count($params) == 0 ? '' : '?' . http_build_query($params));

                    return $this->get($fullurl, $headers);
                } catch (Exception $e) {
                    if ($this->error) {
                        echo 'Caught exception: ' . $e->getMessage() . "<br>";
                    }

                    return false;
                }
                break;

            case "POST":
            case "PATCH":
                try {
                    $fullurl = $url;

                    //call the class method
                    return call_user_func_array(array($this, strtolower($type)), array($fullurl, $params, $headers));

                } catch (Exception $e) {
                    if ($this->error) {
                        echo 'Caught exception: ' . $e->getMessage() . "<br>";
                    }

                    return false;
                }

                break;


        }
    }



    /**
     *
     * Function responsible for getting stuff from server - physical layer
     *
     * @param string $url url adress
     * @return string return content
     * @throws Exception
     */
    private function get($url, $headers)
    {

        //if allow url fopen is allowed we will use file_get_contents otherwise curl
        $content = $this->curl("GET", $url, "", $headers);

        if ($content === false) {
            throw new Exception('Error getting content from ' . $url);
        }

        if ($content == "") {
            throw new Exception('No content received from ' . $url);
        }

        return $content;

    }

    private function post($url, $params, $headers)
    {
        $action = "POST";
        $content = $this->curl($action, $url, $params, $headers);

        if ($content === false) {
            throw new Exception('Error posting content to ' . $url);
        }

        if ($content == "") {
            throw new Exception('No content returned from '.$action.' action at url ' . $url);
        }

        return $content;

    }

    private function patch($url, $params, $headers)
    {
        $action = "PATCH";
        $content = $this->curl($action, $url, $params, $headers);

        if ($content === false) {
            throw new Exception('Error patching content to ' . $url);
        }

        if ($content == "") {
            throw new Exception('No content returned from '.$action.' action at url ' . $url);
        }

        if ($content == "Not Found") {
            throw new Exception('Annotation Not found for '.$action.' action at url ' . $url);
        }

        return $content;

    }


    private function curl($type, $url, $params="", $headers)
    {

        //echo $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);

        if($type!="GET"){

            $params_string = $params; //because it is already in json
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);


            $Headers = array_merge(array('Content-Type: application/json', 'Content-Length: ' . strlen($params_string)), $headers);

            //var_dump($Headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$Headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
       // echo($response);

        if (curl_error($ch) && ($this->error)) {
            echo 'CURL error:' . curl_error($ch)."<br>";
        }

        curl_close($ch);
        //var_dump($response);
        return $response;

    }

    private function isContentAvailable($input)
    {
        if (($input == "") || ($input == false) || (strpos($input, 'error') !== false)) {
            return false;
        }

        return true;
    }


    /**
     *
     * function for decoding, it can be easily turned of if necessary
     *
     * @param $json
     * @return mixed
     */
    private function decoding($json)
    {
        return json_decode($json);
    }




    /**
     * returns website annotations based on websiteApiKey
     *
     * @return array
     */
    public function getAnnotationList()
    {

        //$params["key"] = $this->getWebsiteApiKey();
        //$json = $this->transport("GET", "annotation/list/",$params);

        //if(!$this->isContentAvailable($json)) {
        $json = $this->transport("GET", "annotation/list/" . $this->getWebsiteApiKey());

        //}

        return $json;
    }

    /**
     * post a new annotation to the server
     *
     * @return array
     */
    public function postAnnotation($json)
    {


        $settings["headers"] = array('website-secret:'." ".$this->getWebsiteApiSecret());
        $json = $this->transport("POST", "annotation/" . $this->getWebsiteApiKey(), $json, $settings);

        return $json;
    }

    /**
     *
     * update an annotation by uid to website
     *
     * @param $json
     * @param $uid
     * @return string
     */
    public function updateAnnotation($json, $UID)
    {

        $settings["headers"] = array('website-secret:'." ".$this->getWebsiteApiSecret());
        $json = $this->transport("PATCH", "annotation/uid/".$UID, $json, $settings);


        return $json;
    }


    /**
     *
     * Funciton which get annotations by url
     *
     * @param $url
     * @return string
     */
    public function getAnnotationByURL($url)
    {
        return $this->transport("GET", "annotation/url/" . rawurlencode($url));
    }

    /**
     *
     * returns json-ld anotations based on anotations id
     *
     * @param string $id
     * @return json
     */
    public function getAnnotation($id)
    {

        return $this->transport("GET", "annotation/short/" . $id);

    }


}
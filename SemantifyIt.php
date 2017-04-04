<?php
namespace STI\SemantifyIt;

use Exception;


/**
 * Class SemantifyIt
 */
class SemantifyIt {

    /**
     * variable for websiteApiKey
     *  @param string $websiteApiKey;
     */
    private $websiteApiKey;

    /**
     * variable for Url
     *  @param string $websiteKey;
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
     * SemantifyIt constructor.
     * @param string $key
     */
    public function __construct($key = "")
    {
        if($key!=""){
            $this->setWebsiteApiKey($key);
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
    private function get($url){

        $content = @file_get_contents($url);

        if($content===false){
            throw new Exception('Error getting content from '.$url);
        }

        if($content==""){
            throw new Exception('No content received from '.$url);
        }

        return $content;

    }

    private function isContentAvailable($input){
        if(($input == "") || ($input == false) || (strpos($input, 'error') !== false)){
            return false;
        }
        return true;
    }

    /**
     *
     * transport layer for api
     *
     * @param $type
     * @param path
     * @param array $params
     * @return string
     */
    private function transport($type, $path, $params=array()){

        /** url with server and path */
        $url = $this->live_server.'/'.$path;
        //if it is in staging server than switch to staging api
        if($this->live==false){
            $url = $this->staging_server.'/'.$path;
        }

        switch ($type) {

            case "GET":
                try{
                    $fullurl = $url.( count($params)==0 ? '' : '?'. http_build_query($params) );
                    return $this->get($fullurl);
                }
                catch (Exception $e) {
                    if($this->error){
                        echo 'Caught exception: '.$e->getMessage(). "<br>";
                    }

                    return false;
                }

            break;

        }
    }

    /**
     *
     * function for decoding, it can be easily turned of if necessary
     *
     * @param $json
     * @return mixed
     */
    private function decoding($json){
        return json_decode($json);
    }


    /**
     * getter for websiteApiKey
     * @return string
     */
    public function getWebsiteApiKey()
    {
        //return ""
        return $this->websiteApiKey;
    }

    /**
     * setter for websiteApiKey
     * @param string $websiteApiKey
     */
    public function setWebsiteApiKey($websiteApiKey)
    {
        $this->websiteApiKey = $websiteApiKey;
    }

    /**
     * returns website annotations based on websiteApiKey
     *
     * @return array
     */
    public function getAnnotationList(){

        //$params["key"] = $this->getWebsiteApiKey();
        //$json = $this->transport("GET", "annotation/list/",$params);

        //if(!$this->isContentAvailable($json)) {
            $json = $this->transport("GET", "annotation/list/".$this->getWebsiteApiKey());
        //}

        return $json;
    }

    /**
     *
     * Funciton which get annotations by url
     *
     * @param $url
     * @return string
     */
    public function getAnnotationByURL($url){
        return $this->transport("GET", "annotation/url/".rawurlencode($url));
    }

    /**
     *
     * returns json-ld anotations based on anotations id
     *
     * @param string $id
     * @return json
     */
    public function getAnnotation($id){

        return $this->transport("GET", "annotation/short/".$id);

    }




}
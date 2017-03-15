<?php

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
    private $server = "https://staging.semantify.it/api";



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
        $url = $this->server.'/'.$path;

        switch ($type) {

            case "GET":
                try{
                    $fullurl = $url.( count($params)==0 ? '' : '?'. http_build_query($params) );
                    return $this->get($fullurl);
                }
                catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                    return false;
                }

            break;

        }
    }


    /**
     * getter for websiteApiKey
     * @return string
     */
    public function getWebsiteApiKey()
    {
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

        $params["apiKey"] = $this->getWebsiteApiKey();
        $json = $this->transport("GET", "user/list/",$params);

         return json_decode($json);

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
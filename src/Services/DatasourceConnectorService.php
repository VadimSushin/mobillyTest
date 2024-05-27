<?php

namespace App\Services;

class DatasourceConnectorService
{
    private $datasourceUrl;

    public function __construct($datasourceApiURL)
    {
        $this->datasourceUrl = $datasourceApiURL;
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->callApi('');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getDetails($id)
    {
        return $this->callApi('&q='.$id);
    }

    /**
     * @param $request
     * @return mixed
     */
    private function callApi($request)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->datasourceUrl.$request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}
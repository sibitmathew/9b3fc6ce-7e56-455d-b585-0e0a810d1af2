<?php
namespace ReportProcess\Services;

use Exception;

/**
 * This service used to just fetch data from json files and store in variable
 *
 * Class DataService
 * @package ReportProcess\services
 */
class DataService
{
    CONST JSON_PATH = './data/*.json';

    /**
     * To initialise data
     *
     * @return array|null
     */
    public function initData()
    {
        return $this->fetchData();
    }

    /**
     * To fetch data
     *
     * @return array|null
     */
    private function fetchData()
    {
        $jsonFiles = glob(self::JSON_PATH);

        try{
            $allData = [];
            foreach ($jsonFiles as $jsonFileName => $jsonFile) {

                // Read the contents of the JSON file into a string
                $jsonString = file_get_contents($jsonFile);

                //Decode json data
                $data = json_decode($jsonString, true);

                //Get filename
                $baseame = basename($jsonFile);

                //Remove ext
                $filename = substr($baseame, 0, strrpos($baseame, "."));

                // Check if decoding was successful
                if ($data !== null) {
                    $allData[$filename] = $data;
                } else {
                    throw new Exception("Error reading json: ".$jsonFile);
                }
            }
            return $allData;
        } catch (Exception $e){
            echo $e->getMessage()."\n";
        }
    }
}
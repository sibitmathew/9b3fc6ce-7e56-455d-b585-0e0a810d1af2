<?php


namespace ReportProcess\Services;

/**
 * Class QuestionsService
 * @package ReportProcess\Services
 */
class QuestionsService
{
    /**
     * @param $data
     * @param $id
     * @return array
     */
    public function getQuestionsById($data, $id)
    {
        if(!isset($data["questions"])){
            echo "Questions data not found!\n";
            die;
        }


        return array_filter($data["questions"], function($value) use ($id){
            return isset($value["id"]) && $value["id"] === $id;
        });
    }

    public function getOptionsById($data, $id)
    {
        if(!isset($data['config']['options'])){
            echo "Options data not found!\n";
            die;
        }


        return array_filter($data['config']['options'], function($value) use ($id){
            return isset($value["id"]) && $value["id"] === $id;
        });
    }
}
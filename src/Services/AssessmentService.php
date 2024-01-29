<?php


namespace ReportProcess\Services;


class AssessmentService
{
    public function getAssessmentById($data, $id)
    {
        if(!isset($data["assessments"])){
            echo "Assessments data not found!\n";
            die;
        }


        return array_filter($data["assessments"], function($value) use ($id){
            return isset($value["id"]) && $value["id"] === $id;
        }, ARRAY_FILTER_USE_BOTH);
    }
}
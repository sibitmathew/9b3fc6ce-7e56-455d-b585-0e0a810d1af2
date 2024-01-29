<?php


namespace ReportProcess\Services;

/**
 * Class StudentService
 * @package ReportProcess\Services
 */
class StudentService
{
    /**
     * @param $data
     * @param $studentId
     * @return array
     */
    public function getStudentData($data, $studentId)
    {
        if(!isset($data["students"])){
            echo "Student data not found!\n";
            die;
        }

        return array_filter($data["students"], function($value) use ($studentId){
            return isset($value["id"]) && $value["id"] === $studentId;
        });
    }
}
<?php


namespace ReportProcess\Services;

use DateTime;

/**
 * Class StudentResponsesService
 * @package ReportProcess\Services
 */
class StudentResponsesService
{
    /**
     * @param $data
     * @param $studentId
     * @return array
     */
    public function getStudentResponseData($data, $studentId)
    {
        if(!isset($data["student-responses"])){
            echo "Student response data not found!\n";
            die;
        }

        return array_filter($data["student-responses"], function($value) use ($studentId){
            return isset($value["student"]["id"]) && $value["student"]["id"] === $studentId;
        });
    }

    /**
     * @param $data
     * @param $studentId
     * @return array
     */
    public function getTotalStudentCompletedAssesmentsById($data, $studentId, $assessmentId)
    {
        if(!isset($data["student-responses"])){
            echo "Student response data not found!\n";
            die;
        }

        return array_filter($data["student-responses"], function($value) use ($studentId, $assessmentId){
            return isset($value["student"]["id"]) && isset($value["assessmentId"]) && isset($value["completed"]) && $value["student"]["id"] === $studentId && $value["assessmentId"] === $assessmentId;
        });
    }


    /**
     * To get getStudentRecentCompletedResponseData
     *
     * @param $responseData
     * @return array|mixed
     */
    public function getStudentRecentCompletedResponseData($responseData)
    {
        // Get today's date
        $today = strtotime(date('d-m-Y H:i:s'));
        $closestDate = null;
        $closestDifference = PHP_INT_MAX;
        $recentData = [];

        foreach ($responseData as $dateInfo) {
            $difference = 0;
            if(!isset($dateInfo['completed'])){
                continue;
            }

            $dateTime = DateTime::createFromFormat('d/m/Y H:i:s', $dateInfo['completed']);
            $date = $dateTime->getTimestamp();

            $difference = abs($date - $today); // Absolute difference between the dates

            // Update closest date if the current date is closer to today
            if ($difference < $closestDifference) {
                $recentData = $dateInfo;
                $closestDifference = $difference;
            }
        }

        return $recentData;

    }
}
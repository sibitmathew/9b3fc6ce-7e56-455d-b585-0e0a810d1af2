<?php


namespace ReportProcess\Processors;

use DateTime;
use Exception;
use ReportProcess\Services\StudentService;
use ReportProcess\Services\StudentResponsesService;
use ReportProcess\Services\QuestionsService;
use ReportProcess\Services\AssessmentService;

/**
 * Class DiagnosticReportProcessor
 * @package ReportProcess\Processors
 */
class ReportProcessor
{
    const DIAGNOSTIC_REPORT = "1";

    const PROGRESS_REPORT = "2";

    const FEEDBACK_REPORT = "3";

    /**
     * @var
     */
    private $studentData;

    /**
     * @var
     */
    private $studentResponsedata;

    /**
     * @var
     */
    private $studentLatestCompletedData;

    /**
     * @var
     */
    private $resultData;

    /**
     * @var AssessmentService
     */
    private $assessmentService;

    /**
     * @var StudentResponsesService
     */
    private $studentResponseService;

    /**
     * @var
     */
    private $studentId;

    /**
     * @param $data
     * @param $studentId
     * @throws Exception
     */
    public function Process($data, $studentId, $choice)
    {
        if(!in_array($choice, [self::DIAGNOSTIC_REPORT, self::FEEDBACK_REPORT, self::PROGRESS_REPORT])){
            throw new Exception("Invalid choice!");
        }

        //Get student data
        $this->studentData = (new StudentService())->getStudentData($data, $studentId);
        if(empty($this->studentData)){
            throw new Exception("Student data not found!");
        }

        $this->studentResponseService = new StudentResponsesService();

        $this->studentResponsedata = $this->studentResponseService->getStudentResponseData($data, $studentId);
        if(empty($this->studentResponsedata)){
            throw new Exception("Invalid student response!");
        }

        $this->studentLatestCompletedData = (new StudentResponsesService())->getStudentRecentCompletedResponseData($this->studentResponsedata);
        if(empty($this->studentLatestCompletedData)){
            throw new Exception("Empty student completion data!");
        }

        $this->resultData = $this->getResultData($this->studentLatestCompletedData, $data);
        if(empty($this->resultData)){
            throw new Exception("Empty result data!");
        }

        $this->assessmentService = new AssessmentService();
        $this->studentId = $studentId;

        if (self::DIAGNOSTIC_REPORT === $choice){
            $this->displayDiagnosticReport($data);
        }

        if (self::PROGRESS_REPORT === $choice){
            $this->displayProgressReport($data);
        }

        if (self::FEEDBACK_REPORT === $choice){
            $this->displayFeedbackReport($data);
        }

    }

    private function displayDiagnosticReport($data)
    {
        $assessmentId = isset($this->studentLatestCompletedData['assessmentId']) ? $this->studentLatestCompletedData['assessmentId'] : '';
        $assessment = $this->assessmentService->getAssessmentById($data, $assessmentId);

        $studentName = "";
        $assessmentName = "";
        $totalQuestions = isset($this->studentLatestCompletedData['responses']) ? count($this->studentLatestCompletedData['responses']) : 0;
        $correctAns = isset($this->studentLatestCompletedData['results']['rawScore']) ? $this->studentLatestCompletedData['results']['rawScore'] : 0;

        foreach ($this->studentData as $student){
            $studentName = $student['firstName'].' '.$student['lastName'];
        }

        foreach ($assessment as $asm){
            $assessmentName = $asm['name'];
        }

        $d = DateTime::createFromFormat('d/m/Y H:i:s', $this->studentLatestCompletedData['completed']);

        echo "\n\n";
        echo "{$studentName} recently completed {$assessmentName} assessment on {$d->format('jS F Y h:i A')}\n";
        echo "He got {$correctAns} questions right out of {$totalQuestions}. Details by strand given below:\n\n\n";

        foreach ($this->resultData['result'] as $strand => $score){
            echo "{$strand}: {$score} out of {$this->resultData['total'][$strand]} correct\n";
        }
        echo "\n\n";

    }

    private function displayProgressReport($data)
    {
        $studentName = "";
        foreach ($this->studentData as $student){
            $studentName = $student['firstName'].' '.$student['lastName'];
        }

        echo "\n";
        foreach ($data["assessments"] as $assessment){
            $completedAssessments = $this->studentResponseService->getTotalStudentCompletedAssesmentsById($data, $this->studentId, $assessment['id']);
            $count = count($completedAssessments);
            if($count <= 0){
                continue;
            }
            echo "{$studentName} has completed {$assessment['name']} assessment {$count} times in total. Date and raw score given below:\n\n";

            $scores = [];
            foreach ($completedAssessments as $assessment){
                $total = count($assessment['responses']);
                $scores[] = $assessment['results']['rawScore'];
                $d = DateTime::createFromFormat('d/m/Y H:i:s', $assessment['completed']);
                echo "Date: {$d->format('jS F Y')}, Raw Score: {$assessment['results']['rawScore']} out of {$total}\n";
            }

            $first = reset($scores);
            $last = end($scores);
            $diff = abs($first-$last);

            $m = "less";
            if($last > $first){
                $m = "more";
            }


            echo "\n";
            echo "{$studentName} got {$diff} {$m} correct in the recent completed assessment than the oldest";
            echo "\n\n";

        }

    }

    private function displayFeedbackReport($data)
    {
        $assessmentId = isset($this->studentLatestCompletedData['assessmentId']) ? $this->studentLatestCompletedData['assessmentId'] : '';
        $assessment = $this->assessmentService->getAssessmentById($data, $assessmentId);

        $studentName = "";
        $assessmentName = "";
        $totalQuestions = isset($this->studentLatestCompletedData['responses']) ? count($this->studentLatestCompletedData['responses']) : 0;
        $correctAns = isset($this->studentLatestCompletedData['results']['rawScore']) ? $this->studentLatestCompletedData['results']['rawScore'] : 0;

        foreach ($this->studentData as $student){
            $studentName = $student['firstName'].' '.$student['lastName'];
        }

        foreach ($assessment as $asm){
            $assessmentName = $asm['name'];
        }

        $d = DateTime::createFromFormat('d/m/Y H:i:s', $this->studentLatestCompletedData['completed']);

        echo "\n\n";
        echo "{$studentName} recently completed {$assessmentName} assessment on {$d->format('jS F Y h:i A')}\n";
        echo "He got {$correctAns} questions right out of {$totalQuestions}. Feedback for wrong answers given below:\n";

        $questionService = new QuestionsService();
        foreach ($this->studentLatestCompletedData['responses'] as $response){
            $question = $questionService->getQuestionsById($data, $response['questionId']);
            foreach ($question as $qn){
                if($response['response'] === $qn['config']['key']){
                    continue;
                }
                echo "Question: {$qn['stem']}\n";
                $rsp = $questionService->getOptionsById($qn, $response['response']);
                foreach ($rsp as $r){
                    echo "Your answer: {$r['label']} with value {$r['value']}\n";
                }
                $rightRsp = $questionService->getOptionsById($qn, $qn['config']['key']);
                foreach ($rightRsp as $rs){
                    echo "Right answer: {$rs['label']} with value {$rs['value']}\n";
                }

                echo "Hint: {$qn['config']['hint']}\n";

            }

        }
        echo "\n";
    }

    /**
     * @param $studentLatestCompletedData
     * @param $data
     * @return array
     */
    private function getResultData($studentLatestCompletedData, $data)
    {
        if(!isset($studentLatestCompletedData['responses'])){
            return [];
        }

        $questionService = new QuestionsService();
        $totalQns = [];
        $resultData = [];

        foreach ($studentLatestCompletedData['responses'] as $idx => $response){
            if(!isset($response['questionId'])){
                continue;
            }

            $question = $questionService->getQuestionsById($data, $response['questionId']);

            if(!isset($question[$idx]['strand'])){
                continue;
            }

            if(!isset($resultData[$question[$idx]['strand']])){
                $resultData[$question[$idx]['strand']] = 0;
            }

            if(!isset($totalQns[$question[$idx]['strand']])){
                $totalQns[$question[$idx]['strand']] = 0;
            }

            if(!isset($question[$idx]['config']['key'])){
                continue;
            }

            if(!isset($response['response'])){
                continue;
            }

            $totalQns[$question[$idx]['strand']]++;

            if($response['response'] === $question[$idx]['config']['key']){
                $resultData[$question[$idx]['strand']]++;
            }
        }

        return [
            'total' => $totalQns,
            'result'=> $resultData,
        ];

    }
}
<?php
namespace ReportProcess;

use ReportProcess\Processors\ReportProcessor;
use ReportProcess\Processors\DataProcessor;

/**
 * Class Reporting
 * @package ReportProcess
 */
class Reporting
{
    /**
     * @var string|null
     */
    private $studentId;

    /**
     * @var int|null
     */
    private $choice;

    /**
     * @var array|null
     */
    private $data;

    /**
     * Reporting constructor.
     * @param $studentId
     * @param $choice
     */
    public function __construct($studentId, $choice)
    {
        $this->studentId = $studentId;
        $this->choice = $choice;
        $this->data = (new DataProcessor())->Process();
    }

    /**
     * Run function
     *
     */
    public function run()
    {
        try{
            (new ReportProcessor())->Process($this->data, $this->studentId, $this->choice);
        } catch(\Exception $e){
            echo $e->getMessage()."\n";
        }

    }
}
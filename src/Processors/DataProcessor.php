<?php
namespace ReportProcess\Processors;

use ReportProcess\Interfaces\DataProcessorInterface;
use ReportProcess\Services\DataService;

/**
 * Class DataProcessorInterface
 *
 * @package ReportProcess\Processors
 */
class DataProcessor implements DataProcessorInterface
{
    /**
     * To Start Data Process
     */
    public function Process()
    {
        $dataService = new DataService();
        return $dataService->initData();
    }
}
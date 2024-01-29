<?php


namespace ReportProcess\Interfaces;


interface ReportProcessorInterface
{
    public function Process($data, $studentId, $choice);
}
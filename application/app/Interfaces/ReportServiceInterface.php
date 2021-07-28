<?php

namespace App\Interfaces;

interface ReportServiceInterface {

    public function getTransactionReport(int $projectId = NULL);

}

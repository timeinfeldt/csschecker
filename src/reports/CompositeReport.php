<?php
namespace csschecker\reports;

class CompositeReport extends Report {

    private $reports;

    /**
     * @param Report[] $reports
     */
    public function __construct($reports) {
        $this->reports = $reports;
    }

    public function generateReport() {
        foreach ($this->reports as $report) {
            $report->generateReport();
        }
    }

    public function addWarning($check, $entity, $message) {
        foreach ($this->reports as $report) {
            $report->addWarning($check, $entity, $message);
        }
    }

    public function setStartTime($startTime) {
        foreach ($this->reports as $report) {
            $report->setStartTime($startTime);
        }
    }

}


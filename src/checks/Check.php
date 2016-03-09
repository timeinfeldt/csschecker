<?php
namespace csschecker\checks;

use csschecker\reports\Report;

abstract class Check {

    public function __construct(Report $report, $config) {
        $this->config = $config;
        $this->report = $report;
    }

    public function addWarning($entity, $message) {
        $this->report->addWarning(get_class($this), $entity, $message);
    }

    abstract public function run($item);
}

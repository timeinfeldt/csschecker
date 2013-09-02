<?php
namespace csschecker\reports;

abstract class Report {

    protected $startTime;

    protected $warnings;

    abstract public function generateReport();

    public function addWarning($check, $entity, $message) {
        $warning = array(
            'entity' => $entity,
            'message' => $message
        );
        $this->warnings[$check][] = $warning;
    }

    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

}


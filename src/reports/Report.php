<?php
namespace csschecker\reports;

class Report {

    public function write($fp, $message) {
        fwrite($fp, $message . PHP_EOL);
        print_r($message . PHP_EOL);
    }

    public function printReport($start_time) {
        // mark the stop time
        $stop_time = microtime(true);

        // get the difference in seconds
        $time = $stop_time - $start_time;

        $fp = fopen(__DIR__ . "/../results.txt","wb");

        $this->write($fp, "\n");
        $this->write($fp, "CSS check finished after " . number_format($time, 2)  . " seconds.");

        foreach ($this->warnings as $check => $warnings) {
            $this->write($fp, "\n");
            $this->write($fp, $check);
            $this->write($fp, "========================");
            foreach($warnings as $warning) {
                $this->write($fp, $warning['entity'] . ": " . $warning['message']);
            }
        }

        fclose($fp);
    }

    public function addWarning($check, $entity, $message) {
        $warning = array(
            'entity' => $entity,
            'message' => $message
        );
        $this->warnings[$check][] = $warning;
    }
}


<?php
namespace csschecker\reports;

class FileReport extends Report {

    private $fileHandle;

    public function __construct($fileHandle) {
        $this->fileHandle = $fileHandle;
    }

    private function write($message) {
        fwrite($this->fileHandle, $message . PHP_EOL);
    }

    public function generateReport() {
        $time = microtime(true) - $this->startTime;

        $this->write(PHP_EOL);
        $this->write("CSS check finished after " . number_format($time, 2)  . " seconds." . PHP_EOL);

        foreach ($this->warnings as $check => $warnings) {
            $this->write($check);
            $this->write("========================");
            foreach($warnings as $warning) {
                $this->write($warning['entity'] . ": " . $warning['message']);
            }
        }
    }

}


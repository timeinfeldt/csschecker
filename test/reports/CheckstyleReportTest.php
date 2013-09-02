<?php
namespace csschecker\reports;

class CheckstyleReportTest extends \PHPUnit_Framework_TestCase {

    private $tempFile;

    public function setUp() {
        $this->tempFile = __DIR__ . 'checkstyleReportOutput.tmp.xml';
        $this->removeTempFile();
    }

    public function testCheckstyleOutput() {
        $report = new CheckstyleReport($this->tempFile);
        $report->addWarning('NoUsageCheck', '.bar', 'Bar closed');
        $report->addWarning('NoUsageCheck', '.foo', 'Fooooooooo');
        $report->addWarning('SelectorLengthCheck', '.foo .bar .baz .bobz', 'Selector too deep');
        $report->generateReport();
        $this->assertXmlFileEqualsXmlFile(
            __DIR__ . '/CheckstyleReportTestOutput.xml',
            $this->tempFile
        );
    }

    public function tearDown() {
        $this->removeTempFile();
    }

    private function removeTempFile() {
        if (file_exists($this->tempFile)) {
            @unlink($this->tempFile);
        }
    }

}
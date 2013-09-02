<?php
namespace csschecker\reports;

class CheckstyleReport extends Report {

    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function addWarning($check, $entity, $message) {
        $warning = array(
            'check' => $check,
            'message' => $message
        );
        $this->warnings[$entity][] = $warning;
    }

    public function generateReport() {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('checkstyle');
        $doc->appendChild($root);

        foreach ($this->warnings as $entity => $warnings) {
            $file = $doc->createElement('file');
            $file->setAttribute('name', $entity);
            $root->appendChild($file);
            foreach ($warnings as $warning) {
                $error = $doc->createElement('error');
                $error->setAttribute('message', $warning['message']);
                $error->setAttribute('severity', 'error');
                $error->setAttribute('line', 1);
                $error->setAttribute('column', 1);
                $error->setAttribute('source', $warning['check']);
                $file->appendChild($error);
            }
        }
        if (!$doc->schemaValidateSource(file_get_contents(__DIR__ . '/checkstyle.xsd'))) {
            throw new \Exception('Checkstyle generation failed.');
        }
        $doc->formatOutput = true;
        $doc->save($this->filename);
    }

}


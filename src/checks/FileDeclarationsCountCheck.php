<?php
namespace csschecker\checks;

class FileDeclarationsCountCheck extends CssFileCheck {
    public function run($file) {
        $this->addWarning($file['name'], $file['declarationsCount'] . " declaration(s).");
    }
}
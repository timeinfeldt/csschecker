<?php
namespace csschecker\checks;

class ClassLengthCheck extends ClassCheck {

    public function run($class) {
        if (strlen($class['name']) > $this->config->maxClassLength) {
            $this->addWarning($class['name'], 'Over ' . $this->config->maxClassLength . ' characters long.');
        }
    }
}

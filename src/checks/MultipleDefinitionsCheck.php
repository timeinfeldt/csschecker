<?php
namespace csschecker\checks;

class MultipleDefinitionsCheck extends ClassCheck {
    public function run($class) {
        if ($class['defCount'] > $this->config['maxDefinitions']) {
            $this->addWarning($class['name'], 'Defined ' . $class['defCount'] . ', used ' . $class['useCount']);
        }
    }
}

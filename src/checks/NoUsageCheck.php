<?php
namespace csschecker\checks;

class NoUsageCheck extends ClassCheck {
    public function run($class) {
        if ($class['useCount'] == 0) {
            $this->addWarning($class['name'], 'Not used.');
        }
    }
}

<?php
namespace csschecker\checks;

use csschecker\Helpers;

class SelectorLengthCheck extends SelectorCheck {
    public function run($selector) {
        $matches = Helpers::getElementsInSelectorString($selector['string']);

        if (count($matches) > $this->config->maxSelectorLength) {
            $this->addWarning($selector['string'], 'Selector is over ' . $this->config->maxSelectorLength . ' levels deep.');
        }
    }
}
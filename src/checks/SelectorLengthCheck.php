<?php
namespace csschecker\checks;

class SelectorLengthCheck extends SelectorCheck {
    public function run($selector) {
        $selectorFragments = explode(" ", $selector['string']);

        if (count($selectorFragments) > $this->config['maxSelectorLength']) {
            $this->addWarning($selector['string'], 'Selector is over ' . $this->config['maxSelectorLength'] . ' levels deep.');
        }
    }
}

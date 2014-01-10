<?php
namespace csschecker\checks;

class SelectorLengthCheck extends SelectorCheck {
    public function run($selector) {
        $matches = array();
        preg_match_all("(\.(?P<classes>-?[_a-zA-Z][_a-zA-Z0-9-]*))", $selector['string'], $matches);
		
        if (count($matches) > $this->config['maxSelectorLength']) {
            $this->addWarning($selector['string'], 'Selector is over ' . $this->config->maxSelectorLength . ' levels deep.');
        }
    }
}

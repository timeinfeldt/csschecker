<?php
namespace csschecker\checks;

class RuleUsageCheck extends RuleCheck {
    public function run($rule) {
        $this->addWarning($rule['name'], 'Defined ' . $rule['defCount'] . ' times with ' . count($rule['values']) . ' value(s).');
    }
}
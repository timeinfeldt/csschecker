<?php
namespace csschecker\checks;

class RuleUsageCheck extends RuleCheck {

    private $whiteList = array(
        'color',
        'line-height',
        'font-size',
        //'padding',
        //'margin'
    );

    public function run($rule) {
        if (in_array($rule['name'], $this->whiteList)) {

            usort($rule['values'], function($a, $b) {
                return $b['defCount'] - $a['defCount'];
            });

            $message = 'Defined ' . $rule['defCount'] . ' times with ' . count($rule['values']) . ' values: ' . PHP_EOL;
            foreach ($rule['values'] as $value) {
                $message .= " > " . $value['name'] . " (" . $value['defCount'] . ")" . PHP_EOL;
            }
            $this->addWarning($rule['name'], $message);
        }
    }
}
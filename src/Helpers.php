<?php
namespace csschecker;

class Helpers {

    public static function getClassesInSelectorString($selectorString) {
        $matches = array();
        preg_match_all("(\.(?P<classes>-?[_a-zA-Z][_a-zA-Z0-9-]*))", $selectorString, $matches);
        return $matches['classes'];
    }

    public static function getElementsInSelectorString($selectorString) {
        $matches = array();
        $elements = explode(" ", $selectorString);
        foreach ($elements as $element) {
            if (preg_match("~[a-z]~i", $element)) {
                $matches[] = $element;
            }
        }
        return $matches;
    }
}
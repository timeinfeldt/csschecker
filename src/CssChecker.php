<?php
namespace csschecker;

use csschecker\reports\Report;

class CssChecker {

    private $selectors = array();

    private $isVerbose = false;

    public function runChecks($options, $checksConfig, Report $report) {
        $report->setStartTime(microtime(true));

        $paths = array();

        while (($arg = array_shift($options)) !== null) {
            switch ($arg) {
                case '--verbose':
                    $this->isVerbose = true;
                    break;
                case '--something':
                    $arg = array_shift($options);
                    break;
                default:
                    $paths[] = $arg;
            }
        }

        if (count($paths) != 2) {
            die('You dont even know how to cli?');
        }

        list($codeDirectoryPath, $cssDirectoryPath) = $paths;

        //gather required data
        $selectors = $this->getSelectorsInDirectory($cssDirectoryPath);

        $classes = $this->getClassUsageInDirectory($codeDirectoryPath, $cssDirectoryPath);

        foreach ($checksConfig as $checkName => $config) {

            $checkName = '\\csschecker\\checks\\' . $checkName;
            $check = new $checkName($report, $config);

            if ($check instanceof \csschecker\checks\SelectorCheck) {
                foreach ($selectors as $selector) {
                    $check->run($selector);
                }
            } else if ($check instanceof \csschecker\checks\ClassCheck) {
                foreach ($classes as $class) {
                    $check->run($class);
                }
            } else {
                die('dafuq');
            }
        }
        $report->generateReport();
    }

    public function getFilesInPath($realpath, $match_regex) {
        $path = realpath($realpath);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $filteredFiles = new \RegexIterator($files, $match_regex, \RecursiveRegexIterator::GET_MATCH);
        return $filteredFiles;
    }

    public function getSelectorsInDirectory($path) {

        if (count($this->selectors) > 0) {
            return $this->selectors;
        }

        $selectors = array();

        //get all CSS files
        $cssFiles = $this->getFilesInPath(
            $path,
            '(\.(css)$)i'
        );

        foreach ($cssFiles as $cssFileName => $cssFileObject) {

            if($this->isVerbose){
                print_r("Collecting CSS selectors from: " . $cssFileName . "\n");
            }

            $oSettings = \Sabberworm\CSS\Settings::create()->withMultibyteSupport(false);
            $oCssParser = new \Sabberworm\CSS\Parser(file_get_contents($cssFileName), $oSettings);
            $oCss = $oCssParser->parse();

            foreach ($oCss->getAllDeclarationBlocks() as $oBlock) {
                foreach ($oBlock->getSelectors() as $oSelector) {
                    $selector = $oSelector->getSelector();
                    $selector = array(
                        'string' => $selector,
                        'defLocation' => $cssFileName
                    );
                    $selectors[] = $selector;
                }
            }
        }

        $this->selectors = $selectors;

        return $selectors;
    }

    public function getClassesInDirectory($cssDirectoryPath) {

        $selectors = $this->getSelectorsInDirectory($cssDirectoryPath);

        $classes = array();

        foreach ($selectors as $selector) {

            $classesInSelector = $this->getClassesInSelector($selector);
            foreach ($classesInSelector as $class) {
                if (isset($classes[$class['name']])) {
                    $classes[$class['name']]['defCount'] += 1;
                } else {
                    $classes[$class['name']]['defCount'] = 1;
                    $classes[$class['name']]['useCount'] = 0;
                }
                $classes[$class['name']]['name'] = $class['name'];
                $classes[$class['name']]['defLocations'][] = $class['defLocation'];
            }
        }

        return $classes;
    }

    public function getClassesInSelector($selector) {
        $classNames = $this->getClassesInSelectorString($selector['string']);
        $classes = array();

        foreach($classNames as $className) {
            $classes[] = array(
                'name' => $className,
                'defLocation' => $selector['defLocation']
            );
        }

        return $classes;
    }

    public function getClassesInSelectorString($selectorString) {
        $matches = array();
        preg_match_all("(\.(?P<classes>-?[_a-zA-Z][_a-zA-Z0-9-]*))", $selectorString, $matches);
        return $matches['classes'];
    }

    public function getClassUsageInDirectory($codeDirectoryPath, $cssDirectoryPath) {
        $classes = $this->getClassesInDirectory($cssDirectoryPath);

        //get all code files
        $filteredCodeFiles = $this->getFilesInPath(
            $codeDirectoryPath,
            '(\.(js|php|html)$)i'
        );

        //search code files for css classes
        foreach ($filteredCodeFiles as $codeFileName => $codeFileObject) {
            $fileContent = file_get_contents($codeFileName);

            if($this->isVerbose){
                print_r("Searching for classes in: " . $codeFileName . "\n");
            }

            //search this file for css classes
            foreach ($classes as $className => $counters) {
                if (strpos($fileContent, $className) !== false) {
                    $classes[$className]['useCount'] += 1;
                    $classes[$className]['useLocations'][] = $codeFileName;
                }
            }
        }

        return $classes;
    }
}

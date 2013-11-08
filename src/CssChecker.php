<?php
namespace csschecker;

use csschecker\reports\Report;

class CssChecker {

    private $selectors = array();

    private $rules = array();

    private $cssFiles = array();

    private $isVerbose = false;

    private $config;

    public function runChecks($options, Report $report) {
        $report->setStartTime(microtime(true));

        $paths = array();

        while (($arg = array_shift($options)) !== null) {
            switch ($arg) {
                case '--verbose':
                    $this->isVerbose = true;
                    break;
                case '--config':
                    $this->config = array_shift($options);
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
        $this->parseDeclarations($cssDirectoryPath);

        $classes = $this->getClassUsage($codeDirectoryPath, $cssDirectoryPath);

        $checksConfig = $this->loadConfig();

        foreach ($checksConfig as $checkName => $config) {

            $checkName = '\\csschecker\\checks\\' . $checkName;
            $check = new $checkName($report, $config);

            if ($check instanceof \csschecker\checks\SelectorCheck) {
                foreach ($this->selectors as $selector) {
                    $check->run($selector);
                }
            } else if ($check instanceof \csschecker\checks\ClassCheck) {
                foreach ($classes as $class) {
                    $check->run($class);
                }
            } else if ($check instanceof \csschecker\checks\RuleCheck) {
                foreach ($this->rules as $rule) {
                    $check->run($rule);
                }
            } else if ($check instanceof \csschecker\checks\CssFileCheck) {
                foreach ($this->cssFiles as $file) {
                    $check->run($file);
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

    public function parseCSSFile($cssFileName) {
        if ($this->isVerbose) {
            print_r("Parsing: " . $cssFileName . "\n");
        }

        $oSettings = \Sabberworm\CSS\Settings::create()->withMultibyteSupport(false);
        $oCssParser = new \Sabberworm\CSS\Parser(file_get_contents($cssFileName), $oSettings);
        $oCss = $oCssParser->parse();

        return $oCss;
    }

    public function parseDeclarations($path) {

        $selectors = array();

        $rules = array();

        $files = array();

        //get all CSS files
        $cssFiles = $this->getFilesInPath(
            $path,
            '(\.(css)$)i'
        );

        foreach ($cssFiles as $cssFileName => $cssFileObject) {

            $oCss = $this->parseCSSFile($cssFileName);

            $declarations = $oCss->getAllDeclarationBlocks();

            foreach ($declarations as $oBlock) {
                foreach ($oBlock->getSelectors() as $oSelector) {
                    $selectors[] = array(
                        'string' => $oSelector->getSelector(),
                        'defLocation' => $cssFileName
                    );
                }

                foreach ($oBlock->getRules() as $oRule) {
                    $rule = $oRule->getRule();
                    $val = (string) $oRule->getValue();

                    if (isset($rules[$rule])) {
                        $rules[$rule]['defCount'] += 1;
                        if (isset($rules[$rule]['values'][$val])) {
                            $rules[$rule]['values'][$val]['defCount'] += 1;
                        } else {
                            $rules[$rule]['values'][$val]['name'] = $val;
                            $rules[$rule]['values'][$val]['defCount'] = 1;
                        }
                    } else {
                        $rules[$rule]['name'] = $rule;
                        $rules[$rule]['defCount'] = 1;
                        $rules[$rule]['values'][$val]['name'] = $val;
                        $rules[$rule]['values'][$val]['defCount'] = 1;
                    }
                }
            }

            $files[] = array(
                'name' => $cssFileName,
                'declarationsCount' => count($declarations)
            );

        }

        $this->cssFiles = $files;
        $this->selectors = $selectors;
        $this->rules = $rules;
    }

    public function getClasses() {

        $classes = array();

        foreach ($this->selectors as $selector) {

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

        foreach ($classNames as $className) {
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

    public function getClassUsage($codeDirectoryPath) {
        $classes = $this->getClasses();

        //get all code files
        $filteredCodeFiles = $this->getFilesInPath(
            $codeDirectoryPath,
            '(\.(js|php|html)$)i'
        );

        //search code files for css classes
        foreach ($filteredCodeFiles as $codeFileName => $codeFileObject) {
            $fileContent = file_get_contents($codeFileName);

            if ($this->isVerbose) {
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

    private function loadConfig() {
        if ($this->config && file_exists($this->config)) {
            return $this->parseConfigFile($this->config);
        }
        if ($this->config && file_exists(getcwd() . '/' . $this->config)) {
            return $this->parseConfigFile(getcwd() . '/' . $this->config);
        }
        $defaultConfigPath = getcwd() . '/csschecker.json';
        if (file_exists($defaultConfigPath)) {
            return $this->parseConfigFile($defaultConfigPath);
        }
        return $this->parseConfigFile(__DIR__ . '/../default-config.json');
    }

    private function parseConfigFile($configFilePath) {
        $configFile = file_get_contents($configFilePath);
        if (!$configFile) {
            throw new \Exception('Config file ' . $configFilePath . ' could not be read.');
        }
        $config = json_decode($configFile);
        if ($config === null) {
            throw new \Exception('Json could not be parsed');
        }
        return $config;
    }

}
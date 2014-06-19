<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 6/19/14 1:30 PM
 */

namespace lib\RequireJS;

/**
 * Class Optimizer
 *
 * @package lib\RequireJS
 */
class Optimizer {
    private $baseDir;
    public $exclude = ['jquery'=>1];

    private $importedModule = [];
    private $modules = [];

    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }

    public function addModule($name) {
        if (!isset($this->importedModule[$name]) && !isset($this->exclude[$name])) {
            $this->importedModule[$name] = true;

            foreach (self::getDependencyFromFile($this->getJsFileName($name)) as $m) {
                if ($m{0} == '.') $m = dirname($name) . substr($m, 1);
                $this->addModule($m);
            }

            $this->modules[] = $name;
        }
    }

    public function export() {
        $result = [];
        foreach($this->modules as $m) {
            $content = file_get_contents($this->getJsFileName($m));
            $result[] = preg_replace('/define\s*\(\s*\[/', 'define("' . $m . '", [', $content);
        }

        return implode("\n", $result);
    }

    private function getJsFileName($moduleName) {
        return $this->baseDir . '/' . $moduleName . '.js';
    }

    private static function getDependencyFromFile($fileName) {
        $content = file_get_contents($fileName, null, null, 0, 1024);
        if (preg_match('/define\s*\(\s*\[\s*([^]]+)\]/s', $content, $m) > 0) {
            $modules = [];
            foreach(explode(',', $m[1]) as $module) {
                $modules[] = trim($module, " \t'\"");
            }
            return $modules;
        }
        return [];
    }
} 
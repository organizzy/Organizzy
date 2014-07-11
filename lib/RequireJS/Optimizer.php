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
    const DEFINE_PREFIX = '_mod_';
    private $baseDir;
    public $exclude = [];

    private $importedModule = [];
    private $modules = [];
    private $neededModules = [];
    public $globalVars = [];

    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }

    public function addExternalModule($name) {
        $this->exclude[$name] = true;
    }

    public function addModule($name) {
        $this->neededModules[$name] = self::DEFINE_PREFIX . str_replace('/', '_', $name);

        if (isset($this->exclude[$name])) {
            return $this->neededModules[$name];
        }
        elseif (!isset($this->importedModule[$name])) {
            $this->importedModule[$name] = [
                'var' => $this->neededModules[$name]
            ];;

            $dependency = [];
            foreach (self::getDependencyFromFile($this->getJsFileName($name)) as $m) {
                if ($m{0} == '.') $m = dirname($name) . substr($m, 1);
                $dependency[] = $this->addModule($m);
            }

            $this->importedModule[$name]['dependency'] = $dependency;
            $this->modules[] = $name;
        }
        return $this->neededModules[$name];
    }

    public function export($modules) {
        $result = [];

        $vars = [];
        $i = 1;
        foreach($this->neededModules as $k=>$v) {
            if (isset($this->exclude[$k]) || in_array($k, $modules)) {
                $name = $k;
            } else {
                $name = '_m' . $i++;
            }
            $vars[] = $v . ' = "' . $name . '"';
        }
        //$result[] = '!function(' . implode(',', $fun_args) . '){' . PHP_EOL;
        foreach($this->modules as $m) {
            $data = $this->importedModule[$m];
            $content = file_get_contents($this->getJsFileName($m));
            //$result[] = preg_replace('/define\s*\(\s*\[/', 'define("' . $m . '", [', $content);
            $result[] = preg_replace(
                '/define\s*\(\s*\[[^\]]*\]+\s*,/',
                'define(' . $data['var'] . ', [' . implode(',', $data['dependency']) . '], ',
                $content
            );
        }

        if (count($this->globalVars) > 0) {
            $globals = 'define, ' . implode(', ', $this->globalVars);
        } else {
            $globals = 'define';
        }


        return '!function(' . $globals . ', undefined){ var ' . implode(', ', $vars) . ";" .
            implode(";", $result) . '}(' . $globals . ')';
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
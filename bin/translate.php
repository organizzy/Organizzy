<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 6/10/14 7:50 PM
 */

$languages = ['id_ID', 'su'];

$result = TranslationFinder::findRecursive(__DIR__ . '/../protected/');
foreach($languages as $lang){
    $result->export(__DIR__ . '/../protected/messages/' . $lang . '/organizzy.php');
}

class TranslationFinder {
    private $parent;
    private $result;
    
    public function __construct($parent) {
        $this->parent = $parent;
        $this->result = new TranslationResult();
    }

    public static function findRecursive($directory) {
        $finder = new self($directory);
        $finder->findRecursiveInternal('/');
        return $finder->result;
    }

    private function findRecursiveInternal($dir) {
        $parent = $this->parent;
        if ($dh = opendir($parent . $dir)) {
            while ($file = readdir($dh)) {
                if ($file == '.' || $file == '..') continue;
                $fullPath = $parent . $dir . $file;
                if ( is_dir($fullPath)) {
                    $this->findRecursiveInternal($dir . $file . '/');
                }
                elseif (substr_compare($file, '.php', -4) == 0) {
                    $this->findInFile($dir . $file);
                }
            }
        }


    }

    private function findInFile($file) {
        // , PREG_OFFSET_CAPTURE
        if ($fh = fopen($this->parent . $file, 'r')) {
            $line = 1;
            while(($buffer = fgets($fh)) !== false) {
                if (preg_match_all('/(_t|_p)\s*\(\s*(\'(?:[^\']|(?<=\\\)\')*\'|"(?:[^"]|(?<=\\\)")*")/', $buffer, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
                    foreach ($m as $set) {
                        $msg = stripslashes(substr($set[2][0], 1, -1));

                        echo substr($file, 1), ':', $line,':', $set[0][1], ' ', $msg, "\n";
                        $this->result->add($msg, substr($file, 1), $line, $set[0][1]);
                    } 
                }
                $line++;
            }
            fclose($fh);
        }
        
        // Yii::t\s*\(\s*##PATTERN##\s*,\s*##PATTERN##\s*\)
        // ('(?:[^']|(?<=\\)')*'|"(?:[^"]|(?<=\\)")*")

        
    }

}

class TranslationResult {

    private $data = [];

    public function add($msg, $file, $line, $offset) {
        if (!isset($this->data[$msg])) {
            $this->data[$msg] = [];
        }

        $this->data[$msg][] = [$file, $line, $offset];
    }

    public function export($file) {
        $bufferTop = '';
        $bufferDown = '';
        $current = [];
        if (file_exists($file)) {
            $current = include($file);
        }

        //ksort($this->data);
        foreach ($this->data as $msg => $locs) {
            $item = '';
            foreach($locs as $loc) {
                $item .= '// ' . implode(':', $loc) . "\n";
            }
            $item .= "'" . addcslashes($msg, "'\\") . "' => ";

            if (isset($current[$msg]) && $current[$msg] != '')
                $bufferDown .= $item . "'" . addcslashes($current[$msg], "'\\") . "',\n\n";
            else
                $bufferTop .= $item . "'',\n\n";
        }
        file_put_contents($file, "<?php return [\n\n" . $bufferTop . "\n// ------------ TRANSLATED --------\n\n" . $bufferDown . "];\n");

    }
}

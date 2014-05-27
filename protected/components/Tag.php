<?php
/**
 * Organizzy
 * Copyright (C) 2014 Organizzy Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 */
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Tag {

    private $_name;

    private $_options;

    private $_contents = [];

    private $_forceClose;

    public function __construct($tagName, $options = [], $forceClose = true){
        $this->_name = $tagName;
        $this->_options = $options;

    }

    /**
     * Render current tag and return it's html string
     * @return string
     */
    public function render() {
        $attr = '';
        if ($this->_options) {
            foreach($this->_options as $name => $value) {
                if ($value === true) $value = $name;
                $attr .= ' ' . $name . '="' . htmlentities($value, ENT_COMPAT) . '"';
            }
        }
        $html = '<' . $this->_name . $attr;
        if (!$this->_contents && !$this->_forceClose)
            return $html . ' />';
        else
            $html .= '>';

        foreach ($this->_contents as $content) {
            $html .= $content;
        }

        return $html . '</' . $this->_name . '>';

    }

    /**
     * Append $content to this tag
     *
     * @param Tag|string $content
     * @return $this
     */
    public function append($content) {
        $this->_contents[] = $content;
        return $this;
    }

    /**
     * Set tag attribute
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function attr($name, $value) {
        $this->_options[$name] = $value;
        return $this;
    }

    /**
     * set id attribute
     *
     * @param string $id
     * @return $this
     */
    public function id($id) {
        return $this->attr('id', $id);
    }

    /**
     * add class
     *
     * @param string|string[] $class
     * @return $this
     */
    public function addClass($class) {
        if (is_array($class)) $class=implode(' ', $class);
        if (!isset($this->_options['class'])) $this->_options['class'] = $class;
        else $this->_options['class'] .= ' ' . $class;
        return $this;
    }

    /**
     * call render
     *
     * @return string
     */
    public function __toString() {
        return $this->render();
    }


    /**
     *
     * @param string $name tag name
     * @param array $options
     * @return Tag
     */
    private static function inlineTag($name, $options = []) {
        return (new Tag($name, $options, false));
    }

    /**
     * @param $name
     * @param $content
     * @param array $options
     * @return $this
     */
    private static function contentTag($name, $content, $options = []) {
        return (new Tag($name, $options, true))->append($content);
    }


    public static function b($content = '', $htmlOptions = []) {
        return self::contentTag('strong', $content, $htmlOptions);
    }

    public static function strong($content, $htmlOptions = []) {
        return self::contentTag('strong', $content, $htmlOptions);
    }

    public static function div($content = '', $htmlOptions = []) {
        return self::contentTag('div', $content, $htmlOptions);
    }


    public static function a($content = '', $htmlOptions = []) {
        return self::contentTag('a', $content, $htmlOptions);
    }

    public static function link($url, $content = '', $htmlOptions = []) {
        return self::a($content, $htmlOptions)->attr('href', CHtml::normalizeUrl($url));
    }


    public function input($name, $type = 'text', $options = []) {
        return self::inlineTag('input', $options)->attr('name', $name)->attr('type', $type);
    }


    public function select($name, $htmlOptions = []) {
        return new SelectTag();
    }
}

class SelectTag extends Tag {
    public function __construct($options = []) {
        parent::__construct('select', $options, true);
    }

    public function addOption($label, $value = null, $options = []) {
        if (!$value) $value = $label;
        return $this->append((new Tag('option', $options))->attr('value', $value)->append($value));
    }
}
<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 6/11/14 7:52 PM
 */

/**
 * Class SettingForm
 *
 * @property string $language
 */
class SettingForm extends CFormModel implements ISavableModel {

    const PROP_LANGUAGE = 'language';

    /** @var array  */
    private $props = [];

    /**
     * @param string $value
     */
    public function setLanguage($value) {
        $this->setProp(self::PROP_LANGUAGE, $value);
    }

    /**
     * @return string
     */
    public function getLanguage() {
        if (! $this->hasProp(self::PROP_LANGUAGE)) {
            $lang = O::app()->getLanguage();
            $this->setProp(self::PROP_LANGUAGE, $lang, false);
            return $lang;
        }
        return $this->getProp(self::PROP_LANGUAGE);
    }

    /**
     * validate language selection
     */
    public function validateLanguage() {
        if (!isset(OrganizzyApplication::$supportedLocale[$this->language])) {
            $this->addError(self::PROP_LANGUAGE, _t('Language not supported'));
        }
    }

    /**
     * @return bool
     */
    private function setupLanguage() {
        setcookie('l', $this->getLanguage(), time() + 31536000, '/');
        return true;
    }


    /**
     * @param string $name property name
     * @param string $value property value
     * @param bool $changed
     */
    private function  setProp($name, $value, $changed = true) {
        if (!isset($this->props[$name]) || $this->props[$name][0] != $value) {
            $this->props[$name] = [$value, $changed];
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private function getProp($name, $default = null) {
        if (isset($this->props[$name])) {
            return $this->props[$name][0];
        }
        return $default;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function propChanged($name) {
        return isset($this->props[$name]) && $this->props[$name][1];
    }

    /**
     * @param string $name
     * @return bool
     */
    private function hasProp($name) {
        return isset($this->props[$name]);
    }


    /**
     * @return boolean
     */
    public function save()
    {
        $result = true;
        if ($this->propChanged(self::PROP_LANGUAGE))
            $result = $this->setupLanguage() && $result;

        return $result;
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            ['language', 'validateLanguage'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return [
            self::PROP_LANGUAGE => _t('Language'),
        ];
    }

}
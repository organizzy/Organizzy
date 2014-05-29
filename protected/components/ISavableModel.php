<?php
/**
 * Organizzy
 * copyright (c) 2014 abie
 *
 * @author abie
 * @date 5/29/14 7:32 PM
 */

interface ISavableModel {

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes);

    /**
     * @return boolean
     */
    public function validate();

    /**
     * @return boolean
     */
    public function save();
} 
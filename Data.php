<?php
/**
 * Created by PhpStorm.
 * User: alfredo.galiana
 * Date: 26/9/18
 * Time: 17:05
 */

class Data
{
    public $templateFile;
    public $templateData;
    public $extraParams=[];
    public $pivotalParam;

    public function __construct($templateFile, $extraParams)
    {
        $this->setTemplateData($templateFile);
        $this->buildArrayParams($extraParams);
        $this->setPivotalParam();
    }


    public function setTemplateData($templateFile)
    {
        $this->templateFile = __DIR__ . '/templates/' . $templateFile;

        if (!is_file($this->templateFile)) {
            self::error(' Data template file: ' . $this->templateFile . ' is not a file.' );
        }

        $this->templateData = file_get_contents($this->templateFile);
    }

    /*
     *  formats:
     *      fieldName@range:1..100
     *      fieldName@options:optionA,optionB,optionC
     *      fieldName:fixedValue
     */
    public function buildArrayParams($extraParams)
    {
        foreach ($extraParams as $extraParam) {

            if (preg_match("/^([^@]+)@range:([0-9]+)\.\.([0-9]+)$/", $extraParam, $matches)) {
                $fieldName = $matches[1];
                $rangeInit = $matches[2];
                $rangeEnd  = $matches[3];

                $this->extraParams[$fieldName] = range($rangeInit, $rangeEnd);
            } else if (preg_match("/^([^@]+)@options:(.*)$/", $extraParam, $matches)) {
                $fieldName = $matches[1];
                $this->extraParams[$fieldName] = explode(',', $matches[2]);
            } else {
                list($fieldName, $value) = explode(':', $extraParam);
                $this->extraParams[$fieldName] = [$value];
            }
        }
    }

    public function setPivotalParam()
    {
        $max = 0;
        $pivotalParam = false;

        foreach ($this->extraParams as $field=>$values) {
            $nValues = count($values);
            if ($nValues > $max) {
                $max = $nValues;
                $pivotalParam = $field;
            }
        }

        $this->pivotalParam = $pivotalParam;
    }

    public function next()
    {
        foreach ($this->extraParams[$this->pivotalParam] as $index=>$pivotalParamValue) {
            $data = str_replace('%' . $this->pivotalParam . '%', $pivotalParamValue, $this->templateData);
            foreach ($this->extraParams as $field => $values) {
                if ($field != $this->pivotalParam) {
                    $index = $index % count($values);
                    $data = str_replace('%' . $field . '%', $values[$index], $data);
                }
            }

            yield $data;
        }
    }

    public static function error($msg)
    {
        echo "\n [ERROR][DATA] " . $msg;
        exit(1);
    }
}
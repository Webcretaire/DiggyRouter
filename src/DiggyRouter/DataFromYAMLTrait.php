<?php
namespace DiggyRouter;

/**
 * Trait DataFromYAMLTrait
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter
 */
trait DataFromYAMLTrait
{
    /**
     * @param string $attribute
     * @param array $dataArray
     * @param string $key
     * @return bool
     */
    private function loadAttribute($attribute, $dataArray, $key = null)
    {
        if(is_null($key))
        {
            $key = $attribute;
        }

        if($this->checkAtribute($dataArray, $key))
        {
            $this->$attribute = $dataArray[$key];

            return true;
        }

        return false;
    }

    /**
     * @param array $dataArray
     * @param string $key
     * @return bool
     */
    private function checkAtribute($dataArray, $key)
    {
        return isset($dataArray[$key]) && !is_null($dataArray[$key]);
    }
}
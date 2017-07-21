<?php
namespace DiggyRouter;

trait DataFromYAMLTrait
{
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

    private function checkAtribute($dataArray, $key)
    {
        return isset($dataArray[$key]) && !is_null($dataArray[$key]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: j.emmanuel
 * Date: 21/07/2017
 * Time: 11:21
 */

namespace DiggyRouter;

/**
 * Class Action
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter
 */
class Action
{
    private $function;
    private $parameters;

    use DataFromYAMLTrait;

    public function __construct($actionData)
    {
        $this->loadAttribute('function', $actionData);
        $this->loadAttribute('parameters', $actionData);
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }


}
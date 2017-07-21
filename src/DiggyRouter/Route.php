<?php
namespace DiggyRouter;

/**
 * Class Route
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter
 */
class Route
{
    private $uri;
    private $controller;
    private $action;

    use DataFromYAMLTrait;

    public function __construct($routeData)
    {
        $this->loadAttribute('uri', $routeData);
        $this->loadAttribute('controller', $routeData);
        if($this->checkAtribute($routeData, 'action'))
        {
            $this->action = new Action($routeData['action']);
        }
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
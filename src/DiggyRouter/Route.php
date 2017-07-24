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
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    use DataFromYAMLTrait;

    /**
     * Route constructor.
     * @param array $routeData
     */
    public function __construct($routeData)
    {
        $this->loadAttribute('uri', $routeData);
        $this->loadAttribute('controller', $routeData);
        $this->loadAttribute('action', $routeData);
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
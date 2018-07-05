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
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private $params = [];

    use DataFromYAMLTrait;

    /**
     * Route constructor.
     * @param array $routeData
     */
    public function __construct(array $routeData)
    {
        $this->loadAttribute('controller', $routeData);
        $this->loadAttribute('action', $routeData);
        $this->loadAttribute('params', $routeData);
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array|null
     */
    public function getParams()
    {
        return $this->params;
    }
}
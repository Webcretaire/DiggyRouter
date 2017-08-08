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

    use DataFromYAMLTrait;

    /**
     * Route constructor.
     * @param array $routeData
     */
    public function __construct(array $routeData)
    {
        $this->loadAttribute('controller', $routeData);
        $this->loadAttribute('action', $routeData);
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
    public function getAction(): ?string
    {
        return $this->action;
    }
}
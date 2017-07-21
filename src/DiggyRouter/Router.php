<?php
namespace DiggyRouter;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Router
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter
 */
class Router
{
    private $routingFile;
    private $routingData;
    private $defaultValues = ['action' => 'render'];

    /**
     * @param string $routingFile
     */
    public function loadRoutes($routingFile)
    {
        $this->routingFile = $routingFile;
        $this->routingData = Yaml::parse(file_get_contents($routingFile));
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function matchRequest($uri)
    {
        $key = array_search($uri, array_column($this->routingData['routes'], 'uri'));
        if($key !== false)
        {
            $route = new Route($this->routingData['routes'][$key]);
            $this->useRoute($route);
            return true;
        }
        return false;
    }

    /**
     * @param Route $route
     */
    private function useRoute($route)
    {
        $controller = new ($route->getController())();
        $action = $route->getAction();
        $toPerform = !is_null($action) ? $action->getAction() : $this->defaultValues['action'];

        if(!is_null($action)) {
            $controller->$toPerform();
        }
    }
}
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
     * @return bool
     */
    public function handleRequest($uri = null)
    {
        if(is_null($uri))
        {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $cleanURI = $this->removeURIParams($uri);

        return $this->matchURI($cleanURI);
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function matchURI($uri)
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
        $toCreate = $route->getController();
        $controller = new $toCreate();
        $toPerform = !is_null($route->getAction()) ? $route->getAction()->getFunction() : $this->defaultValues['action'];
        $controller->$toPerform();
    }

    /**
     * @param string $uri
     * @return string
     */
    public function removeURIParams($uri)
    {
        if(!strpos($uri, '?')) {
            return $uri;
        }
        return strstr ( $uri , '?', true);
    }
}
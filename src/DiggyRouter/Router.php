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
    /**
     * @var string
     */
    private $routingFile;

    /**
     * @var array
     */
    private $routingData;

    /**
     * @var array
     */
    private $defaultValues = ['action' => 'render'];

    /**
     * @var string
     */
    private $delimiter = '~';

    /**
     * @param string $routingFile
     */
    public function loadRoutes(string $routingFile)
    {
        $this->routingFile = $routingFile;
        $this->routingData = Yaml::parse(file_get_contents($routingFile));
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function handleRequest(string $uri = null): bool
    {
        if (is_null($uri)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $cleanURI = $this->removeURIParams($uri);

        return $this->matchURI($cleanURI);
    }

    /**
     * @param string $uri
     * @return bool
     * @throws InvalidURIException
     */
    public function matchURI(string $uri): bool
    {
        foreach ($this->routingData['routes'] as $key => $path) {
            if ($route = $this->checkSinglePath($this->routingData['routes'][$key], $uri)) {
                $this->useRoute($route);
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $currentPath
     * @param $uri
     * @return bool|Route
     * @throws InvalidURIException
     */
    private function checkSinglePath(array $currentPath, string $uri)
    {
        if (substr($currentPath['uri'], 0, 1) == $this->delimiter) // 'uri' is an expression
        {
            // Check if expression syntax is valid :
            if (substr($currentPath['uri'], strlen($currentPath['uri']) - 1, 1) != $this->delimiter) {
                throw new InvalidURIException('Expression that starts with ~ must also end with ~', 1001);
            }
            $expression = $currentPath['uri'];
        } else // 'uri' is directly the requested URI
        {
            $expression = $this->delimiter . '^' . $currentPath['uri'] . '$' . $this->delimiter;
        }

        if (preg_match($expression, $uri)) {
            return new Route($currentPath);
        }

        return false;
    }

    /**
     * @param Route $route
     */
    private function useRoute(Route $route)
    {
        $toCreate = $route->getController();
        $controller = new $toCreate();
        $toPerform = !is_null($route->getAction()) ? $route->getAction() : $this->defaultValues['action'];
        $controller->$toPerform();
    }

    /**
     * @param string $uri
     * @return string
     */
    public function removeURIParams(string $uri): string
    {
        if (!strpos($uri, '?')) {
            return $uri;
        }

        return strstr($uri, '?', true);
    }

    /**
     * @param string $action
     */
    public function setDefaultAction(string $action)
    {
        $this->defaultValues['action'] = $action;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }
}
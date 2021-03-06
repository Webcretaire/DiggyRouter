<?php

namespace DiggyRouter;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Router
 *
 * @author  Julien EMMANUEL <JuEm0406@gmail.com>
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
        $this->routingData = $this->handleIncludes($this->routingData, $this->routingFile);
    }

    /**
     * @param array $routingData
     * @param string $routingFile
     * @return array
     */
    private function handleIncludes(array $routingData, string $routingFile)
    {
        if (isset($routingData['includes'])) {
            foreach ($routingData['includes'] as $includedFile) {
                if (substr($includedFile, 0, 1) === '/') // Absolute path
                {
                    $newRoutingFile = $includedFile;
                } else // Relative path
                {
                    $newRoutingFile = dirname($routingFile) . '/' . $includedFile;
                }

                $newData = Yaml::parse(file_get_contents($newRoutingFile));
                $newData = $this->handleIncludes($newData, $newRoutingFile);

                if (!isset($routingData['routes'])) {
                    $routingData['routes'] = [];
                }

                $routingData['routes'] = array_merge($routingData['routes'], $newData['routes']);
            }

            unset($routingData['includes']);
        }

        return $routingData;
    }

    /**
     * @param string $uri
     * @return bool
     * @throws InvalidURIException
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
        foreach ($this->routingData['routes'] as $path) {
            if (is_array($path['uri'])) {
                foreach ($path['uri'] as $currentURI) {
                    if ($this->isCorrectPath($currentURI, $uri)) {
                        $this->useRoute(new Route($path));
                        return true;
                    }
                }
            } else {
                if ($this->isCorrectPath($path['uri'], $uri)) {
                    $this->useRoute(new Route($path));
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $currentURI
     * @param string $uri
     * @return bool
     * @throws InvalidURIException
     */
    private function isCorrectPath(string $currentURI, string $uri): bool
    {
        if ($this->isExpressionURI($currentURI))
            $expression = $currentURI;
        else // currentURI is directly the requested URI
            $expression = $this->delimiter . '^' . $currentURI . '$' . $this->delimiter;

        if (preg_match($expression, $uri)) {
            return true;
        }

        return false;
    }

    /**
     * @param Route $route
     */
    private function useRoute(Route $route)
    {
        $toCreate        = $route->getController();
        $controller      = new $toCreate();
        $toPerform       = !is_null($route->getAction()) ? $route->getAction() : $this->defaultValues['action'];
        $effectiveParams = [];
        foreach ($route->getParams() as $param)
            $effectiveParams[] = isset($_REQUEST[$param]) ? $_REQUEST[$param] : null;
        call_user_func_array([$controller, $toPerform], $effectiveParams);
    }

    /**
     * @param string|array $uri
     * @return string|array
     */
    public function removeURIParams($uri)
    {
        if (is_array($uri)) {
            $cleanUri = [];
            foreach ($uri as $key => $singleUri) {
                $cleanUri[$key] = $this->removeURIParams($singleUri);
            }

            return $cleanUri;
        }

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

    /**
     * @param string $uri
     * @return bool
     * @throws InvalidURIException
     */
    public function isExpressionURI($uri)
    {
        if (substr($uri, 0, 1) == $this->delimiter) // $currentURI is an expression
        {
            // Check if expression syntax is valid :
            if (substr($uri, strlen($uri) - 1, 1) != $this->delimiter) {
                throw new InvalidURIException('Expression that starts with ' . $this->delimiter . ' must also end with ' . $this->delimiter, 1001);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @throws NotFoundException
     * @throws UnhandledException
     */
    public function path($name)
    {
        echo $this->getPath($name);
    }

    /**
     * @param $name
     * @return bool|string
     * @throws NotFoundException
     * @throws UnhandledException
     */
    public function getPath($name)
    {
        foreach ($this->routingData['routes'] as $path) {
            if (isset($path['name']) && $name == $path['name']) {
                if ($uri = $this->getNonRegexURI($path)) {
                    return $uri;
                }
                throw new UnhandledException("Can't generate Regex URIs yet", 1003);
            }
        }

        throw new NotFoundException("No URI with this name", 1002);
    }

    /**
     * @param array $route
     * @return bool|string
     * @throws InvalidURIException
     */
    private function getNonRegexURI(array $route)
    {
        if (is_array($route['uri'])) {
            foreach ($route['uri'] as $uri) {
                if (!$this->isExpressionURI($uri)) {
                    return $uri;
                }
            }
        } else {
            if (!$this->isExpressionURI($route['uri'])) {
                return $route['uri'];
            }
        }

        return false;
    }
}
<?php

namespace DiggyRouter\Tests;

use DiggyRouter\InvalidURIException;
use DiggyRouter\NotFoundException;
use DiggyRouter\Router;
use DiggyRouter\UnhandledException;
use PHPUnit\Framework\TestCase;

/**
 * Class RouterTest
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter\Tests
 */
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        parent::setUp();

        $this->router = new Router();
    }

    /**
     * @covers Router::loadRoutes
     */
    public function testLoadRoutes()
    {
        $this->router->loadRoutes(__DIR__ . "/Resources/routing.yml");
        $this->assertAttributeEquals(__DIR__ . "/Resources/routing.yml", 'routingFile', $this->router);
        $this->assertAttributeEquals([
            'routes' => [
                [
                    'name' => 'homepage',
                    'uri' => '/',
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                ],
                [
                    'uri' => '/customAction',
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                    'action' => 'customAction'
                ],
                [
                    'name' => 'expression_uri',
                    'uri' => '~^/expression-([0-9]+)-~',
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                    'action' => 'expression'
                ],
                [
                    'name' => 'multiple_uri',
                    'uri' => [
                        '/multipleURI1',
                        '/multipleURI2',
                    ],
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                    'action' => 'multipleURI'
                ],
                [
                    'uri' => '/otherRouting',
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                ],
                [
                    'uri' => '/otherRouting2',
                    'controller' => 'DiggyRouter\Tests\Resources\DummyClass',
                ],
            ]
        ], 'routingData', $this->router);
    }

    /**
     * @covers Router::isExpressionURI
     */
    public function testIsExpressionURI()
    {
        $exceptionThrown = false;
        try {
            $this->router->isExpressionURI('~expression');
        } catch (InvalidURIException $ex) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
        $this->assertTrue($this->router->isExpressionURI('~expression~'));
        $this->assertFalse($this->router->isExpressionURI('nonExpression'));
    }

    /**
     * @covers Router::handleRequest
     */
    public function testHandleRequest()
    {
        $this->router->loadRoutes(__DIR__ . "/Resources/routing.yml");

        $this->assertEquals('Rendering', $this->useURI('/'));
        $this->assertEquals('CustomActing', $this->useURI('/customAction'));
        $this->assertEquals('Expressing', $this->useURI('/expression-12-test'));
        $this->assertEquals('MultipleURIing', $this->useURI('/multipleURI1'));
        $this->assertEquals('MultipleURIing', $this->useURI('/multipleURI2'));

        $this->assertFalse($this->router->handleRequest('/fakeURI'));
    }

    /**
     * @covers Router::getPath
     */
    public function testGetPath()
    {
        $this->router->loadRoutes(__DIR__ . "/Resources/routing.yml");

        $this->assertEquals('/', $this->router->getPath('homepage'));
        $this->assertEquals('/multipleURI1', $this->router->getPath('multiple_uri'));
        try {
            $this->router->getPath('fake_name');
            $this->assertEquals(1, 2); // No exception -> problem
        } catch (NotFoundException $ex) {
            $this->assertEquals(1, 1); // Exception thrown -> good result
        }
        try {
            $this->router->getPath('expression_uri');
            $this->assertEquals(1, 2); // No exception -> problem
        } catch (UnhandledException $ex) {
            $this->assertEquals(1, 1); // Exception thrown -> good result
        }
    }

    /**
     * @param string $uri
     * @return string
     */
    private function useURI($uri)
    {
        ob_start();
        $this->router->handleRequest($uri);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}

<?php

namespace DiggyRouter\Tests\Resources;

/**
 * Class DummyClass
 *
 * @author Julien EMMANUEL <JuEm0406@gmail.com>
 * @package DiggyRouter\Tests\Resources
 */
class DummyClass
{
    public function customAction()
    {
        echo "CustomActing";
    }

    public function expression()
    {
        echo "Expressing";
    }

    public function multipleURI()
    {
        echo "MultipleURIing";
    }

    public function render()
    {
        echo "Rendering";
    }
}
<?php

class Element
{
    public string $name;
    public array $beats;

    public function __construct(string $name, array $beats)
    {
        $this->name = $name;
        $this->beats = $beats;
    }
}
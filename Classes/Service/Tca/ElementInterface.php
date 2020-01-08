<?php
declare(strict_types = 1);
namespace Supseven\Confuse\Service\Tca;

interface ElementInterface
{
    // used methods in the element abstract class
    public function __construct();
    public function build();
    public function getYamlConfig();
    public function getElement();
    public function checkConfig($key, $value);
    public function getElementConfig();
    public function setName($name);

    // used methods in the elements abstraction
    public static function create();

    // must have default methods. maybe overwritten in the
    // elements abstraction
    public function setValue($value);
    public function setRenderType(string $renderType);
    public function setDefault($default);
    public function setEval(array $eval);
    public function setReadOnly(bool $readOnly);
}

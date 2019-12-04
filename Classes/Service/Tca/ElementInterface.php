<?php
declare(strict_types = 1);
namespace Supseven\Confuse\Service\Tca;

interface ElementInterface
{
    public static function create();
    public function build();
    public function setValue($value);
    public function setRenderType(string $renderType);
    public function setDefault($default);
    public function setEval(array $eval);
    public function setReadOnly(bool $readOnly);
}

<?php
declare(strict_types = 1);
namespace Supseven\Confuse\Service\Tca;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Create
{
    /** @var array */
    private $elements = [];

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @param array $elements
     * @return $this
     * @throws \Exception
     */
    public function addElement(array $elements): self
    {
        foreach ($elements as $element) {
            if (!$element instanceof Element) {
                throw new \Exception('Added Element is not of Type ' . Element::class, 1575378454);
            }

            $this->elements[array_keys($element->getElement())[0]] = array_values($element->getElement())[0];
        }

        return $this;
    }

    /**
     * @param $table string
     */
    public function persist($table): void
    {
        ExtensionManagementUtility::addTCAcolumns($table, $this->elements);
    }
}

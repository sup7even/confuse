<?php
declare(strict_types = 1);
namespace Supseven\Confuse\Service\Tca\Elements;

use Supseven\Confuse\Service\Tca\Element;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Check extends Element
{
    /** @var string rendertype setting */
    public const RENDER_checkboxToggle = 'checkboxToggle';

    /** @var string rendertype setting */
    public const RENDER_checkboxLabeledToggle = 'checkboxLabeledToggle';

    /** @var string eval method */
    public const EVAL_maximumRecordsChecked = 'maximumRecordsChecked';

    /** @var string eval method */
    public const EVAL_maximumRecordsCheckedInPid = 'maximumRecordsCheckedInPid';

    /**
     * @return static
     */
    public static function create(): self
    {
        /** @var Check $element */
        $element = GeneralUtility::makeInstance(self::class);
        $element->type = $element->getYamlConfig()['check']['config']['type'];

        return $element;
    }

    /**
     * @return Element
     * @throws \Exception
     */
    public function build(): Element
    {
        $this->element[$this->name] = [];
        $this->customElementConfig = [];

        return parent::build();
    }

    /**
     * @param $items
     * @return $this
     * @throws \Exception
     */
    public function setValue($items): self
    {
        if ($this->checkConfig('items', $items)) {
            $this->elementConfig['items'] = $this->setItemsForValue($items);
        } else {
            throw new \Exception('Value for Checkbox is not an Array or an empty Array', 1575378425);
        }

        return $this;
    }

    /**
     * @param $cols
     * @return $this
     */
    public function setCols($cols): self
    {
        if ($this->checkConfig('cols', $cols)) {
            $this->elementConfig['cols'] = $cols;
        }

        return $this;
    }

    /**
     * @param $renderType
     * @return $this
     */
    public function setRenderType($renderType): self
    {
        if (in_array($renderType, $this->getYamlConfig()[$this->type]['config']['renderType'])) {
            $this->elementConfig['renderType'] = $renderType;
        }

        return $this;
    }

    /**
     * @param array $eval
     * @return Element
     */
    public function setEval(array $eval): Element
    {
        $evalMethods = [];

        foreach ($eval as $method => $value) {
            if (in_array($method, $this->getYamlConfig()[$this->type]['config']['eval'])) {
                $evalMethods[] = $method;
                $this->elementConfig['validation'][$method] = $value;
            }
        }

        $this->elementConfig['eval'] = implode(', ', $evalMethods);

        return $this;
    }

    /**
     * @param $itemsProcFunc string the class name for the procFunc
     * @return $this
     */
    public function setItemsProcFunc($itemsProcFunc): self
    {
        if ($this->checkConfig('itemsProcFunc', $itemsProcFunc)) {
            $processedMethod = explode('->', $itemsProcFunc);

            if (method_exists($processedMethod[0], $processedMethod[1])) {
                $this->elementConfig['itemsProcFunc'] = $itemsProcFunc;
            }
        }

        return $this;
    }

    /**
     * returns the items in a correct key/value pair as TYPO3 expects it
     *
     * @param $items
     * @return array
     */
    private function setItemsForValue($items): array
    {
        foreach($items as &$item) {
            if (!is_array($item)) {
                $item = [
                    0 => $item,
                    1 => $item
                ];
            }

            if (count($item) !== 2) {
                $item = [
                    0 => $item[0],
                    1 => array_pop($item),
                ];
            }

        }

        return $items;
    }
}

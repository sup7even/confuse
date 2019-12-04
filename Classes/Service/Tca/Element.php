<?php
declare(strict_types = 1);
namespace Supseven\Confuse\Service\Tca;

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

abstract class Element implements ElementInterface
{
    private const YAML_FILE = 'EXT:confuse/Configuration/Yaml/Config.yaml';

    protected $element = [];

    /** @var string the element type */
    protected $type;

    /** @var string the element name */
    protected $name;

    /** @var bool the element exclude setting */
    protected $exclude;

    /** @var string the element label */
    protected $label;

    /** @var mixed the element default */
    protected $default;

    /** @var string the elements validation methods */
    protected $eval;

    /** @var array the element config */
    protected $elementConfig = [];

    /** @var array a possible custom array config */
    protected $customElementConfig = [];

    /** @var YamlFileLoader */
    private $yamlFileloader;

    /** @var array */
    private $yamlConfig;

    /**
     * Element constructor.
     */
    public function __construct()
    {
        $this->yamlFileloader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $this->yamlConfig = $this->getYamlConfig();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        if (!$this->element) {
            throw new \Exception('No Build method in Element Class ' . ucfirst($this->type));
        }

        ArrayUtility::mergeRecursiveWithOverrule($this->element, [
            $this->name => [
                'exclude' => $this->exclude,
                'label'   => $this->label ?: LocalizationUtility::translate($this->type . '.label', 'confuse'),
                'config'  => $this->getElementConfig(),
            ]
        ]);

        // possible custom overrides
        ArrayUtility::mergeRecursiveWithOverrule($this->element[$this->name], $this->customElementConfig);

        return $this;
    }

    /**
     * @return array
     */
    public function getElement(): array
    {
        return $this->element;
    }

    /**
     * @param $name string The Element Name
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $exclude bool the Element Exclude Setting
     * @return $this
     */
    public function setExclude($exclude): self
    {
        if ($this->checkConfig('exclude', $exclude)) {
            $this->exclude = $exclude;
        }

        return $this;
    }

    /**
     * @param $label string the Element Label
     * @return $this
     */
    public function setLabel($label): self
    {
        if ($this->checkConfig('label', $label)) {
            $this->label = $label;
        }

        return $this;
    }

    public function setDefault($default): self
    {
        if ($this->checkConfig('default', $default)) {
            $this->default = $default;
        }

        return $this;
    }

    public function setEval(array $eval): self
    {
        $evalMethods = [];

        foreach ($eval as $value) {
            if (in_array($value, $this->getYamlConfig()[$this->type]['config']['eval'])) {
                $evalMethods[] = $value;
            }
        }

        $this->elementConfig['eval'] = implode(', ', $evalMethods);

        return $this;
    }

    public function setReadOnly(bool $readOnly): self
    {
        if ($this->checkConfig('readOnly', $readOnly)) {
            $this->elementConfig['readOnly'] = $readOnly;
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getYamlConfig(): array
    {
        return $this->yamlFileloader->load(self::YAML_FILE)['TCA']['Types'];
    }

    /**
     * @param $key string The Config Keyname
     * @param $type string The TypeCast Type
     * @param $value string the Value to TypeCast
     * @return bool
     */
    protected function checkConfig($key, $value): bool
    {
        if (isset($this->yamlConfig[$this->type][$key]) && $this->yamlConfig[$this->type][$key] === gettype($value)) {
            return true;
        }

        if (isset($this->yamlConfig[$this->type]['config'][$key]) && $this->yamlConfig[$this->type]['config'][$key] === gettype($value)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getElementConfig()
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->elementConfig, [
            'type'    => $this->type,
            'default' => $this->default,
        ]);

        // remove nullable entries
        $this->elementConfig = array_filter($this->elementConfig, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return $this->elementConfig;
    }
}

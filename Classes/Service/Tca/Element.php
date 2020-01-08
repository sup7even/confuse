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

    /** @var array the default settings based on the yaml config file */
    protected $defaultSettings = [];

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
     * @return array
     */
    public function getYamlConfig(): array
    {
        return $this->yamlFileloader->load(self::YAML_FILE)['TCA']['Types'];
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

        // load default settings from yaml file with magic method __call()
        ArrayUtility::mergeRecursiveWithOverrule($this->element, [
            $this->name => $this->defaultSettings
        ]);

        // merge settings from abstraction
        ArrayUtility::mergeRecursiveWithOverrule($this->element, [
            $this->name => [
                'config'  => $this->getElementConfig(),
            ]
        ]);

        // merge custom settings from abstraction
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
     * @param $key string The Config Keyname
     * @param $type string The TypeCast Type
     * @param $value string the Value to TypeCast
     * @return bool
     */
    public function checkConfig($key, $value): bool
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
    public function getElementConfig()
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
     * the magic method to call stuff based on the yaml config file
     *
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $defaultKey = strtolower(str_replace('set', '', $method));

        if (array_key_exists($defaultKey, $this->yamlConfig['default'])) {
            $value = array_shift($arguments);
            $type = $this->yamlConfig['default'][$defaultKey];

            if (gettype($value) === $type) {
                $this->defaultSettings[$defaultKey] = $value;
            }
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function setValue($value)
    {
        if ($this->checkConfig('value', $value)) {
            $this->elementConfig['value'] = $value;
        } else {
            throw new \Exception('Value for Element is not as described in the YAML Config', 1578487613);
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
}

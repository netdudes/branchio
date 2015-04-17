<?php
namespace Netdudes\Branchio;

use Symfony\Component\Yaml\Parser;

class Configuration
{

    protected $configuration;

    /**
     * @param $yamlConfigurationFile
     */
    public function __construct($yamlConfigurationFile)
    {
        $parser = new Parser();
        $this->configuration = array_map(
            [$this, 'replacePlaceholders'],
            $parser->parse(file_get_contents($yamlConfigurationFile))['parameters']
        );
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key) {
        return $this->configuration[$key];
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function replacePlaceholders($value) {
        $placeholders = [
            '{base_directory}' => __DIR__ . '/..'
        ];

        foreach ($placeholders as $placeholder => $replacement) {
            $value = str_replace($placeholder, $replacement, $value);
        }

        return $value;
    }
}
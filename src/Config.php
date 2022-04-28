<?php

namespace nkoporec\Pmd;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Config class.
 */
class Config
{
    /**
     * The config file name.
     */
    public const CONFIG_FILE = "pmd.yaml";

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $dir = $this->getConfigDir();
        $file = $dir . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

        if (! file_exists($file)) {
            return [
                'url' => 'localhost',
                'port' => '8080',
                'type' => 'php',
            ];
        }

        try {
            $config = Yaml::parse(file_get_contents($file));

            return [
                'url' => $config['url'],
                'port' => $config['port'],
                'type' => $config['type'] ?: 'php',
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error parsing config file: " . $file);
        }
    }

    /**
     * Get the config directory, where the config file is located.
     *
     * @return string
     */
    public function getConfigDir(): string
    {
        $reflection = new \ReflectionClass(ClassLoader::class);
        $vendorDir = dirname(dirname($reflection->getFileName()));

        if (! $vendorDir) {
            throw new \Exception("Could not find vendor dir");
        }

        return str_replace("/vendor", "", $vendorDir);
    }
}

<?php

namespace nkoporec\Pmd;

use Symfony\Component\Yaml\Yaml;
use Composer\Autoload\ClassLoader;

class Config
{
    public const CONFIG_FILE = "pmd.yaml";

    public function getConfig()
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

    public function getConfigDir()
    {
        $reflection = new \ReflectionClass(ClassLoader::class);
        $vendorDir = dirname(dirname($reflection->getFileName()));
        return str_replace("/vendor", "", $vendorDir);
    }
}

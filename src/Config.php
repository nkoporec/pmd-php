<?php

namespace nkoporec\Pmd;

use Symfony\Component\Yaml\Yaml;

class Config {

    const CONFIG_FILE = "pmd.yaml";

    public function getConfig() {
        $dir = $this->getConfigDir();
        $file = $dir . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

        if (!file_exists($file)) {
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
                'type' => $config['user'] ?: 'php',
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error parsing config file: " . $file);
        }
    }

    public function getConfigDir() {
        return dirname(__FILE__);
    }

}

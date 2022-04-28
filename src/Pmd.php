<?php

namespace nkoporec\Pmd;

class Pmd
{
    public const URL = "localhost";
    public const PORT = "8080";
    public const TYPE = "php";

    public function send(...$args) {
        $this->curl($args);
    }

    protected function curl($payload)
    {
        $config = new Config();
        $config = $config->getConfig();
        try {
            $ch = curl_init($config['url'] . ':' . $config['port'] . '/api/dump');

            $data = [
                'payload' => json_encode($payload),
                'file' => __FILE__,
                'type' => $config['type'],
                'timestamp' => (string) time(),
            ];

            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
        } finally {
            curl_close($ch);
        }
    }
}

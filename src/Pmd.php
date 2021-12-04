<?php

namespace nkoporec\Pmd;

class Pmd
{
    public const URL = "localhost";
    public const PORT = "8080";
    public const TYPE = "php";

    public function send(...$args)
    {
        $this->curl($args);
    }

    protected function curl($payload)
    {
        try {
            $ch = curl_init(self::URL . ':' . self::PORT);

            $data = [
                'payload' => $payload,
            'file' => __FILE__,
                'type' => self::TYPE,
                'timestamp' => time(),
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

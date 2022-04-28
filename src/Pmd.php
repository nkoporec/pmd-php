<?php

namespace nkoporec\Pmd;

class Pmd
{
    public function send(...$args)
    {
        $this->curl($args);
    }

    protected function curl($payload)
    {
        $config = new Config();
        $config = $config->getConfig();

        try {
            $ch = curl_init($config['url'] . ':' . $config['port'] . '/api/dump');
            $backfiles = debug_backtrace();
            // The first two calls are from the Pmd class.
            $backfiles = array_slice($backfiles, 2);
            $file = $backfiles[0]['file'];

            $data = [
                'payload' => json_encode($payload),
                'file' => $file,
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

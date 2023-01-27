<?php

namespace nkoporec\Pmd;

/**
 * Class Pmd
 */
class Pmd
{
    /**
     * Poor's man debugger.
     *
     * @param mixed $args
     *   The payload to send.
     *
     * @return string
     *  The response from PMD.
     */
    public function send(...$args): string
    {
        return $this->curl($args);
    }

    /**
     * Helper function for curl.
     *
     * @param mixed $payload
     *   The payload to send.
     *
     * @return string
     *   Curl response code.
     */
    protected function curl($payload): string
    {
        $config = new Config();
        $config = $config->getConfig();


        try {
            $ch = curl_init($config['url'] . ':' . $config['port'] . '/dump');
            $backfiles = debug_backtrace();
            // The first two calls are from the Pmd class.
            $backfiles = array_slice($backfiles, 2);

            $file = $backfiles[0]['file'];
            $line = $backfiles[0]['line'];

            $callstack = [];

            // Send the full callstack.
            foreach ($backfiles as $call) {
                $callstack[] = [
                    'filepath' => (string) $call['file'] . "::" . (string) $call['function'] . '()',
                    'line' => (string) $call['line'],
                ];
            }

            $data = [
                'payload' => json_encode($payload, JSON_FORCE_OBJECT),
                'filepath' => (string) $file,
                'callstack' => $callstack,
                'line' => (string) $line,
                'connector_type' => (string) $config['type'],
                'timestamp' => (string) time(),
            ];

            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
        } finally {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return (string) $code;
        }
    }
}

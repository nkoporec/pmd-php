<?php

namespace nkoporec\Pmd;

use ReflectionClass;

class Pmd
{
    /**
     * Send debug payload to CLI tool.
     *
     * @param mixed ...$args
     * @return string
     */
    public function send(...$args): string
    {
        $namedPayload = [];

        foreach ($args as $i => $arg) {
            $varName = '$var' . $i; // fallback name
            $namedPayload[$varName] = [
                'type' => $this->typeOf($arg),
                'value' => $this->normalize($arg),
            ];
        }

        return $this->curl($namedPayload);
    }

    /**
     * Perform CURL request to the backend server.
     *
     * @param array $payload
     * @return string
     */
    protected function curl(array $payload): string
    {
        $config = (new Config())->getConfig();

        try {
            $ch = curl_init($config['url'] . ':' . $config['port'] . '/dump');

            $backtrace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2);
            $file = $backtrace[0]['file'] ?? 'unknown';
            $line = $backtrace[0]['line'] ?? '0';

            $callstack = array_map(function ($call) {
                return [
                    'filepath' => ($call['file'] ?? '[internal]') . '::' . ($call['function'] ?? '[unknown]') . '()',
                    'line' => (string) ($call['line'] ?? '0'),
                ];
            }, $backtrace);

            // No double-encoding!
            $data = [
                'payload' => $payload, // raw array
                'filepath' => (string) $file,
                'callstack' => $callstack,
                'line' => (string) $line,
                'connector_type' => (string) $config['type'],
                'timestamp' => (string) time(),
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
        } finally {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return (string) $code;
        }
    }

    /**
     * Normalize a PHP variable to a JSON-safe structure.
     *
     * @param mixed $data
     * @param int $depth
     * @return mixed
     */
    protected function normalize($data, int $depth = 0)
    {
        if ($depth > 20) {
            return '[Max depth reached]';
        }

        if (is_scalar($data) || $data === null) {
            return $data;
        }

        if (is_array($data)) {
            return array_map(fn ($item) => $this->normalize($item, $depth + 1), $data);
        }

        if (is_object($data)) {
            $result = ['__class' => get_class($data)];
            $reflection = new ReflectionClass($data);

            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);

                try {
                    $result[$property->getName()] = $this->normalize($property->getValue($data), $depth + 1);
                } catch (\Throwable $e) {
                    $result[$property->getName()] = '[unreadable]';
                }
            }

            return $result;
        }

        return '[unsupported type]';
    }

    /**
     * Get simplified type of a value for debug metadata.
     *
     * @param mixed $value
     * @return string
     */
    protected function typeOf($value): string
    {
        if (is_object($value)) {
            return 'Object';
        }

        if (is_array($value)) {
            return 'Array';
        }

        if (is_string($value)) {
            return 'String';
        }

        if (is_int($value)) {
            return 'Integer';
        }

        if (is_float($value)) {
            return 'Float';
        }

        if (is_bool($value)) {
            return 'Boolean';
        }

        if (is_null($value)) {
            return 'Null';
        }

        return 'Unknown';
    }
}

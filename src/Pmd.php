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
            $varName = '$var' . $i;
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
            $ch = curl_init($config['url'] . '/dump');

            $backtrace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2);
            $file = $backtrace[0]['file'] ?? 'unknown';
            $line = $backtrace[0]['line'] ?? '0';

            $callstack = array_map(function ($call) {
                return [
                    'filepath' => ($call['file'] ?? '[internal]') . '::' . ($call['function'] ?? '[unknown]') . '()',
                    'line' => (string) ($call['line'] ?? '0'),
                ];
            }, $backtrace);

            $data = [
                'payload' => $payload,
                'filepath' => (string) $file,
                'callstack' => $callstack,
                'line' => (string) $line,
                'connector_type' => (string) $config['type'],
                'timestamp' => (string) time(),
            ];

            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } finally {
            curl_close($ch);
        }

        return '200';
    }

    /**
     * Normalize any PHP value to a JSON-safe structure with UTF-8 encoding.
     */
    protected function normalize($data, int $depth = 0, array &$seen = [])
    {
        if ($depth > 10) {
            return '[Max depth reached]';
        }

        if (is_scalar($data) || $data === null) {
            return is_string($data)
                ? mb_convert_encoding($data, 'UTF-8', 'UTF-8')
                : $data;
        }

        if (is_resource($data)) {
            return '[resource]';
        }

        if (is_callable($data)) {
            return '[callable]';
        }

        if (is_array($data)) {
            $normalized = [];
            foreach ($data as $key => $value) {
                $safeKey = is_string($key)
                    ? mb_convert_encoding($key, 'UTF-8', 'UTF-8')
                    : $key;

                $normalized[$safeKey] = $this->normalize($value, $depth + 1, $seen);
            }
            return $normalized;
        }

        if (is_object($data)) {
            $objectId = spl_object_id($data);
            if (isset($seen[$objectId])) {
                return '[circular reference]';
            }
            $seen[$objectId] = true;

            $result = ['__class' => get_class($data)];
            $reflection = new ReflectionClass($data);

            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                try {
                    $value = $property->getValue($data);
                    $result[$property->getName()] = $this->normalize($value, $depth + 1, $seen);
                } catch (\Throwable $e) {
                    $result[$property->getName()] = '[unreadable]';
                }
            }

            return $result;
        }

        return '[unsupported type]';
    }

    /**
     * Check for invalid UTF-8 in a deeply nested structure and return the path.
     */
    protected function findInvalidUtf8($data, string $path = ''): ?string
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $newPath = $path . "[$key]";
                $found = $this->findInvalidUtf8($value, $newPath);
                if ($found !== null) {
                    return $found;
                }
            }
        } elseif (is_string($data) && !mb_check_encoding($data, 'UTF-8')) {
            return "Invalid UTF-8 at path: $path";
        }
        return null;
    }

    /**
     * Get the simplified type name for the payload.
     */
    protected function typeOf($value): string
    {
        return match (true) {
            is_object($value) => 'Object',
            is_array($value) => 'Array',
            is_string($value) => 'String',
            is_int($value) => 'Integer',
            is_float($value) => 'Float',
            is_bool($value) => 'Boolean',
            is_null($value) => 'Null',
            default => 'Unknown',
        };
    }
}

<?php

namespace App\Formatters;

use Monolog\Formatter\LineFormatter;

class Utf8LineFormatter extends LineFormatter
{
    /**
     * Create a new UTF-8 line formatter instance.
     *
     * @param string $format
     * @param string $dateFormat
     * @param bool $allowInlineLineBreaks
     * @param bool $ignoreEmptyContextAndExtra
     */
    public function __construct($format = null, $dateFormat = null, $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    /**
     * Format a log record with proper UTF-8 encoding.
     *
     * @param array $record
     * @return string
     */
    public function format(array $record): string
    {
        // Ensure the message is properly UTF-8 encoded
        if (isset($record['message'])) {
            $record['message'] = $this->ensureUtf8($record['message']);
        }

        // Ensure context is properly UTF-8 encoded
        if (isset($record['context'])) {
            $record['context'] = $this->ensureUtf8Context($record['context']);
        }

        // Format the record
        $formatted = parent::format($record);

        // Ensure the final output is UTF-8
        return $this->ensureUtf8($formatted);
    }

    /**
     * Ensure a string is properly UTF-8 encoded.
     *
     * @param string $string
     * @return string
     */
    protected function ensureUtf8($string)
    {
        // Remove any BOM
        $string = str_replace("\xEF\xBB\xBF", '', $string);
        
        // If the string is not UTF-8, convert it
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'auto');
        }
        
        return $string;
    }

    /**
     * Ensure context array is properly UTF-8 encoded.
     *
     * @param array $context
     * @return array
     */
    protected function ensureUtf8Context($context)
    {
        $encoded = [];
        
        foreach ($context as $key => $value) {
            if (is_string($value)) {
                $encoded[$key] = $this->ensureUtf8($value);
            } elseif (is_array($value)) {
                $encoded[$key] = $this->ensureUtf8Context($value);
            } else {
                $encoded[$key] = $value;
            }
        }
        
        return $encoded;
    }
}

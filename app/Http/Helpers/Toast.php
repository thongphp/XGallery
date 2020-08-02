<?php

namespace App\Http\Helpers;

use Throwable;

/**
 * Class Toast
 * @package App\Http\Helpers
 */
class Toast
{
    /**
     * @param string $title
     * @param string $status
     * @param string $message
     *
     * @return string
     */
    public static function html(string $title, string $status, string $message): string
    {
        try {
            return view(
                'includes.toast',
                ['title' => $title, 'status' => $status, 'message' => $message]
            )->render();
        } catch (Throwable $e) {
            return '';
        }
    }

    /**
     * @param string $title
     * @param string $message
     *
     * @return string
     */
    public static function success(string $title, string $message = ''): string
    {
        return self::html($title, ucfirst(__FUNCTION__), $message);
    }

    /**
     * @param string $title
     * @param string $message
     *
     * @return string
     */
    public static function warning(string $title, string $message = ''): string
    {
        return self::html($title, ucfirst(__FUNCTION__), $message);
    }
}

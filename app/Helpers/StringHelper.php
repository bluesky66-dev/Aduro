<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Helpers;

class StringHelper
{
    const KIB = 1024;
    const MIB = 1024 * 1024;
    const GIB = 1024 * 1024 * 1024;
    const TIB = 1024 * 1024 * 1024 * 1024;

    public static function generateRandomString($length = 20)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $bytes = max($bytes, 0);
        $suffix = 'B';
        $value = $bytes;
        if ($bytes >= self::TIB) {
            $suffix = 'TiB';
            $value = $bytes / self::TIB;
        } else if ($bytes >= self::GIB) {
            $suffix = "GiB";
            $value = $bytes / self::GIB;
        } else if ($bytes >= self::MIB) {
            $suffix = "MiB";
            $value = $bytes / self::MIB;
        } else if ($bytes >= self::KIB) {
            $suffix = "KiB";
            $value = $bytes / self::KIB;
        }

        return round($value, $precision) . ' ' . $suffix;
    }

    /**
     * @method timeElapsed
     * @param $seconds time in bigInt
     * @return String
     */
    public static function timeElapsed($seconds)
    {
        $minutes = 0;
        $hours = 0;
        $days = 0;
        $weeks = 0;
        $months = 0;
        $years = 0;

        if ($seconds == 0) {
            return "N/A";
        }
        while ($seconds >= 31536000) {
            $years++;
            $seconds -= 31536000;
        }
        while ($seconds >= 2592000) {
            $months++;
            $seconds -= 2592000;
        }
        while ($seconds >= 604800) {
            $weeks++;
            $seconds -= 604800;
        }
        while ($seconds >= 86400) {
            $days++;
            $seconds -= 86400;
        }
        while ($seconds >= 3600) {
            $hours++;
            $seconds -= 3600;
        }
        while ($seconds >= 60) {
            $minutes++;
            $seconds -= 60;
        }
        $years = ($years == 0) ? "" : $years . "Y ";
        $months = ($months == 0) ? "" : $months . "M ";
        $weeks = ($weeks == 0) ? "" : $weeks . "W ";
        $days = ($days == 0) ? "" : $days . "D ";
        $hours = ($hours == 0) ? "" : $hours . "h ";
        $minutes = ($minutes == 0) ? "" : $minutes . "m ";
        $seconds = ($seconds == 0) ? "" : $seconds . "s";
        return $years . $months . $weeks . $days . $hours . $minutes . $seconds;
    }
}

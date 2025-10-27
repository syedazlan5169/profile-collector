<?php

namespace App\Support;

use Carbon\Carbon;

class Nric
{
    /**
     * Parse a Malaysian NRIC string into structured data.
     *
     * @param  string|null  $nric  e.g. "860329016763" or "860329-01-6763"
     * @return array|null
     * [
     *   'raw'           => '860329016763',
     *   'yy'            => '86',
     *   'mm'            => '03',
     *   'dd'            => '29',
     *   'state_code'    => '01',
     *   'serial'        => '6763',
     *   'date_of_birth' => '1986-03-29',   // Y-m-d or null if invalid
     *   'gender'        => 'male'|'female' // null if indeterminate
     * ]
     */
    public static function parse(?string $nric): ?array
    {
        if (!$nric) return null;

        // Keep digits only
        $raw = preg_replace('/\D+/', '', $nric ?? '');
        if (strlen($raw) !== 12) {
            return null; // not a valid length
        }

        $yy   = substr($raw, 0, 2);
        $mm   = substr($raw, 2, 2);
        $dd   = substr($raw, 4, 2);
        $pp   = substr($raw, 6, 2);  // historical "place code"
        $last = substr($raw, -1);    // last digit (odd=male, even=female)
        $serial = substr($raw, 8, 4);

        // Gender by last digit
        $gender = null;
        if (ctype_digit($last)) {
            $gender = ((int)$last % 2 === 1) ? 'male' : 'female';
        }

        // Century disambiguation for YY:
        // Heuristic: if YY <= current YY -> 2000s, else 1900s
        // e.g. in 2025: 00..25 -> 2000..2025, 26..99 -> 1926..1999
        $currentTwo = (int) date('y');
        $yyNum      = (int) $yy;
        $century    = ($yyNum <= $currentTwo) ? 2000 : 1900;
        $fullYear   = $century + $yyNum;

        // Validate date
        $date = null;
        try {
            $dateObj = Carbon::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $fullYear, (int)$mm, (int)$dd));
            if ($dateObj && $dateObj->format('Y-m-d')) {
                $date = $dateObj->toDateString();
            }
        } catch (\Throwable) {
            $date = null;
        }

        return [
            'raw'           => $raw,
            'yy'            => $yy,
            'mm'            => $mm,
            'dd'            => $dd,
            'state_code'    => $pp,
            'serial'        => $serial,
            'date_of_birth' => $date,
            'gender'        => $gender,
        ];
    }

    /**
     * Quick convenience helpers (optional use)
     */
    public static function dob(?string $nric): ?string
    {
        $p = self::parse($nric);
        return $p['date_of_birth'] ?? null;
    }

    public static function gender(?string $nric): ?string
    {
        $p = self::parse($nric);
        return $p['gender'] ?? null;
    }

    public static function stateCode(?string $nric): ?string
    {
        $p = self::parse($nric);
        return $p['state_code'] ?? null;
    }
}

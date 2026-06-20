<?php

namespace App\Services;

use Illuminate\Support\Str;

class SimpleCaptcha
{
    public const SESSION_KEY = 'simple_captcha.answer';

    /**
     * Generate a simple arithmetic expression and store the answer in session.
     */
    public static function generate(): string
    {
        // Always generate at least two-digit-ish answers for readability.
        $a = random_int(1, 20);
        $b = random_int(1, 20);
        $ops = ['+', '-'];
        $op = $ops[array_rand($ops)];

        // Keep subtraction non-negative for better UX.
        if ($op === '-' && $a < $b) {
            [$a, $b] = [$b, $a];
        }

        $answer = match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            default => $a + $b,
        };

        session()->put(self::SESSION_KEY, (string) $answer);

        return $a . ' ' . $op . ' ' . $b;
    }

    /**
     * Verify user input against the last generated captcha.
     */
    public static function verify(?string $userAnswer): bool
    {
        $stored = session()->get(self::SESSION_KEY);
        if ($stored === null) {
            return false;
        }

        $ok = hash_equals((string) $stored, trim((string) ($userAnswer ?? '')));

        // Always invalidate after any attempt — prevents brute-force replay.
        session()->forget(self::SESSION_KEY);

        return $ok;
    }

    public static function fieldName(): string
    {
        return 'captcha_answer';
    }
}


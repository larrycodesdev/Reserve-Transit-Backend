<?php
    class Utils {
        public static function randomString($length = 10): string {
            return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
        }

        public static function validateEmail(string $email): bool {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
    }

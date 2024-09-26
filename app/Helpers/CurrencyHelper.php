<?php

if (!function_exists('formatCurrency')) {
    function formatCurrency($number)
    {
        if ($number >= 1e9) {
            return '$ ' . number_format($number / 1e9, 1) . ' B';
        }
        if ($number >= 1e6) {
            return '$ ' . number_format($number / 1e6, 1) . ' M';
        }

        return '$ ' . number_format($number, 0, ',', '.');
    }
}

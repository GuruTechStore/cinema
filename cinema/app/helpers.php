<?php

if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        return 'S/ ' . number_format($price, 2);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd M Y') {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '';
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($path, $folder = '', $default = 'placeholder.jpg') {
        if ($path && file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        
        return asset('images/' . $folder . '/' . $default);
    }
}

if (!function_exists('getPosterUrl')) {
    function getPosterUrl($poster) {
        return getImageUrl($poster, 'posters', 'placeholder.jpg');
    }
}

if (!function_exists('getCinemaImageUrl')) {
    function getCinemaImageUrl($image, $cinemaName) {
        $defaultImage = str_replace(' ', '-', strtolower($cinemaName)) . '.jpg';
        return getImageUrl($image, 'cines', $defaultImage);
    }
}

if (!function_exists('getDulceriaImageUrl')) {
    function getDulceriaImageUrl($image, $productName) {
        $defaultImage = str_replace(' ', '-', strtolower($productName)) . '.jpg';
        return getImageUrl($image, 'dulceria', $defaultImage);
    }
    function userName() {
    return Auth::check() && Auth::user()->name ? Auth::user()->name : 'Usuario';
}

    function isAdmin() {
        return Auth::check() && 
            Auth::user() && 
            method_exists(Auth::user(), 'esAdmin') && 
            Auth::user()->esAdmin();
    }
}

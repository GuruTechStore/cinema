<?php
// app/helpers.php

if (!function_exists('formatPrice')) {
    /**
     * Formatear precio en soles peruanos
     * 
     * @param float $price
     * @return string
     */
    function formatPrice($price) {
        return 'S/ ' . number_format($price, 2);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formatear fecha usando Carbon
     * 
     * @param mixed $date
     * @param string $format
     * @return string
     */
    function formatDate($date, $format = 'd M Y') {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '';
    }
}

if (!function_exists('getImageUrl')) {
    /**
     * Obtener URL de imagen con fallback
     * 
     * @param string $path
     * @param string $folder
     * @param string $default
     * @return string
     */
    function getImageUrl($path, $folder = '', $default = 'placeholder.jpg') {
        if ($path && file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        
        return asset('images/' . $folder . '/' . $default);
    }
}

if (!function_exists('getPosterUrl')) {
    /**
     * Obtener URL del poster de película
     * 
     * @param string $poster
     * @return string
     */
    function getPosterUrl($poster) {
        return getImageUrl($poster, 'posters', 'placeholder.jpg');
    }
}

if (!function_exists('getCinemaImageUrl')) {
    /**
     * Obtener URL de imagen de cine
     * 
     * @param string $image
     * @param string $cinemaName
     * @return string
     */
    function getCinemaImageUrl($image, $cinemaName) {
        $defaultImage = str_replace(' ', '-', strtolower($cinemaName)) . '.jpg';
        return getImageUrl($image, 'cines', $defaultImage);
    }
}

if (!function_exists('getDulceriaImageUrl')) {
    /**
     * Obtener URL de imagen de producto de dulcería
     * 
     * @param string $image
     * @param string $productName
     * @return string
     */
    function getDulceriaImageUrl($image, $productName) {
        $defaultImage = str_replace(' ', '-', strtolower($productName)) . '.jpg';
        return getImageUrl($image, 'dulceria', $defaultImage);
    }
}

if (!function_exists('userName')) {
    /**
     * Obtener el nombre del usuario autenticado
     * 
     * @return string
     */
    function userName() {
        return Auth::check() && Auth::user()->name ? Auth::user()->name : 'Usuario';
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Verificar si el usuario es administrador
     * 
     * @return bool
     */
    function isAdmin() {
        return Auth::check() && 
               Auth::user() && 
               method_exists(Auth::user(), 'esAdmin') && 
               Auth::user()->esAdmin();
    }
}

if (!function_exists('isUser')) {
    /**
     * Verificar si el usuario es usuario normal
     * 
     * @return bool
     */
    function isUser() {
        return Auth::check() && 
               Auth::user() && 
               method_exists(Auth::user(), 'esUsuario') && 
               Auth::user()->esUsuario();
    }
}

if (!function_exists('formatTime')) {
    /**
     * Formatear hora
     * 
     * @param mixed $time
     * @param string $format
     * @return string
     */
    function formatTime($time, $format = 'H:i') {
        return $time ? \Carbon\Carbon::parse($time)->format($format) : '';
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Formatear fecha y hora completa
     * 
     * @param mixed $datetime
     * @param string $format
     * @return string
     */
    function formatDateTime($datetime, $format = 'd M Y - H:i') {
        return $datetime ? \Carbon\Carbon::parse($datetime)->format($format) : '';
    }
}

if (!function_exists('getEstadoBadge')) {
    /**
     * Obtener badge HTML para estados
     * 
     * @param string $estado
     * @param string $tipo
     * @return string
     */
    function getEstadoBadge($estado, $tipo = 'reserva') {
        $badges = [
            'reserva' => [
                'confirmada' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Confirmada</span>',
                'pendiente' => '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pendiente</span>',
                'cancelada' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelada</span>',
            ],
            'pedido' => [
                'confirmado' => '<span class="badge bg-info"><i class="fas fa-clock me-1"></i>Confirmado</span>',
                'preparando' => '<span class="badge bg-warning"><i class="fas fa-utensils me-1"></i>Preparando</span>',
                'listo' => '<span class="badge bg-success"><i class="fas fa-bell me-1"></i>Listo</span>',
                'entregado' => '<span class="badge bg-secondary"><i class="fas fa-check-circle me-1"></i>Entregado</span>',
                'cancelado' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelado</span>',
            ]
        ];
        
        return $badges[$tipo][$estado] ?? '<span class="badge bg-secondary">' . ucfirst($estado) . '</span>';
    }
}

if (!function_exists('getMetodoPagoIcon')) {
    /**
     * Obtener icono para método de pago
     * 
     * @param string $metodo
     * @return string
     */
    function getMetodoPagoIcon($metodo) {
        $iconos = [
            'yape' => '<i class="fab fa-cc-paypal text-purple"></i> Yape',
            'visa' => '<i class="fab fa-cc-visa text-primary"></i> Visa',
            'mastercard' => '<i class="fab fa-cc-mastercard text-warning"></i> Mastercard',
        ];
        
        return $iconos[$metodo] ?? '<i class="fas fa-credit-card"></i> ' . ucfirst($metodo);
    }
}

if (!function_exists('truncateText')) {
    /**
     * Truncar texto con puntos suspensivos
     * 
     * @param string $text
     * @param int $length
     * @return string
     */
    function truncateText($text, $length = 100) {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }
}

if (!function_exists('getCarritoCount')) {
    /**
     * Obtener cantidad de items en el carrito de dulcería
     * 
     * @return int
     */
    function getCarritoCount() {
        $carrito = session('carrito_dulceria', []);
        return array_sum(array_column($carrito, 'cantidad'));
    }
}

if (!function_exists('calcularEdad')) {
    /**
     * Calcular edad de película por fecha de estreno
     * 
     * @param mixed $fechaEstreno
     * @return string
     */
    function calcularEdad($fechaEstreno) {
        if (!$fechaEstreno) return '';
        
        $fecha = \Carbon\Carbon::parse($fechaEstreno);
        $now = \Carbon\Carbon::now();
        
        if ($fecha->isToday()) {
            return 'Estreno hoy';
        } elseif ($fecha->isTomorrow()) {
            return 'Estreno mañana';
        } elseif ($fecha->isFuture()) {
            return 'Próximo estreno: ' . $fecha->format('d M');
        } else {
            $diasEstreno = $fecha->diffInDays($now);
            if ($diasEstreno < 7) {
                return 'Hace ' . $diasEstreno . ' días';
            } elseif ($diasEstreno < 30) {
                return 'Hace ' . ceil($diasEstreno / 7) . ' semanas';
            } else {
                return $fecha->format('M Y');
            }
        }
    }
}
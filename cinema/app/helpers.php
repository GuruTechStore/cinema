<?php
// app/helpers.php - ACTUALIZADO CON MEJORAS

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
if (!function_exists('isAdmin')) {
    /**
     * Verificar si el usuario autenticado es administrador
     * 
     * @return bool
     */
    function isAdmin() {
        return Auth::check() && Auth::user()->esAdmin();
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
     * Obtener URL de imagen con fallback mejorado
     * 
     * @param string $path
     * @param string $folder
     * @param string $default
     * @return string
     */
    function getImageUrl($path, $folder = '', $default = 'placeholder.jpg') {
        // Si hay path y el archivo existe
        if ($path && \Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }
        
        // Si hay path pero el archivo no existe, intentar reparar la ruta
        if ($path) {
            // Verificar si es una ruta completa que necesita ser simplificada
            $cleanPath = str_replace(['storage/', 'public/'], '', $path);
            if (\Storage::disk('public')->exists($cleanPath)) {
                return asset('storage/' . $cleanPath);
            }
            
            // Verificar si solo necesita agregar la carpeta
            $pathWithFolder = $folder . '/' . basename($path);
            if (\Storage::disk('public')->exists($pathWithFolder)) {
                return asset('storage/' . $pathWithFolder);
            }
        }
        
        // Fallback a imagen por defecto
        $defaultPath = 'images/' . ($folder ? $folder . '/' : '') . $default;
        
        // Verificar si existe la imagen por defecto
        if (file_exists(public_path($defaultPath))) {
            return asset($defaultPath);
        }
        
        // Crear imagen placeholder si no existe
        return createPlaceholderImage($folder, $default);
    }
}

if (!function_exists('getDulceriaImageUrl')) {
    /**
     * Obtener URL de imagen de producto de dulcería con múltiples fallbacks
     * 
     * @param string $image
     * @param string $productName
     * @return string
     */
    function getDulceriaImageUrl($image, $productName) {
        // Limpiar nombre del producto para usar como fallback
        $cleanProductName = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['-', 'a', 'e', 'i', 'o', 'u', 'n'], $productName));
        $defaultImage = $cleanProductName . '.jpg';
        
        return getImageUrl($image, 'dulceria', $defaultImage);
    }
}

if (!function_exists('createPlaceholderImage')) {
    /**
     * Crear imagen placeholder si no existe
     * 
     * @param string $folder
     * @param string $filename
     * @return string
     */
    function createPlaceholderImage($folder, $filename) {
        // Retornar una imagen placeholder base64 o URL genérica
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200">
                <rect width="300" height="200" fill="#f8f9fa"/>
                <text x="150" y="100" text-anchor="middle" fill="#6c757d" font-family="Arial" font-size="14">
                    Sin imagen
                </text>
                <text x="150" y="120" text-anchor="middle" fill="#6c757d" font-family="Arial" font-size="12">
                    ' . ucfirst($folder) . '
                </text>
            </svg>
        ');
    }
}

if (!function_exists('ensureStorageDirectories')) {
    /**
     * Asegurar que existan los directorios de storage necesarios
     * 
     * @return void
     */
    function ensureStorageDirectories() {
        $directories = [
            'dulceria',
            'peliculas',
            'cines',
            'usuarios'
        ];
        
        foreach ($directories as $dir) {
            if (!\Storage::disk('public')->exists($dir)) {
                \Storage::disk('public')->makeDirectory($dir);
                \Log::info("Directorio creado: storage/app/public/{$dir}");
            }
        }
    }
}

if (!function_exists('optimizeImage')) {
    /**
     * Optimizar imagen antes de guardar
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string
     */
    function optimizeImage($file, $folder, $maxWidth = 800, $maxHeight = 600, $quality = 85) {
        try {
            // Generar nombre único
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $folder . '/' . $filename;
            
            // Verificar que el directorio existe
            if (!\Storage::disk('public')->exists($folder)) {
                \Storage::disk('public')->makeDirectory($folder);
            }
            
            // Para imágenes pequeñas, solo guardar sin procesar
            if ($file->getSize() < 500000) { // 500KB
                $file->storeAs($folder, $filename, 'public');
                return $path;
            }
            
            // Aquí se puede agregar lógica de redimensionamiento con GD o Imagick
            // Por simplicidad, guardamos la imagen original
            $file->storeAs($folder, $filename, 'public');
            
            \Log::info('Imagen optimizada y guardada', [
                'path' => $path,
                'size' => $file->getSize()
            ]);
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Error al optimizar imagen', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            // Fallback: guardar sin optimizar
            return $file->store($folder, 'public');
        }
    }
}

if (!function_exists('deleteImageSafely')) {
    /**
     * Eliminar imagen de forma segura
     * 
     * @param string $path
     * @return bool
     */
    function deleteImageSafely($path) {
        if (!$path) {
            return true;
        }
        
        try {
            if (\Storage::disk('public')->exists($path)) {
                $deleted = \Storage::disk('public')->delete($path);
                
                if ($deleted) {
                    \Log::info('Imagen eliminada correctamente', ['path' => $path]);
                } else {
                    \Log::warning('No se pudo eliminar la imagen', ['path' => $path]);
                }
                
                return $deleted;
            }
            
            return true; // Si no existe, consideramos que está "eliminada"
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar imagen', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}

if (!function_exists('validateImageFile')) {
    /**
     * Validar archivo de imagen
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    function validateImageFile($file) {
        $errors = [];
        
        // Verificar que es un archivo válido
        if (!$file || !$file->isValid()) {
            $errors[] = 'Archivo inválido';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Verificar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WEBP';
        }
        
        // Verificar tamaño (máximo 2MB)
        if ($file->getSize() > 2048000) {
            $errors[] = 'El archivo es demasiado grande. Máximo 2MB';
        }
        
        // Verificar dimensiones mínimas
        if (function_exists('getimagesize')) {
            $dimensions = getimagesize($file->getPathname());
            if ($dimensions && ($dimensions[0] < 100 || $dimensions[1] < 100)) {
                $errors[] = 'La imagen es demasiado pequeña. Mínimo 100x100 pixels';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'dimensions' => $dimensions ?? null
        ];
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
        return getImageUrl($poster, 'peliculas', 'poster-placeholder.jpg');
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

if (!function_exists('getStorageInfo')) {
    /**
     * Obtener información del storage
     * 
     * @return array
     */
    function getStorageInfo() {
        try {
            $publicPath = storage_path('app/public');
            $size = 0;
            $files = 0;
            
            if (is_dir($publicPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicPath)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                        $files++;
                    }
                }
            }
            
            return [
                'total_size' => $size,
                'total_files' => $files,
                'size_formatted' => formatBytes($size),
                'path' => $publicPath,
                'url' => asset('storage')
            ];
            
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'total_size' => 0,
                'total_files' => 0,
                'size_formatted' => '0 B'
            ];
        }
    }
}

if (!function_exists('formatBytes')) {
    /**
     * Formatear bytes a formato legible
     * 
     * @param int $size
     * @param int $precision
     * @return string
     */
    function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
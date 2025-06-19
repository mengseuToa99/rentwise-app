<?php
// Image Optimization Helper
class ImageOptimizer {
    public static function getOptimizedImageUrl($path, $width = null, $height = null, $quality = 80) {
        $params = [];
        if ($width) $params[] = "w=$width";
        if ($height) $params[] = "h=$height";
        if ($quality !== 80) $params[] = "q=$quality";
        
        $query = $params ? "?" . implode("&", $params) : "";
        return "/images/optimize" . $path . $query;
    }
    
    public static function generateWebP($imagePath) {
        $pathInfo = pathinfo($imagePath);
        $webpPath = $pathInfo["dirname"] . "/" . $pathInfo["filename"] . ".webp";
        
        if (function_exists("imagewebp")) {
            $image = null;
            switch (strtolower($pathInfo["extension"])) {
                case "jpg":
                case "jpeg":
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case "png":
                    $image = imagecreatefrompng($imagePath);
                    break;
            }
            
            if ($image) {
                imagewebp($image, $webpPath, 80);
                imagedestroy($image);
                return $webpPath;
            }
        }
        
        return $imagePath;
    }
}

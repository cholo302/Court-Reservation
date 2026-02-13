<?php
/**
 * Photo/Image Validator
 * Validates uploaded image files
 */

class PhotoValidator {
    
    // Allowed MIME types
    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];
    
    // Allowed file extensions
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Max file size (5MB)
    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    
    // Min dimensions
    const MIN_WIDTH = 100;
    const MIN_HEIGHT = 100;
    
    // Max dimensions
    const MAX_WIDTH = 4000;
    const MAX_HEIGHT = 4000;
    
    /**
     * Validate uploaded photo file
     * Returns: ['valid' => bool, 'message' => string]
     */
    public static function validate($file) {
        // Check if file exists
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'valid' => false,
                'message' => 'No file uploaded or upload error occurred.'
            ];
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return [
                'valid' => false,
                'message' => 'File size exceeds maximum limit of 5MB.'
            ];
        }
        
        // Check file type by MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return [
                'valid' => false,
                'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.'
            ];
        }
        
        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            return [
                'valid' => false,
                'message' => 'Invalid file extension. Only jpg, jpeg, png, gif, and webp are allowed.'
            ];
        }
        
        // Check image dimensions
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'valid' => false,
                'message' => 'Could not read image. File may be corrupted.'
            ];
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
            return [
                'valid' => false,
                'message' => 'Image dimensions too small. Minimum is ' . self::MIN_WIDTH . 'x' . self::MIN_HEIGHT . ' pixels.'
            ];
        }
        
        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            return [
                'valid' => false,
                'message' => 'Image dimensions too large. Maximum is ' . self::MAX_WIDTH . 'x' . self::MAX_HEIGHT . ' pixels.'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Photo validation passed.',
            'mime_type' => $mimeType,
            'extension' => $ext,
            'width' => $width,
            'height' => $height
        ];
    }
    
    /**
     * Save uploaded photo to storage
     * Returns: ['success' => bool, 'path' => string, 'message' => string]
     */
    public static function save($file, $userId, $photoType = 'profile') {
        // Validate first
        $validation = self::validate($file);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'path' => null,
                'message' => $validation['message']
            ];
        }
        
        // Create storage directory if not exists
        $storageDir = __DIR__ . '/../../storage/avatars';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }
        
        // Generate unique filename with photo type
        $ext = $validation['extension'];
        $filename = 'user_' . $userId . '_' . $photoType . '_' . time() . '.' . $ext;
        $filepath = $storageDir . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'Failed to save photo. Please try again.'
            ];
        }
        
        // Set proper permissions
        @chmod($filepath, 0644);
        
        // Return relative path for database storage
        $relativePath = 'storage/avatars/' . $filename;
        
        return [
            'success' => true,
            'path' => $relativePath,
            'message' => 'Photo uploaded successfully.',
            'filename' => $filename
        ];
    }
    
    /**
     * Delete old photo if exists
     */
    public static function delete($photoPath) {
        if (!$photoPath) {
            return true;
        }
        
        $fullPath = __DIR__ . '/../../' . $photoPath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true;
    }
    
    /**
     * Get photo URL from path
     */
    public static function getPhotoUrl($photoPath) {
        if (!$photoPath) {
            return '/assets/images/default-avatar.png';
        }
        
        return '/' . $photoPath;
    }
}

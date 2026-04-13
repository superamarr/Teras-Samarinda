<?php
class Uploader {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 5242880;
    
    public function __construct($uploadDir = null) {
        $this->uploadDir = $uploadDir ?? __DIR__ . '/../public/uploads';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function upload($file, $prefix = '') {
        if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }
        
        if ($file['size'] > $this->maxSize) {
            throw new Exception('File size exceeds limit');
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new Exception('File type not allowed');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        
        $targetPath = $this->uploadDir . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        return $filename;
    }
    
    public function delete($filename) {
        $filepath = $this->uploadDir . '/' . $filename;
        
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
    }
    
    public function setMaxSize($size) {
        $this->maxSize = $size;
    }
}
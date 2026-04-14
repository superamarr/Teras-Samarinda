<?php
require_once __DIR__ . '/../models/HeroModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';
require_once __DIR__ . '/../../helpers/ActivityLogger.php';

class HeroController {
    private $model;
    private $uploadDir;
    private $videoDir;
    
    public function __construct() {
        $this->model = new HeroModel();
        $this->uploadDir = __DIR__ . '/../../public/uploads/hero/';
        $this->videoDir = __DIR__ . '/../../public/uploads/hero/videos/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        if (!file_exists($this->videoDir)) {
            mkdir($this->videoDir, 0755, true);
        }
    }

    private function normalizeMediaPath($path, $prefix) {
        $path = trim((string)$path);
        if ($path === '') return '';
        $path = ltrim($path, '/');
        if (strpos($path, $prefix . '/') === 0) {
            return $path;
        }
        return $prefix . '/' . $path;
    }
    
    public function get() {
        try {
            $data = $this->model->get();
            
            if (!$data) {
                $this->model->createTableIfNotExists();
                $data = $this->model->get();
                
                if (!$data) {
                    return Response::json([
                        'success' => true,
                        'data' => [
                            'title_line1' => 'Jelajahi Ikon Baru',
                            'title_line2' => 'Kota Samarinda',
                            'title_line1_italic' => ['Ikon', 'Baru'],
                            'title_line2_italic' => ['Kota'],
                            'subtitle' => 'Teras Samarinda menghadirkan ruang publik modern di tepian Sungai Mahakam.',
                            'cta_text' => 'LIHAT EVENT',
                            'cta_link' => '/events',
                            'cta_text_secondary' => 'BOOKING EVENT',
                            'cta_link_secondary' => 'https://wa.me/6281522650048',
                            'use_video' => false,
                            'video_file' => '',
                            'background_image' => ''
                        ]
                    ]);
                }
            }
            
            $result = [
                'title_line1' => $data['title_line1'] ?? 'Jelajahi Ikon Baru',
                'title_line2' => $data['title_line2'] ?? 'Kota Samarinda',
                'title_line1_italic' => [],
                'title_line2_italic' => [],
                'subtitle' => $data['subtitle'] ?? '',
                'cta_text' => $data['cta_text'] ?? 'LIHAT EVENT',
                'cta_link' => $data['cta_link'] ?? '/events',
                'cta_text_secondary' => $data['cta_text_secondary'] ?? 'BOOKING EVENT',
                'cta_link_secondary' => $data['cta_link_secondary'] ?? 'https://wa.me/6281522650048',
                'use_video' => !empty($data['use_video']),
                'video_file' => $data['video_file'] ?? '',
                'background_image' => $data['background_image'] ?? ''
            ];
            
            // Return just the filename/path, frontend will construct full URL
            if (isset($data['background_image']) && $data['background_image']) {
                $result['background_image'] = $this->normalizeMediaPath($data['background_image'], 'hero');
            }
            
            if (isset($data['video_file']) && $data['video_file']) {
                $result['video_file'] = $this->normalizeMediaPath($data['video_file'], 'hero/videos');
            }
            
            if (isset($data['title_line1_italic']) && $data['title_line1_italic']) {
                if (is_string($data['title_line1_italic'])) {
                    $decoded = json_decode($data['title_line1_italic'], true);
                    $result['title_line1_italic'] = is_array($decoded) ? $decoded : [];
                } elseif (is_array($data['title_line1_italic'])) {
                    $result['title_line1_italic'] = $data['title_line1_italic'];
                }
            }
            
            if (isset($data['title_line2_italic']) && $data['title_line2_italic']) {
                if (is_string($data['title_line2_italic'])) {
                    $decoded = json_decode($data['title_line2_italic'], true);
                    $result['title_line2_italic'] = is_array($decoded) ? $decoded : [];
                } elseif (is_array($data['title_line2_italic'])) {
                    $result['title_line2_italic'] = $data['title_line2_italic'];
                }
            }
            
            Response::success('Data retrieved successfully', $result);
        } catch (Exception $e) {
            error_log("HeroController get error: " . $e->getMessage());
            Response::json([
                'success' => true,
                'data' => [
                    'title_line1' => 'Jelajahi Ikon Baru',
                    'title_line2' => 'Kota Samarinda',
                    'title_line1_italic' => ['Ikon', 'Baru'],
                    'title_line2_italic' => ['Kota'],
                    'subtitle' => 'Teras Samarinda menghadirkan ruang publik modern di tepian Sungai Mahakam.',
                    'cta_text' => 'LIHAT EVENT',
                    'cta_link' => '/events',
                    'cta_text_secondary' => 'BOOKING EVENT',
                    'cta_link_secondary' => 'https://wa.me/6281522650048',
                    'use_video' => false,
                    'video_file' => '',
                    'background_image' => ''
                ]
            ]);
        }
    }
    
    public function update() {
        $input = $_POST;
        $current = $this->model->get();
        $existingImage = $current ? ($current['background_image'] ?? '') : '';
        $existingVideo = $current ? ($current['video_file'] ?? '') : '';
        
        $useVideo = isset($input['use_video']) ? filter_var($input['use_video'], FILTER_VALIDATE_BOOLEAN) : false;
        
        $titleLine1Italic = [];
        if (isset($input['title_line1_italic'])) {
            $italicJson = $input['title_line1_italic'];
            if (is_string($italicJson)) {
                $decoded = json_decode($italicJson, true);
                if (is_array($decoded)) {
                    $titleLine1Italic = $decoded;
                }
            } elseif (is_array($input['title_line1_italic'])) {
                $titleLine1Italic = $input['title_line1_italic'];
            }
        }
        
        $titleLine2Italic = [];
        if (isset($input['title_line2_italic'])) {
            $italicJson = $input['title_line2_italic'];
            if (is_string($italicJson)) {
                $decoded = json_decode($italicJson, true);
                if (is_array($decoded)) {
                    $titleLine2Italic = $decoded;
                }
            } elseif (is_array($input['title_line2_italic'])) {
                $titleLine2Italic = $input['title_line2_italic'];
            }
        }
        
        $data = [
            'title_line1' => Sanitize::input($input['title_line1'] ?? ''),
            'title_line2' => Sanitize::input($input['title_line2'] ?? ''),
            'title_line1_italic' => json_encode($titleLine1Italic),
            'title_line2_italic' => json_encode($titleLine2Italic),
            'subtitle' => Sanitize::input($input['subtitle'] ?? ''),
            'cta_text' => Sanitize::input($input['cta_text'] ?? ''),
            'cta_link' => Sanitize::input($input['cta_link'] ?? '/events'),
            'cta_text_secondary' => Sanitize::input($input['cta_text_secondary'] ?? ''),
            'cta_link_secondary' => Sanitize::input($input['cta_link_secondary'] ?? ''),
            'use_video' => $useVideo ? 1 : 0,
            'video_file' => $existingVideo,
            'background_image' => $existingImage
        ];
        
        if (isset($_FILES['video_file'])) {
            $file = $_FILES['video_file'];
            
            if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                $errorMsg = 'Gagal mengupload video: ';
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE: $errorMsg .= 'Ukuran file melebihi batas server (upload_max_filesize).'; break;
                    case UPLOAD_ERR_PARTIAL: $errorMsg .= 'File hanya terupload sebagian.'; break;
                    case UPLOAD_ERR_NO_TMP_DIR: $errorMsg .= 'Folder temporary hilang.'; break;
                    case UPLOAD_ERR_CANT_WRITE: $errorMsg .= 'Gagal menulis file ke disk.'; break;
                    default: $errorMsg .= 'Error tidak diketahui (Code: ' . $file['error'] . ')';
                }
                Response::error($errorMsg);
                return;
            }

            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowedVideo = ['video/webm', 'video/mp4'];
                
                if (!in_array($file['type'], $allowedVideo)) {
                    Response::error('Hanya format WebM dan MP4 yang diizinkan untuk video');
                    return;
                }
                
                if ($file['size'] > 50 * 1024 * 1024) {
                    Response::error('Ukuran video maksimal 50MB');
                    return;
                }
                
                $extension = $file['type'] === 'video/webm' ? 'webm' : 'mp4';
                $filename = 'hero_video_' . time() . '.' . $extension;
                $destination = $this->videoDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($existingVideo && file_exists($this->videoDir . $existingVideo)) {
                        unlink($this->videoDir . $existingVideo);
                    }
                    $data['video_file'] = $filename;
                } else {
                    Response::error('Gagal memindahkan file video ke folder tujuan');
                    return;
                }
            }
        }
        
        if (isset($_FILES['background_image'])) {
            $file = $_FILES['background_image'];

            if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                $errorMsg = 'Gagal mengupload gambar: ';
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE: $errorMsg .= 'Ukuran file melebihi batas server (upload_max_filesize).'; break;
                    case UPLOAD_ERR_PARTIAL: $errorMsg .= 'File hanya terupload sebagian.'; break;
                    default: $errorMsg .= 'Error tidak diketahui.';
                }
                Response::error($errorMsg);
                return;
            }

            if ($file['error'] === UPLOAD_ERR_OK) {
                $sizeKB = $file['size'] / 1024;
                
                if ($sizeKB > 1024) {
                    $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
                } else {
                    $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                }
                
                if (!in_array($file['type'], $allowed)) {
                    Response::error('Hanya format WebP, JPG, PNG, dan GIF yang diizinkan');
                    return;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) {
                    Response::error('Ukuran file maksimal 5MB');
                    return;
                }
                
                // Jangan ubah ekstensi ke webp tanpa proses konversi sebenarnya.
                // Simpan dengan ekstensi asli agar browser bisa render file dengan benar.
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, ['webp', 'jpg', 'jpeg', 'png', 'gif'], true)) {
                    $extension = $file['type'] === 'image/webp' ? 'webp' : 'jpg';
                }
                
                $filename = 'hero_' . time() . '.' . $extension;
                $destination = $this->uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($existingImage && file_exists($this->uploadDir . $existingImage)) {
                        unlink($this->uploadDir . $existingImage);
                    }
                    $data['background_image'] = $filename;
                } else {
                    Response::error('Gagal memindahkan file gambar ke folder tujuan');
                    return;
                }
            }
        }
        
        $result = $this->model->update($data);
        
        if ($result) {
            ActivityLogger::update('Hero', 'Mengubah section "Hero Section"');
            Response::success('Hero section berhasil diperbarui!');
        } else {
            Response::error('Gagal memperbarui hero section');
        }
    }
    
    public function deleteMedia($type) {
        $current = $this->model->get();
        
        if (!$current || !isset($current['id'])) {
            Response::error('Data hero tidak ditemukan');
            return;
        }
        
        $id = $current['id'];
        $db = \Database::getInstance();
        
        if ($type === 'image') {
            if (isset($current['background_image']) && $current['background_image']) {
                $path = $this->uploadDir . $current['background_image'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $db->update('hero_section', ['background_image' => ''], 'id = :id', ['id' => $id]);
            Response::success('Gambar berhasil dihapus');
        } 
        elseif ($type === 'video') {
            if (isset($current['video_file']) && $current['video_file']) {
                $path = $this->videoDir . $current['video_file'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $db->update('hero_section', ['video_file' => '', 'use_video' => 0], 'id = :id', ['id' => $id]);
            Response::success('Video berhasil dihapus');
        }
        else {
            Response::error('Tipe media tidak valid. Gunakan "image" atau "video"');
        }
    }
}

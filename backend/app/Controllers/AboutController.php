<?php
require_once __DIR__ . '/../models/AboutModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';

class AboutController {
    private $model;
    private $uploadDir;
    
    public function __construct() {
        $this->model = new AboutModel();
        $this->uploadDir = __DIR__ . '/../../public/uploads/about/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    private function normalizeMediaPath($path, $prefix) {
        $path = trim((string)$path);
        if ($path === '') return '';
        $path = ltrim($path, '/');
        if (strpos($path, $prefix . '/') === 0) return $path;
        return $prefix . '/' . $path;
    }
    
    public function get() {
        $data = $this->model->get();
        
        if (!$data) {
            return Response::json([
                'success' => true,
                'data' => [
                    'title' => 'Tentang Teras',
                    'subtitle' => 'Samarinda',
                    'content' => 'Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai kawasan waterfront modern di tepian Sungai Mahakam.',
                    'image_left' => '',
                    'image_right' => '',
                    'button_text' => 'BACA SELENGKAPNYA',
                    'button_link' => '/tentang',
                    'layout_type' => 'default',
                    'vision' => '',
                    'mission' => '',
                    'page_hero_title' => 'Tentang Teras Samarinda',
                    'page_hero_subtitle' => 'Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai kawasan waterfront modern di tepian Sungai Mahakam.',
                    'page_hero_background' => '',
                    'welcome_title' => 'Selamat Datang Di Teras Samarinda',
                    'welcome_text' => 'Teras Samarinda bukan sekadar ruang terbuka hijau; ia adalah sebuah pernyataan tentang transformasi urban yang berkelanjutan.',
                    'welcome_image' => '',
                    'story_title' => 'Story Teras Samarinda',
                    'story_text' => 'Di tepian Sungai Mahakam, wajah Kota Samarinda perlahan berubah.',
                    'story_background' => ''
                ]
            ]);
        }
        
        $result = [
            'title' => $data['title'] ?? '',
            'subtitle' => $data['subtitle'] ?? '',
            'content' => $data['content'] ?? '',
            'vision' => $data['vision'] ?? '',
            'mission' => $data['mission'] ?? '',
            'button_text' => $data['button_text'] ?? 'BACA SELENGKAPNYA',
            'button_link' => $data['button_link'] ?? '/tentang',
            'layout_type' => $data['layout_type'] ?? 'default',
            'image_left' => '',
            'image_right' => '',
            'page_hero_title' => $data['page_hero_title'] ?? 'Tentang Teras Samarinda',
            'page_hero_subtitle' => $data['page_hero_subtitle'] ?? '',
            'page_hero_background' => '',
            'welcome_title' => $data['welcome_title'] ?? 'Selamat Datang Di Teras Samarinda',
            'welcome_text' => $data['welcome_text'] ?? '',
            'welcome_image' => '',
            'story_title' => $data['story_title'] ?? 'Story Teras Samarinda',
            'story_text' => $data['story_text'] ?? '',
            'story_background' => '',
            'title_italic' => []
        ];

        $rawItalic = $data['title_italic'] ?? '[]';
        $result['title_italic'] = is_array($rawItalic) ? $rawItalic : (json_decode($rawItalic, true) ?: []);
        
        $imageFields = ['image_left', 'image_right', 'page_hero_background', 'welcome_image', 'story_background'];
        foreach ($imageFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                $result[$field] = $this->normalizeMediaPath($data[$field], 'about');
            }
        }
        
        Response::success('Data retrieved successfully', $result);
    }
    
    public function update() {
        $input = $_POST;
        $current = $this->model->get();
        $existingImageLeft = $current ? ($current['image_left'] ?? '') : '';
        $existingImageRight = $current ? ($current['image_right'] ?? '') : '';
        $existingHeroBg = $current ? ($current['page_hero_background'] ?? '') : '';
        $existingWelcomeImg = $current ? ($current['welcome_image'] ?? '') : '';
        $existingStoryBg = $current ? ($current['story_background'] ?? '') : '';
        
        $data = [
            'title' => Sanitize::input($input['title'] ?? ''),
            'subtitle' => Sanitize::input($input['subtitle'] ?? ''),
            'content' => Sanitize::input($input['content'] ?? ''),
            'vision' => Sanitize::input($input['vision'] ?? ''),
            'mission' => Sanitize::input($input['mission'] ?? ''),
            'button_text' => Sanitize::input($input['button_text'] ?? 'BACA SELENGKAPNYA'),
            'button_link' => Sanitize::input($input['button_link'] ?? '/tentang'),
            'layout_type' => Sanitize::input($input['layout_type'] ?? 'default'),
            'image_left' => $existingImageLeft,
            'image_right' => $existingImageRight,
            'page_hero_title' => Sanitize::input($input['page_hero_title'] ?? ''),
            'page_hero_subtitle' => Sanitize::input($input['page_hero_subtitle'] ?? ''),
            'page_hero_background' => $existingHeroBg,
            'welcome_title' => Sanitize::input($input['welcome_title'] ?? ''),
            'welcome_text' => Sanitize::input($input['welcome_text'] ?? ''),
            'welcome_image' => $existingWelcomeImg,
            'story_text' => Sanitize::input($input['story_text'] ?? ''),
            'story_background' => $existingStoryBg,
            'title_italic' => $input['title_italic'] ?? '[]',
        ];

        // Ensure title_italic is a valid JSON string
        if (is_array($data['title_italic'])) {
            $data['title_italic'] = json_encode($data['title_italic']);
        }
        
        // Handle Image Left
        if (isset($_FILES['image_left']) && $_FILES['image_left']['error'] === UPLOAD_ERR_OK) {
            $newFile = $this->uploadFile($_FILES['image_left'], 'about_left_', $existingImageLeft);
            if ($newFile) $data['image_left'] = $newFile;
        }

        // Handle Image Right
        if (isset($_FILES['image_right']) && $_FILES['image_right']['error'] === UPLOAD_ERR_OK) {
            $newFile = $this->uploadFile($_FILES['image_right'], 'about_right_', $existingImageRight);
            if ($newFile) $data['image_right'] = $newFile;
        }

        // Handle Hero Background
        if (isset($_FILES['page_hero_background']) && $_FILES['page_hero_background']['error'] === UPLOAD_ERR_OK) {
            $newFile = $this->uploadFile($_FILES['page_hero_background'], 'about_hero_', $existingHeroBg);
            if ($newFile) $data['page_hero_background'] = $newFile;
        }

        // Handle Welcome Image
        if (isset($_FILES['welcome_image']) && $_FILES['welcome_image']['error'] === UPLOAD_ERR_OK) {
            $newFile = $this->uploadFile($_FILES['welcome_image'], 'about_welcome_', $existingWelcomeImg);
            if ($newFile) $data['welcome_image'] = $newFile;
        }

        // Handle Story Background
        if (isset($_FILES['story_background']) && $_FILES['story_background']['error'] === UPLOAD_ERR_OK) {
            $newFile = $this->uploadFile($_FILES['story_background'], 'about_story_', $existingStoryBg);
            if ($newFile) $data['story_background'] = $newFile;
        }
        
        $result = $this->model->update($data);
        
        if ($result) {
            Response::success('About section updated successfully');
        } else {
            Response::error('Failed to update about section');
        }
    }

    private function uploadFile($file, $prefix, $existingFile = '') {
        $sizeKB = $file['size'] / 1024;
        $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
        
        if (!in_array($file['type'], $allowed)) {
            Response::error('Format gambar tidak valid');
            return false;
        }
        
        $extension = ($file['type'] === 'image/webp' || $sizeKB > 1024) ? 'webp' : pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            if ($existingFile && file_exists($this->uploadDir . $existingFile)) {
                unlink($this->uploadDir . $existingFile);
            }
            return $filename;
        }
        return false;
    }
    
    public function deleteMedia($type = null) {
        if (!$type) {
            $type = $_GET['type'] ?? null;
        }

        $validTypes = ['image_left', 'image_right', 'page_hero_background', 'welcome_image', 'story_background'];
        $field = in_array($type, $validTypes) ? $type : null;
        
        if (!$field) {
            Response::error('Tipe media tidak valid: ' . ($type ?? 'null'));
            return;
        }
        
        $current = $this->model->get();
        if (!$current || !isset($current['id'])) {
            Response::error('Data about tidak ditemukan');
            return;
        }
        
        if (isset($current[$field]) && $current[$field]) {
            $path = $this->uploadDir . $current[$field];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        
        $db = \Database::getInstance();
        $db->update('about_section', [$field => ''], 'id = :id', ['id' => $current['id']]);
        Response::success('Gambar berhasil dihapus');
    }
}

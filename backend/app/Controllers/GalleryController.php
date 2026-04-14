<?php
require_once __DIR__ . '/../models/GalleryModel.php';
require_once __DIR__ . '/../models/SectionSettingsModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';
require_once __DIR__ . '/../../helpers/ActivityLogger.php';

class GalleryController {
    private $model;
    private $settingsModel;
    private $uploadDir;
    
    public function __construct() {
        $this->model = new GalleryModel();
        $this->settingsModel = new SectionSettingsModel('gallery_settings');
        $this->uploadDir = __DIR__ . '/../../public/uploads/gallery/';
        
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
    
    public function getAll() {
        try {
            $options = [
                'category' => $_GET['category'] ?? null,
                'featured' => isset($_GET['featured']) ? (int)$_GET['featured'] : null,
                'limit' => $_GET['limit'] ?? null
            ];
            $data = $this->model->getAll($options);
            
            foreach ($data as &$item) {
                if (isset($item['url']) && $item['url']) {
                    $item['url'] = $this->normalizeMediaPath($item['url'], 'gallery');
                }
            }
            
            Response::success('Gallery retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("GalleryController::getAll Error: " . $e->getMessage());
            Response::error('Gagal mengambil data galeri: ' . $e->getMessage(), 500);
        }
    }

    public function getSettings() {
        try {
            $data = $this->settingsModel->get();
            if ($data && isset($data['page_hero_background']) && $data['page_hero_background']) {
                $data['page_hero_background_url'] = $this->normalizeMediaPath($data['page_hero_background'], 'gallery');
            }
            
            if ($data) {
                $rawItalic = $data['section_title_italic'] ?? '[]';
                $data['section_title_italic'] = is_array($rawItalic) ? $rawItalic : (json_decode($rawItalic, true) ?: []);
            }
            
            Response::success('Gallery settings retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("GalleryController::getSettings Error: " . $e->getMessage());
            Response::error('Gagal mengambil pengaturan: ' . $e->getMessage(), 500);
        }
    }

    public function updateSettings() {
        try {
            $input = $_POST;
            $current = $this->settingsModel->get();
            $backgroundFilename = $current['page_hero_background'] ?? '';

            // Handle Background Media Upload
            if (isset($_FILES['page_hero_background']) && $_FILES['page_hero_background']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['page_hero_background'];
                $allowed = [
                    'image/webp', 'image/jpeg', 'image/jpg', 'image/png',
                    'video/mp4', 'video/webm'
                ];

                if (!in_array($file['type'], $allowed)) {
                    Response::error('Format media tidak diizinkan. Gunakan WebP/JPG/PNG untuk gambar atau MP4/WebM untuk video.');
                    return;
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_hero_' . time() . '.' . $extension;
                $destination = $this->uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($backgroundFilename && file_exists($this->uploadDir . $backgroundFilename)) {
                        unlink($this->uploadDir . $backgroundFilename);
                    }
                    $backgroundFilename = $filename;
                } else {
                    Response::error('Gagal mengupload background hero.');
                    return;
                }
            }

            $data = [
                'section_title' => Sanitize::input($input['section_title'] ?? ''),
                'section_subtitle' => Sanitize::input($input['section_subtitle'] ?? ''),
                'cta_text' => Sanitize::input($input['cta_text'] ?? ''),
                'cta_link' => Sanitize::input($input['cta_link'] ?? ''),
                'layout_type' => Sanitize::input($input['layout_type'] ?? 'default'),
                'page_hero_title' => Sanitize::input($input['page_hero_title'] ?? ''),
                'page_hero_subtitle' => Sanitize::input($input['page_hero_subtitle'] ?? ''),
                'page_hero_background' => $backgroundFilename,
                'section_title_italic' => $input['section_title_italic'] ?? '[]',
            ];

            if (is_array($data['section_title_italic'])) {
                $data['section_title_italic'] = json_encode($data['section_title_italic']);
            }
            
            if ($this->settingsModel->update($data)) {
                Response::success('Gallery settings updated successfully');
            } else {
                Response::error('Failed to update gallery settings');
            }
        } catch (Exception $e) {
            error_log("GalleryController::updateSettings Error: " . $e->getMessage());
            Response::error('Gagal memperbarui pengaturan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getById($id) {
        try {
            $data = $this->model->getById($id);
            if (!$data) {
                Response::notFound('Image not found');
                return;
            }
            
            if (isset($data['url']) && $data['url']) {
                $data['url'] = $this->normalizeMediaPath($data['url'], 'gallery');
            }
            
            Response::success('Image retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("GalleryController::getById Error: " . $e->getMessage());
            Response::error('Gagal mengambil detail gambar: ' . $e->getMessage(), 500);
        }
    }
    
    public function create() {
        try {
            $input = $_POST;
            $imageFilename = '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $sizeKB = $file['size'] / 1024;
                
                $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
                
                if (!in_array($file['type'], $allowed)) {
                    Response::error('Hanya format WebP, JPG, dan PNG yang diizinkan');
                    return;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) {
                    Response::error('Ukuran file maksimal 5MB');
                    return;
                }
                
                // Jangan paksa ekstensi webp tanpa konversi file.
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, ['webp', 'jpg', 'jpeg', 'png'], true)) {
                    $extension = $file['type'] === 'image/webp' ? 'webp' : 'jpg';
                }
                
                $filename = 'gallery_' . time() . '.' . $extension;
                $destination = $this->uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $imageFilename = $filename;
                } else {
                    Response::error('Gagal memindahkan file gambar ke folder tujuan');
                    return;
                }
            }
            
            $data = [
                'title' => Sanitize::input($input['title'] ?? ''),
                'category' => Sanitize::input($input['category'] ?? 'Pemandangan'),
                'url' => $imageFilename,
                'featured' => isset($input['featured']) ? (int)$input['featured'] : 0
            ];
            
            if (empty($data['url'])) {
                Response::error('Gambar wajib diupload');
                return;
            }
            
            $id = $this->model->create($data);
            if ($id) {
                ActivityLogger::create('Galeri', 'Menambahkan foto baru "' . $data['title'] . '"');
                Response::success('Image added successfully', ['id' => $id], 201);
            } else {
                Response::error('Gagal menambahkan gambar ke database');
            }
        } catch (Exception $e) {
            error_log("GalleryController::create Error: " . $e->getMessage());
            Response::error('Tidak dapat menambahkan gambar: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id) {
        try {
            $input = $_POST;
            $current = $this->model->getById($id);
            
            if (!$current) {
                Response::notFound('Image not found');
                return;
            }
            
            $imageFilename = $current['url'] ?? '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                
                $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
                
                if (!in_array($file['type'], $allowed)) {
                    Response::error('Hanya format WebP, JPG, dan PNG yang diizinkan');
                    return;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) {
                    Response::error('Ukuran file maksimal 5MB');
                    return;
                }
                
                $extension = $file['type'] === 'image/webp' ? 'webp' : pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '.' . $extension;
                $destination = $this->uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                        unlink($this->uploadDir . $imageFilename);
                    }
                    $imageFilename = $filename;
                } else {
                    Response::error('Gagal memindahkan file gambar ke folder tujuan');
                    return;
                }
            }
            
            $data = [
                'title' => Sanitize::input($input['title'] ?? ''),
                'category' => Sanitize::input($input['category'] ?? ''),
                'url' => $imageFilename
            ];
            
            if ($this->model->update($id, $data)) {
                ActivityLogger::update('Galeri', 'Mengubah foto "' . $data['title'] . '"');
                Response::success('Image updated successfully');
            } else {
                Response::error('Gagal memperbarui data gambar di database');
            }
        } catch (Exception $e) {
            error_log("GalleryController::update Error: " . $e->getMessage());
            Response::error('Tidak dapat menyimpan perubahan gambar: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        try {
            $current = $this->model->getById($id);
            $title = $current['title'] ?? 'Foto';
            
            if ($current && isset($current['url']) && $current['url']) {
                $path = $this->uploadDir . $current['url'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            if ($this->model->delete($id)) {
                ActivityLogger::delete('Galeri', 'Menghapus foto "' . $title . '"');
                Response::success('Image deleted successfully');
            } else {
                Response::error('Gagal menghapus gambar dari database');
            }
        } catch (Exception $e) {
            error_log("GalleryController::delete Error: " . $e->getMessage());
            Response::error('Gagal menghapus gambar: ' . $e->getMessage(), 500);
        }
    }
    
    public function toggleFeatured($id) {
        try {
            if ($this->model->toggleFeatured($id)) {
                Response::success('Featured status toggled');
            } else {
                Response::error('Gagal mengubah status unggulan');
            }
        } catch (Exception $e) {
            error_log("GalleryController::toggleFeatured Error: " . $e->getMessage());
            Response::error('Gagal mengubah status: ' . $e->getMessage(), 500);
        }
    }
}

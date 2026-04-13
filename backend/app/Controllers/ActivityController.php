<?php
require_once __DIR__ . '/../models/ActivityModel.php';
require_once __DIR__ . '/../models/SectionSettingsModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';
require_once __DIR__ . '/../../helpers/ActivityLogger.php';

class ActivityController {
    private $model;
    private $settingsModel;
    private $uploadDir;
    
    public function __construct() {
        $this->model = new ActivityModel();
        $this->settingsModel = new SectionSettingsModel('activities_settings');
        $this->uploadDir = __DIR__ . '/../../public/uploads/activities/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function getAll() {
        try {
            $status = $_GET['status'] ?? null;
            $data = $this->model->getAll($status);

            foreach ($data as &$item) {
                if (isset($item['image']) && $item['image']) {
                    $path = $item['image'];
                    $item['image'] = (strpos($path, '/') === 0) ? $path : 'activities/' . $path;
                }
            }

            Response::success('Activities retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("ActivityController::getAll Error: " . $e->getMessage());
            Response::error('Gagal mengambil data kegiatan: ' . $e->getMessage(), 500);
        }
    }

    public function getSettings() {
        try {
            $data = $this->settingsModel->get();
            if (isset($data['page_hero_background']) && $data['page_hero_background']) {
                $data['page_hero_background_url'] = 'activities/' . $data['page_hero_background'];
            }
            
            $rawItalic = $data['section_title_italic'] ?? '[]';
            $data['section_title_italic'] = is_array($rawItalic) ? $rawItalic : (json_decode($rawItalic, true) ?: []);
            
            Response::success('Activity settings retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("ActivityController::getSettings Error: " . $e->getMessage());
            Response::error('Gagal mengambil pengaturan: ' . $e->getMessage(), 500);
        }
    }

    public function updateSettings() {
        try {
            $input = $_POST;
            $current = $this->settingsModel->get();
            
            $imageFilename = $current['page_hero_background'] ?? '';
            
            // Handle background deletion
            if (($input['delete_hero_background'] ?? '') === 'true') {
                if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                    unlink($this->uploadDir . $imageFilename);
                }
                $imageFilename = '';
            }

            // Handle new background upload
            if (isset($_FILES['page_hero_background']) && $_FILES['page_hero_background']['error'] === UPLOAD_ERR_OK) {
                $newFilename = $this->handleImageUpload($_FILES['page_hero_background']);
                if ($newFilename) {
                    if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                        unlink($this->uploadDir . $imageFilename);
                    }
                    $imageFilename = $newFilename;
                }
            }

            $data = [
                'section_title' => Sanitize::input($input['section_title'] ?? ''),
                'section_subtitle' => Sanitize::input($input['section_subtitle'] ?? ''),
                'section_subtitle_extra' => Sanitize::input($input['section_subtitle_extra'] ?? ''),
                'cta_text' => Sanitize::input($input['cta_text'] ?? ''),
                'cta_link' => Sanitize::input($input['cta_link'] ?? ''),
                'layout_type' => Sanitize::input($input['layout_type'] ?? 'default'),
                'page_hero_title' => Sanitize::input($input['page_hero_title'] ?? ''),
                'page_hero_subtitle' => Sanitize::input($input['page_hero_subtitle'] ?? ''),
                'page_hero_background' => $imageFilename,
                'section_title_italic' => $input['section_title_italic'] ?? '[]',
            ];

            if (is_array($data['section_title_italic'])) {
                $data['section_title_italic'] = json_encode($data['section_title_italic']);
            }
            
            if ($this->settingsModel->update($data)) {
                Response::success('Activity settings updated successfully');
            } else {
                Response::error('Failed to update activity settings');
            }
        } catch (Exception $e) {
            error_log("ActivityController::updateSettings Error: " . $e->getMessage());
            Response::error('Gagal memperbarui pengaturan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getById($id) {
        try {
            $data = $this->model->getById($id);
            
            if (!$data) {
                Response::notFound('Activity not found');
                return;
            }
            
            if (isset($data['image']) && $data['image']) {
                $path = $data['image'];
                $data['image'] = (strpos($path, '/') === 0) ? $path : 'activities/' . $path;
            }
            
            Response::success('Activity retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("ActivityController::getById Error: " . $e->getMessage());
            Response::error('Gagal mengambil detail kegiatan: ' . $e->getMessage(), 500);
        }
    }
    
    public function create() {
        try {
            $input = $_POST;
            $imageFilename = '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageFilename = $this->handleImageUpload($_FILES['image']);
                if (!$imageFilename) return;
            }
            
            $data = [
                'name' => Sanitize::input($input['name'] ?? ''),
                'image' => $imageFilename
            ];
            
            $id = $this->model->create($data);
            
            if ($id) {
                ActivityLogger::create('Aktivitas', 'Menambahkan aktivitas baru "' . $data['name'] . '"');
                Response::success('Activity created successfully', ['id' => $id], 201);
            } else {
                Response::error('Gagal membuat kegiatan');
            }
        } catch (Exception $e) {
            error_log("ActivityController::create Error: " . $e->getMessage());
            Response::error('Tidak dapat membuat kegiatan: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id) {
        try {
            $input = $_POST;
            $current = $this->model->getById($id);
            
            if (!$current) {
                Response::notFound('Activity not found');
                return;
            }
            
            $imageFilename = $current['image'] ?? '';
            
            // Handle deletion flag
            if (($input['delete_image'] ?? '') === 'true') {
                if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                    unlink($this->uploadDir . $imageFilename);
                }
                $imageFilename = '';
            }

            // Handle new upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newFilename = $this->handleImageUpload($_FILES['image']);
                if ($newFilename) {
                    if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                        unlink($this->uploadDir . $imageFilename);
                    }
                    $imageFilename = $newFilename;
                }
            }
            
            $data = [
                'name' => Sanitize::input($input['name'] ?? ''),
                'category' => Sanitize::input($input['category'] ?? ''),
                'location' => Sanitize::input($input['location'] ?? ''),
                'description' => Sanitize::input($input['description'] ?? ''),
                'highlights' => Sanitize::input($input['highlights'] ?? ''),
                'schedule' => Sanitize::input($input['schedule'] ?? ''),
                'price' => Sanitize::input($input['price'] ?? ''),
                'includes' => Sanitize::input($input['includes'] ?? ''),
                'status' => Sanitize::input($input['status'] ?? 'Aktif'),
                'image' => $imageFilename
            ];
            
            if ($this->model->update($id, $data)) {
                ActivityLogger::update('Aktivitas', 'Mengubah aktivitas "' . $data['name'] . '"');
                Response::success('Activity updated successfully');
            } else {
                Response::error('Gagal memperbarui kegiatan');
            }
        } catch (Exception $e) {
            error_log("ActivityController::update Error: " . $e->getMessage());
            Response::error('Tidak dapat menyimpan kegiatan: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        try {
            $current = $this->model->getById($id);
            $activityName = $current['name'] ?? 'Aktivitas';
            
            if ($current && isset($current['image']) && $current['image']) {
                $path = $this->uploadDir . $current['image'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            if ($this->model->delete($id)) {
                ActivityLogger::delete('Aktivitas', 'Menghapus aktivitas "' . $activityName . '"');
                Response::success('Activity deleted successfully');
            } else {
                Response::error('Gagal menghapus kegiatan');
            }
        } catch (Exception $e) {
            error_log("ActivityController::delete Error: " . $e->getMessage());
            Response::error('Gagal menghapus kegiatan: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateSortOrder($id) {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $sortOrder = isset($input['sort_order']) ? (int)$input['sort_order'] : 0;
            
            if ($this->model->updateSortOrder($id, $sortOrder)) {
                Response::success('Sort order updated successfully');
            } else {
                Response::error('Gagal memperbarui urutan');
            }
        } catch (Exception $e) {
            error_log("ActivityController::updateSortOrder Error: " . $e->getMessage());
            Response::error('Gagal memperbarui urutan: ' . $e->getMessage(), 500);
        }
    }
    
    public function reorder() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['items']) || !is_array($input['items'])) {
                Response::error('Invalid request: items array required');
                return;
            }
            
            $items = $input['items'];
            $success = true;
            
            foreach ($items as $index => $id) {
                $result = $this->model->updateSortOrder($id, $index + 1);
                if (!$result) {
                    $success = false;
                }
            }
            
            if ($success) {
                Response::success('Items reordered successfully');
            } else {
                Response::error('Failed to reorder some items');
            }
        } catch (Exception $e) {
            error_log("ActivityController::reorder Error: " . $e->getMessage());
            Response::error('Gagal menyusun ulang: ' . $e->getMessage(), 500);
        }
    }
    
    public function deleteMedia($id) {
        try {
            $current = $this->model->getById($id);
            if (!$current) {
                Response::notFound('Activity not found');
                return;
            }

            if (isset($current['image']) && $current['image']) {
                $filePath = $this->uploadDir . $current['image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            if ($this->model->update($id, ['image' => null])) {
                Response::success('Image deleted successfully');
            } else {
                Response::error('Gagal memperbarui data kegiatan');
            }
        } catch (Exception $e) {
            error_log("ActivityController::deleteMedia Error: " . $e->getMessage());
            Response::error('Gagal menghapus media: ' . $e->getMessage(), 500);
        }
    }

    private function handleImageUpload($file) {
        $allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
        
        if (!in_array($file['type'], $allowed)) {
            Response::error('Hanya format WebP, JPG, dan PNG yang diizinkan');
            return false;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            Response::error('Ukuran file maksimal 5MB');
            return false;
        }
        
        $extension = $file['type'] === 'image/webp' ? 'webp' : pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'activity_' . time() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        Response::error('Gagal memindahkan file gambar ke folder tujuan');
        return false;
    }
}

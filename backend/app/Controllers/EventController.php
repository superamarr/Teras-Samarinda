<?php
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/SectionSettingsModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';
require_once __DIR__ . '/../../helpers/ActivityLogger.php';

class EventController {
    private $model;
    private $settingsModel;
    private $uploadDir;
    
    public function __construct() {
        $this->model = new EventModel();
        $this->settingsModel = new SectionSettingsModel('events_settings');
        $this->uploadDir = __DIR__ . '/../../public/uploads/events/';
        
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
            $status = $_GET['status'] ?? null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
            $featured = isset($_GET['featured']) && $_GET['featured'] === 'true';
            
            if ($featured) {
                $data = $this->model->getActiveOrLatest($limit ?? 3);
            } else {
                $data = $this->model->getAll($status, $limit);
            }
            
            foreach ($data as &$item) {
                if (isset($item['image']) && $item['image']) {
                    $item['image'] = $this->normalizeMediaPath($item['image'], 'events');
                }
            }
            
            Response::success('Events retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("EventController::getAll Error: " . $e->getMessage());
            Response::error('Gagal mengambil data event: ' . $e->getMessage(), 500);
        }
    }

    public function getSettings() {
        try {
            $data = $this->settingsModel->get();
            if ($data && isset($data['page_hero_background']) && $data['page_hero_background']) {
                $data['page_hero_background_url'] = $this->normalizeMediaPath($data['page_hero_background'], 'events');
            }
            
            if ($data) {
                $rawItalic = $data['section_title_italic'] ?? '[]';
                $data['section_title_italic'] = is_array($rawItalic) ? $rawItalic : (json_decode($rawItalic, true) ?: []);
            }
            
            Response::success('Event settings retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("EventController::getSettings Error: " . $e->getMessage());
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
                $filename = 'events_hero_' . time() . '.' . $extension;
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
                Response::success('Event settings updated successfully');
            } else {
                Response::error('Failed to update event settings');
            }
        } catch (Exception $e) {
            error_log("EventController::updateSettings Error: " . $e->getMessage());
            Response::error('Gagal memperbarui pengaturan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getById($id) {
        try {
            $data = $this->model->getById($id);
            
            if (!$data) {
                Response::notFound('Event not found');
                return;
            }
            
            if (isset($data['image']) && $data['image']) {
                $data['image'] = $this->normalizeMediaPath($data['image'], 'events');
            }
            
            Response::success('Event retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("EventController::getById Error: " . $e->getMessage());
            Response::error('Gagal mengambil detail event: ' . $e->getMessage(), 500);
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
                'category' => Sanitize::input($input['category'] ?? ''),
                'date' => !empty($input['date']) ? Sanitize::input($input['date']) : null,
                'end_date' => !empty($input['end_date']) ? Sanitize::input($input['end_date']) : null,
                'location' => Sanitize::input($input['location'] ?? ''),
                'description' => Sanitize::input($input['description'] ?? ''),
                'highlights' => Sanitize::input($input['highlights'] ?? ''),
                'ticket_price' => Sanitize::input($input['ticket_price'] ?? ''),
                'status' => Sanitize::input($input['status'] ?? 'Aktif'),
                'is_featured' => isset($input['is_featured']) && in_array($input['is_featured'], [1, '1', 'true', true], true) ? 1 : 0,
                'image' => $imageFilename
            ];
            
            $id = $this->model->create($data);
            if ($id) {
                ActivityLogger::create('Event', 'Menambahkan event baru "' . $data['name'] . '"');
                Response::success('Event created successfully', ['id' => $id], 201);
            } else {
                Response::error('Gagal membuat event di database');
            }
        } catch (Exception $e) {
            error_log("EventController::create Error: " . $e->getMessage());
            Response::error('Tidak dapat membuat event: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id) {
        try {
            $input = $_POST;
            $current = $this->model->getById($id);
            
            if (!$current) {
                Response::notFound('Event not found');
                return;
            }
            
            $imageFilename = $current['image'] ?? '';
            
            // Handle deletion of existing image
            if (($input['delete_image'] ?? '') === 'true') {
                if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                    unlink($this->uploadDir . $imageFilename);
                }
                $imageFilename = '';
            }

            // Handle new upload (replaces old one if present)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newFilename = $this->handleImageUpload($_FILES['image']);
                if (!$newFilename) return;
                
                // Delete old file if we haven't already just deleted it via the flag
                if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                    unlink($this->uploadDir . $imageFilename);
                }
                $imageFilename = $newFilename;
            }
            
            $data = [
                'name' => Sanitize::input($input['name'] ?? ''),
                'category' => Sanitize::input($input['category'] ?? ''),
                'date' => !empty($input['date']) ? Sanitize::input($input['date']) : null,
                'end_date' => !empty($input['end_date']) ? Sanitize::input($input['end_date']) : null,
                'location' => Sanitize::input($input['location'] ?? ''),
                'description' => Sanitize::input($input['description'] ?? ''),
                'highlights' => Sanitize::input($input['highlights'] ?? ''),
                'ticket_price' => Sanitize::input($input['ticket_price'] ?? ''),
                'status' => Sanitize::input($input['status'] ?? 'Aktif'),
                'is_featured' => isset($input['is_featured']) && in_array($input['is_featured'], [1, '1', 'true', true], true) ? 1 : 0,
                'image' => $imageFilename
            ];
            
            if ($this->model->update($id, $data)) {
                ActivityLogger::update('Event', 'Mengubah event "' . $data['name'] . '"');
                Response::success('Event updated successfully');
            } else {
                Response::error('Gagal memperbarui event di database');
            }
        } catch (Exception $e) {
            error_log("EventController::update Error: " . $e->getMessage());
            Response::error('Tidak dapat menyimpan event: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        try {
            $current = $this->model->getById($id);
            
            if ($current && isset($current['image']) && $current['image']) {
                $path = $this->uploadDir . $current['image'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $eventName = $current['name'] ?? 'Event';
            if ($this->model->delete($id)) {
                ActivityLogger::delete('Event', 'Menghapus event "' . $eventName . '"');
                Response::success('Event deleted successfully');
            } else {
                Response::error('Gagal menghapus event dari database');
            }
        } catch (Exception $e) {
            error_log("EventController::delete Error: " . $e->getMessage());
            Response::error('Gagal menghapus event: ' . $e->getMessage(), 500);
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
        $filename = 'event_' . time() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        Response::error('Gagal memindahkan file gambar ke folder tujuan');
        return false;
    }
}

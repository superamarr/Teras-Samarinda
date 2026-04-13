<?php
require_once __DIR__ . '/../models/BookingModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';
require_once __DIR__ . '/../../helpers/ActivityLogger.php';

class BookingController {
    private $model;
    
    public function __construct() {
        $this->model = new BookingModel();
    }
    
    public function getAll() {
        try {
            $status = $_GET['status'] ?? null;
            $data = $this->model->getAll($status);
            Response::success('Bookings retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("BookingController::getAll Error: " . $e->getMessage());
            Response::error('Gagal mengambil data booking', 500);
        }
    }
    
    public function getById($id) {
        try {
            $data = $this->model->getById($id);
            if (!$data) {
                Response::notFound('Booking not found');
                return;
            }
            Response::success('Booking retrieved successfully', $data);
        } catch (Exception $e) {
            error_log("BookingController::getById Error: " . $e->getMessage());
            Response::error('Gagal mengambil detail booking', 500);
        }
    }
    
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            $data = [
                'name' => Sanitize::input($input['name'] ?? ''),
                'email' => Sanitize::email($input['email'] ?? ''),
                'phone' => Sanitize::input($input['phone'] ?? ''),
                'event_name' => Sanitize::input($input['event_name'] ?? ''),
                'location' => Sanitize::input($input['location'] ?? ''),
                'guest_count' => Sanitize::integer($input['guest_count'] ?? 1),
                'booking_date' => Sanitize::input($input['booking_date'] ?? ''),
                'notes' => Sanitize::input($input['notes'] ?? ''),
                'status' => Sanitize::input($input['status'] ?? 'Pending')
            ];
            
            $id = $this->model->create($data);
            if ($id) {
                ActivityLogger::create('Booking', 'Menambahkan booking baru untuk "' . $data['event_name'] . '"');
                Response::success('Booking created successfully', ['id' => $id], 201);
            } else {
                Response::error('Gagal membuat data booking');
            }
        } catch (Exception $e) {
            error_log("BookingController::create Error: " . $e->getMessage());
            Response::error('Gagal membuat booking: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id) {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            $data = [];
            if (isset($input['status'])) $data['status'] = Sanitize::input($input['status']);
            if (isset($input['name'])) $data['name'] = Sanitize::input($input['name']);
            if (isset($input['email'])) $data['email'] = Sanitize::email($input['email']);
            if (isset($input['phone'])) $data['phone'] = Sanitize::input($input['phone']);
            if (isset($input['booking_date'])) $data['booking_date'] = Sanitize::input($input['booking_date']);
            if (isset($input['event_name'])) $data['event_name'] = Sanitize::input($input['event_name']);
            if (isset($input['location'])) $data['location'] = Sanitize::input($input['location']);
            
            if (empty($data)) {
                Response::error('No data to update');
                return;
            }
            
            if ($this->model->update($id, $data)) {
                ActivityLogger::update('Booking', 'Mengubah status booking untuk "' . ($data['event_name'] ?? 'Event') . '"');
                Response::success('Booking updated successfully');
            } else {
                Response::error('Failed to update booking');
            }
        } catch (Exception $e) {
            error_log("BookingController::update Error: " . $e->getMessage());
            Response::error('Gagal memperbarui booking: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        try {
            $current = $this->model->getById($id);
            $eventName = $current['event_name'] ?? 'Event';
            if ($this->model->delete($id)) {
                ActivityLogger::delete('Booking', 'Menghapus booking untuk "' . $eventName . '"');
                Response::success('Booking deleted successfully');
            } else {
                Response::error('Gagal menghapus booking');
            }
        } catch (Exception $e) {
            error_log("BookingController::delete Error: " . $e->getMessage());
            Response::error('Gagal menghapus booking', 500);
        }
    }
}
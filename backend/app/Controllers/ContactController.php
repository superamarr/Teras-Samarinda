<?php
require_once __DIR__ . '/../models/ContactModel.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/Sanitize.php';

class ContactController {
    private $model;
    
    public function __construct() {
        $this->model = new ContactModel();
    }
    
    public function get() {
        try {
            $data = $this->model->get();
            
            if (!$data) {
                return Response::json([
                    'success' => true,
                    'data' => [
                        'title' => 'Kunjungi Kami',
                        'description' => 'Berlokasi strategis di pusat kota Samarinda, Teras Samarinda sangat mudah diakses dengan kendaraan pribadi maupun transportasi umum.',
                        'email' => '',
                        'phone' => '',
                        'whatsapp' => '',
                        'address' => '',
                        'facebook' => '',
                        'instagram' => '',
                        'youtube' => '',
                        'mapEmbed' => '',
                        'operatingHours' => '',
                        'cta_text' => 'BOOKING EVENT',
                        'cta_link' => 'https://maps.app.goo.gl/96e5ea78007505d1'
                    ]
                ]);
            }
            
            $result = [
                'title' => $data['title'] ?? 'Kunjungi Kami',
                'description' => $data['description'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'whatsapp' => $data['whatsapp'] ?? '',
                'address' => $data['address'] ?? '',
                'facebook' => $data['facebook'] ?? '',
                'instagram' => $data['instagram'] ?? '',
                'youtube' => $data['youtube'] ?? '',
                'mapEmbed' => $data['map_embed'] ?? '',
                'operatingHours' => $data['operating_hours'] ?? '',
                'cta_text' => $data['cta_text'] ?? 'BOOKING EVENT',
                'cta_link' => $data['cta_link'] ?? 'https://maps.app.goo.gl/96e5ea78007505d1',
                'title_italic' => []
            ];

            $rawItalic = $data['title_italic'] ?? '[]';
            $result['title_italic'] = is_array($rawItalic) ? $rawItalic : (json_decode($rawItalic, true) ?: []);
            
            Response::success('Data retrieved successfully', $result);
        } catch (Exception $e) {
            error_log("ContactController::get Error: " . $e->getMessage());
            Response::error('Gagal mengambil data kontak', 500);
        }
    }
    
    public function update() {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            // XSS Security: map_embed allows iframe but other fields are strictly sanitized
            $mapEmbed = $input['mapEmbed'] ?? '';
            if (!empty($mapEmbed)) {
                // Basic cleanup for iframe tags only, remove potential dangerous attributes if any
                $mapEmbed = strip_tags($mapEmbed, '<iframe>');
                // Protection against JS event handlers in iframe string
                $mapEmbed = preg_replace('/on\w+="[^"]*"/i', '', $mapEmbed);
                $mapEmbed = preg_replace('/javascript:/i', '', $mapEmbed);
            }

            $data = [
                'title' => Sanitize::input($input['title'] ?? 'Kunjungi Kami'),
                'description' => Sanitize::input($input['description'] ?? ''),
                'email' => Sanitize::email($input['email'] ?? ''),
                'phone' => Sanitize::input($input['phone'] ?? ''),
                'whatsapp' => Sanitize::input($input['whatsapp'] ?? ''),
                'address' => Sanitize::input($input['address'] ?? ''),
                'facebook' => Sanitize::url($input['facebook'] ?? ''),
                'instagram' => Sanitize::url($input['instagram'] ?? ''),
                'youtube' => Sanitize::url($input['youtube'] ?? ''),
                'map_embed' => $mapEmbed,
                'operating_hours' => Sanitize::input($input['operatingHours'] ?? ''),
                'cta_text' => Sanitize::input($input['cta_text'] ?? 'BOOKING EVENT'),
                'cta_link' => Sanitize::input($input['cta_link'] ?? 'https://maps.app.goo.gl/96e5ea78007505d1'),
                'title_italic' => $input['title_italic'] ?? '[]',
            ];

            if (is_array($data['title_italic'])) {
                $data['title_italic'] = json_encode($data['title_italic']);
            }
            
            if ($this->model->update($data)) {
                Response::success('Contact updated successfully');
            } else {
                Response::error('Failed to update contact');
            }
        } catch (Exception $e) {
            error_log("ContactController::update Error: " . $e->getMessage());
            Response::error('Gagal memperbarui data kontak: ' . $e->getMessage(), 500);
        }
    }
}

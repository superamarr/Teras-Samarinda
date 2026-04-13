<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/Response.php';

class AnalyticsController {
    private $db;

    public function __construct() {
        date_default_timezone_set('Asia/Makassar');
        $this->db = Database::getInstance();
    }

    public function getOverview() {
        $period = $_GET['period'] ?? 'all';
        error_log("GET Analytics Overview - Period: " . $period);
        try {
            $data = $this->calculateMetrics($period);
            Response::json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            error_log("Analytics Error: " . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Failed to get analytics data'], 500);
        }
    }

    private function calculateMetrics($period) {
        $now = time();
        $startDate = "1970-01-01";
        $prevStartDate = "1970-01-01";
        $prevEndDate = "1970-01-01";
        
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
                $prevStartDate = date('Y-m-d H:i:s', strtotime('-14 days'));
                $prevEndDate = $startDate;
                break;
            case 'month':
                $startDate = date('Y-m-d H:i:s', strtotime('-30 days'));
                $prevStartDate = date('Y-m-d H:i:s', strtotime('-60 days'));
                $prevEndDate = $startDate;
                break;
            case 'year':
                $startDate = date('Y-m-d H:i:s', strtotime('-365 days'));
                $prevStartDate = date('Y-m-d H:i:s', strtotime('-730 days'));
                $prevEndDate = $startDate;
                break;
            case 'all':
            default:
                $startDate = "2000-01-01 00:00:00";
                $prevStartDate = null;
                break;
        }

        // Current Period KPIs
        $kpi = $this->getKpi($startDate, date('Y-m-d H:i:s'));
        
        // Previous Period KPIs (for trend)
        if ($prevStartDate) {
            $prevKpi = $this->getKpi($prevStartDate, $prevEndDate);
        } else {
            $prevKpi = $kpi; // No trend possible
        }

        // Calculate Trends
        $trends = [
            'pageViewsTrend' => $this->calcTrend($kpi['pageViews'], $prevKpi['pageViews']),
            'durationTrend' => $this->calcTrend($kpi['durationValue'], $prevKpi['durationValue']),
            'bounceRateTrend' => $this->calcTrend($kpi['bounceRateValue'], $prevKpi['bounceRateValue']),
            'bookingTrend' => $this->calcTrend($kpi['booking'], $prevKpi['booking']),
            'eventsTrend' => $this->calcTrend($kpi['events'], $prevKpi['events']),
            'facilitiesTrend' => $this->calcTrend($kpi['facilities'], $prevKpi['facilities'])
        ];

        // Format KPI
        $kpiData = [
            'pageViews' => number_format($kpi['pageViews']),
            'pageViewsTrend' => $trends['pageViewsTrend'],
            'duration' => $this->formatDuration($kpi['durationValue']),
            'durationTrend' => $trends['durationTrend'],
            'bounceRate' => round($kpi['bounceRateValue']) . '%',
            'bounceRateTrend' => $trends['bounceRateTrend'],
            'booking' => number_format($kpi['booking']),
            'bookingTrend' => $trends['bookingTrend'],
            'events' => number_format($kpi['events']),
            'eventsTrend' => $trends['eventsTrend'],
            'facilities' => number_format($kpi['facilities']),
            'facilitiesTrend' => $trends['facilitiesTrend']
        ];

        // Chart Data Extraction
        $chartData = $this->getChartDataOverTime($period, $startDate);
        
        // Donut Content (Top pages)
        $donutContent = $this->getContentProportions($startDate);
        
        // Status Booking (Removing Pending as requested)
        $donutBooking = $this->getBookingStatus($startDate);

        // Top Content Details
        $topContentData = $this->getTopContent($startDate);

        return [
            'kpiData' => $kpiData,
            'chartData' => $chartData,
            'donutContentData' => $donutContent,
            'donutBookingData' => $donutBooking,
            'contentData' => [
                'sections' => array_column($donutContent['raw'], 'label'),
                'views' => array_column($donutContent['raw'], 'value')
            ],
            'topContentData' => $topContentData
        ];
    }

    private function getKpi($start, $end) {
        $viewsQ = $this->db->selectOne("SELECT COUNT(*) as cnt, AVG(duration_seconds) as avg_dur, AVG(is_bounce) as avg_bounce FROM page_views WHERE viewed_at >= ? AND viewed_at <= ?", [$start, $end]);
        $bookingQ = $this->db->selectOne("SELECT COUNT(*) as cnt FROM bookings WHERE created_at >= ? AND created_at <= ?", [$start, $end]);
        $eventsQ = $this->db->selectOne("SELECT COUNT(*) as cnt FROM events WHERE created_at >= ? AND created_at <= ?", [$start, $end]);
        $facilitiesQ = $this->db->selectOne("SELECT COUNT(*) as cnt FROM facilities WHERE created_at >= ? AND created_at <= ?", [$start, $end]);

        return [
            'pageViews' => $viewsQ['cnt'] ?: 0,
            'durationValue' => $viewsQ['avg_dur'] ?: 0,
            'bounceRateValue' => ($viewsQ['avg_bounce'] ?? 0) * 100,
            'booking' => $bookingQ['cnt'] ?: 0,
            'events' => $eventsQ['cnt'] ?: 0,
            'facilities' => $facilitiesQ['cnt'] ?: 0
        ];
    }

    private function calcTrend($current, $prev) {
        if ($prev == 0) return $current > 0 ? '+100%' : '0%';
        $diff = (($current - $prev) / $prev) * 100;
        $sign = $diff > 0 ? '+' : '';
        return $sign . round($diff) . '%';
    }

    private function formatDuration($seconds) {
        $seconds = round($seconds);
        $m = floor($seconds / 60);
        $s = $seconds % 60;
        return "{$m}m {$s}s";
    }

    private function getChartDataOverTime($period, $startDate) {
        // Simple logic: return some predefined points or do dynamic grouping.
        // For simplicity, we will group by year if 'all', by month if 'year', by week (segments) if 'month', by day if 'week'
        $categories = [];
        $pageViews = [];
        $bookings = [];
        
        if ($period == 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $categories[] = date('D', strtotime($d));
                $v = $this->db->selectOne("SELECT COUNT(*) as c FROM page_views WHERE DATE(viewed_at) = ?", [$d])['c'];
                $b = $this->db->selectOne("SELECT COUNT(*) as c FROM bookings WHERE DATE(created_at) = ?", [$d])['c'];
                $pageViews[] = $v;
                $bookings[] = $b;
            }
        } elseif ($period == 'month') {
            $currentDay = (int)date('j');
            $currentWeek = (int)ceil($currentDay / 7);
            $daysInMonth = (int)date('t');
            
            for ($w = 1; $w <= $currentWeek; $w++) {
                $categories[] = "Minggu " . $w;
                $weekStart = ($w - 1) * 7 + 1;
                $weekEnd = min($w * 7, $daysInMonth);
                
                $start = date('Y-m-') . sprintf('%02d', $weekStart) . ' 00:00:00';
                $end = date('Y-m-') . sprintf('%02d', $weekEnd) . ' 23:59:59';
                
                $v = $this->db->selectOne("SELECT COUNT(*) as c FROM page_views WHERE viewed_at >= ? AND viewed_at <= ?", [$start, $end])['c'];
                $b = $this->db->selectOne("SELECT COUNT(*) as c FROM bookings WHERE created_at >= ? AND created_at <= ?", [$start, $end])['c'];
                $pageViews[] = (int)$v;
                $bookings[] = (int)$b;
            }
        } elseif ($period == 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $d = mktime(0, 0, 0, date("m") - $i, 1, date("Y"));
                $categories[] = date('M', $d);
                $m = date('m', $d);
                $y = date('Y', $d);
                $v = $this->db->selectOne("SELECT COUNT(*) as c FROM page_views WHERE MONTH(viewed_at) = ? AND YEAR(viewed_at) = ?", [$m, $y])['c'];
                $b = $this->db->selectOne("SELECT COUNT(*) as c FROM bookings WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?", [$m, $y])['c'];
                $pageViews[] = $v;
                $bookings[] = $b;
            }
        } else {
            // all
            for ($i = 4; $i >= 0; $i--) {
                $y = date("Y") - $i;
                $categories[] = (string)$y;
                $v = $this->db->selectOne("SELECT COUNT(*) as c FROM page_views WHERE YEAR(viewed_at) = ?", [$y])['c'];
                $b = $this->db->selectOne("SELECT COUNT(*) as c FROM bookings WHERE YEAR(created_at) = ?", [$y])['c'];
                $pageViews[] = $v;
                $bookings[] = $b;
            }
        }

        return [
            'categories' => $categories,
            'pageViews' => $pageViews,
            'booking' => $bookings
        ];
    }

    private function getContentProportions($startDate) {
        $rows = $this->db->select("SELECT page_url as label, COUNT(*) as value FROM page_views WHERE viewed_at >= ? GROUP BY page_url ORDER BY value DESC", [$startDate]);
        $series = [];
        $labels = [];
        foreach ($rows as $r) {
            $series[] = $r['value'];
            $labels[] = str_replace('Halaman ', '', $r['label']);
        }
        return [
            'series' => $series,
            'labels' => $labels,
            'raw' => $rows
        ];
    }

    private function getBookingStatus($startDate) {
        $rows = $this->db->select("SELECT status, COUNT(*) as cnt FROM bookings WHERE created_at >= ? AND LOWER(status) != 'pending' GROUP BY status", [$startDate]);
        
        $order = ['Confirmed', 'Completed', 'Cancelled']; 
        $map = [];
        foreach ($rows as $r) {
            $map[$r['status']] = $r['cnt'];
        }
        
        $series = [];
        $labels = [];
        foreach ($order as $st) {
            $series[] = $map[$st] ?? 0;
            $labels[] = ucfirst($st);
        }
        
        return [
            'series' => $series,
            'labels' => $labels
        ];
    }

    private function getTopContent($startDate) {
        $rows = $this->db->select("SELECT page_url, COUNT(*) as views, AVG(duration_seconds) as avg_time FROM page_views WHERE viewed_at >= ? GROUP BY page_url ORDER BY views DESC LIMIT 5", [$startDate]);
        $rez = [];
        foreach ($rows as $r) {
            $rez[] = [
                'name' => $r['page_url'],
                'type' => 'Page',
                'views' => number_format($r['views']),
                'avgTime' => $this->formatDuration($r['avg_time']),
                'status' => 'Aktif'
            ];
        }
        // If we want events & facilities explicitly mixed in, we can. But this works accurately for "top content".
        if (empty($rez)) {
            $rez[] = ['name' => 'Data belum cukup', 'type' => '-', 'views' => '-', 'avgTime' => '-', 'status' => '-'];
        }
        return $rez;
    }
}

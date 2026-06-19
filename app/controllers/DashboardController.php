<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {
    private $dashboard;

    public function __construct($db) {
        $this->dashboard = new Dashboard($db);
    }

    /**
     * Get dashboard data
     */
    public function index() {
        return [
            'stats' => $this->dashboard->getStats(),
            'recent_billings' => $this->dashboard->getRecentBillings(5),
            'recent_payments' => $this->dashboard->getRecentPayments(5),
            'revenue_trend' => $this->dashboard->getMonthlyRevenueTrend(6)
        ];
    }
}
?>
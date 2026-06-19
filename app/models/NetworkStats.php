<?php
/**
 * Network Statistics Model
 */

class NetworkStats {
    private $conn;
    private $table = 'network_stats';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Log network stats
     */
    public function logStats($customer_id, $olt_port_id, $mikrotik_id, $download_speed, $upload_speed, $total_data_used, $packet_loss, $latency) {
        $query = "INSERT INTO " . $this->table . "
                  (customer_id, olt_port_id, mikrotik_id, download_speed, upload_speed, total_data_used, packet_loss, latency)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiddddii",
            $customer_id,
            $olt_port_id,
            $mikrotik_id,
            $download_speed,
            $upload_speed,
            $total_data_used,
            $packet_loss,
            $latency
        );

        return $stmt->execute();
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats($customer_id, $days = 7) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE customer_id = ? AND status_check_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
                  ORDER BY status_check_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $customer_id, $days);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get latest stats
     */
    public function getLatestStats($customer_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE customer_id = ?
                  ORDER BY status_check_date DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get average speed
     */
    public function getAverageSpeed($customer_id, $days = 30) {
        $query = "SELECT AVG(download_speed) as avg_dl, AVG(upload_speed) as avg_ul
                  FROM " . $this->table . "
                  WHERE customer_id = ? AND status_check_date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $customer_id, $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
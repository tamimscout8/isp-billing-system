-- Add Mikrotik Routers Table
CREATE TABLE IF NOT EXISTS mikrotiks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    ip_address VARCHAR(15) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    port INT DEFAULT 8728,
    model VARCHAR(50),
    serial_number VARCHAR(100),
    location VARCHAR(100),
    status ENUM('online', 'offline', 'maintenance') DEFAULT 'offline',
    api_enabled BOOLEAN DEFAULT 0,
    last_sync TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add OLT (Optical Line Terminal) Table
CREATE TABLE IF NOT EXISTS olts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    ip_address VARCHAR(15) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    port INT DEFAULT 23,
    model VARCHAR(50),
    serial_number VARCHAR(100),
    vendor VARCHAR(50),
    ports_count INT DEFAULT 32,
    location VARCHAR(100),
    status ENUM('online', 'offline', 'maintenance') DEFAULT 'offline',
    last_sync TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add OLT Ports Table
CREATE TABLE IF NOT EXISTS olt_ports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    olt_id INT NOT NULL,
    port_number INT NOT NULL,
    port_name VARCHAR(50),
    status ENUM('active', 'inactive', 'error') DEFAULT 'inactive',
    customer_id INT,
    onu_sn VARCHAR(100),
    signal_strength INT,
    rx_power DECIMAL(5, 2),
    tx_power DECIMAL(5, 2),
    distance_km DECIMAL(5, 2),
    last_checked TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (olt_id) REFERENCES olts(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    UNIQUE KEY unique_olt_port (olt_id, port_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add Mikrotik Queue Table
CREATE TABLE IF NOT EXISTS mikrotik_queues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mikrotik_id INT NOT NULL,
    customer_id INT NOT NULL,
    queue_name VARCHAR(100),
    max_limit_dl INT,
    max_limit_ul INT,
    target_address VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mikrotik_id) REFERENCES mikrotiks(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add Network Statistics Table
CREATE TABLE IF NOT EXISTS network_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    olt_port_id INT,
    mikrotik_id INT,
    download_speed DECIMAL(10, 2),
    upload_speed DECIMAL(10, 2),
    total_data_used DECIMAL(15, 2),
    packet_loss DECIMAL(5, 2),
    latency INT,
    status_check_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (olt_port_id) REFERENCES olt_ports(id) ON DELETE SET NULL,
    FOREIGN KEY (mikrotik_id) REFERENCES mikrotiks(id) ON DELETE SET NULL,
    INDEX idx_customer_date (customer_id, status_check_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add Service Management Table
CREATE TABLE IF NOT EXISTS service_management (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    olt_port_id INT,
    mikrotik_id INT,
    service_type ENUM('pppoe', 'hotspot', 'bridge') DEFAULT 'pppoe',
    username VARCHAR(100),
    password VARCHAR(100),
    ip_address VARCHAR(15),
    mac_address VARCHAR(17),
    status ENUM('active', 'suspended', 'blocked', 'inactive') DEFAULT 'inactive',
    sync_status ENUM('synced', 'pending', 'error') DEFAULT 'pending',
    last_sync TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (olt_port_id) REFERENCES olt_ports(id) ON DELETE SET NULL,
    FOREIGN KEY (mikrotik_id) REFERENCES mikrotiks(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add Equipment Log Table
CREATE TABLE IF NOT EXISTS equipment_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_type ENUM('mikrotik', 'olt') NOT NULL,
    equipment_id INT NOT NULL,
    log_type ENUM('connection', 'error', 'maintenance', 'sync') NOT NULL,
    message TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    resolved BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Indexes for better performance
CREATE INDEX idx_mikrotik_status ON mikrotiks(status);
CREATE INDEX idx_olt_status ON olts(status);
CREATE INDEX idx_olt_port_customer ON olt_ports(customer_id);
CREATE INDEX idx_olt_port_status ON olt_ports(status);
CREATE INDEX idx_service_customer ON service_management(customer_id);
CREATE INDEX idx_service_status ON service_management(status);
CREATE INDEX idx_equipment_log_type ON equipment_logs(equipment_type, log_type);

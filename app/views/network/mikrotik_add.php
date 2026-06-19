<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>➕ Add Mikrotik Router</h2>

<form method="POST" id="mikrotik-form">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <div class="form-group">
                <label for="name">Router Name *</label>
                <input type="text" id="name" name="name" placeholder="e.g., MikroTik-Main" required>
            </div>

            <div class="form-group">
                <label for="ip_address">IP Address *</label>
                <input type="text" id="ip_address" name="ip_address" placeholder="192.168.1.1" required>
            </div>

            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" placeholder="admin" required>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>

        <div>
            <div class="form-group">
                <label for="port">API Port</label>
                <input type="number" id="port" name="port" value="8728" placeholder="8728">
            </div>

            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" placeholder="e.g., RB3011">
            </div>

            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number" placeholder="e.g., 1234567890">
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Main Office">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="api_enabled" name="api_enabled"> Enable API Connection
                </label>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-success">Add Mikrotik</button>
        <a href="?page=network&section=mikrotik" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>

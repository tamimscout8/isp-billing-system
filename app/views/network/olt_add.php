<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>➕ Add OLT (Optical Line Terminal)</h2>

<form method="POST" id="olt-form">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <div class="form-group">
                <label for="name">OLT Name *</label>
                <input type="text" id="name" name="name" placeholder="e.g., OLT-Main" required>
            </div>

            <div class="form-group">
                <label for="ip_address">IP Address *</label>
                <input type="text" id="ip_address" name="ip_address" placeholder="192.168.100.1" required>
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
                <label for="port">Telnet Port</label>
                <input type="number" id="port" name="port" value="23" placeholder="23">
            </div>

            <div class="form-group">
                <label for="vendor">Vendor *</label>
                <select id="vendor" name="vendor" required>
                    <option value="">-- Select Vendor --</option>
                    <option value="HUAWEI">HUAWEI</option>
                    <option value="NOKIA">NOKIA</option>
                    <option value="ERICSSON">ERICSSON</option>
                    <option value="ZTE">ZTE</option>
                    <option value="CIENA">CIENA</option>
                </select>
            </div>

            <div class="form-group">
                <label for="model">Model *</label>
                <input type="text" id="model" name="model" placeholder="e.g., MA5608T" required>
            </div>

            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number">
            </div>

            <div class="form-group">
                <label for="ports_count">Ports Count</label>
                <input type="number" id="ports_count" name="ports_count" value="32" min="1">
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Exchange-01">
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-success">Add OLT</button>
        <a href="?page=network&section=olt" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>

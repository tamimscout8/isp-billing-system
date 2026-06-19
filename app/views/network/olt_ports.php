<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>🛰️ OLT Ports Management</h2>

<?php if($data['olt']): ?>

<div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem;">
    <h3><?php echo htmlspecialchars($data['olt']['name']); ?> - Port Management</h3>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1.5rem 0;">
        <div class="stat-card">
            <div class="stat-label">Total Ports</div>
            <div class="stat-value"><?php echo $data['olt']['ports_count']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Ports</div>
            <div class="stat-value" style="color: #28a745;"><?php echo $data['stats']['active_ports']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Available Ports</div>
            <div class="stat-value" style="color: #667eea;"><?php echo $data['stats']['available_ports']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Utilization</div>
            <div class="stat-value" style="color: #ff9800;"><?php echo $data['stats']['utilization']; ?>%</div>
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Port #</th>
            <th>Name</th>
            <th>Status</th>
            <th>Customer</th>
            <th>ONU SN</th>
            <th>Signal</th>
            <th>RX Power (dBm)</th>
            <th>TX Power (dBm)</th>
            <th>Distance (km)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $ports = $data['ports'];
        if($ports->num_rows > 0) {
            while($port = $ports->fetch_assoc()): 
        ?>
            <tr>
                <td><strong><?php echo $port['port_number']; ?></strong></td>
                <td><?php echo htmlspecialchars($port['port_name']); ?></td>
                <td>
                    <span class="badge badge-<?php echo ($port['status'] === 'active' ? 'success' : ($port['status'] === 'error' ? 'danger' : 'info')); ?>">
                        <?php echo ucfirst($port['status']); ?>
                    </span>
                </td>
                <td><?php echo $port['customer_name'] ? htmlspecialchars($port['customer_name']) : '-'; ?></td>
                <td><?php echo $port['onu_sn'] ? $port['onu_sn'] : '-'; ?></td>
                <td><?php echo $port['signal_strength'] ? $port['signal_strength'] . '%' : '-'; ?></td>
                <td><?php echo $port['rx_power'] ? $port['rx_power'] : '-'; ?></td>
                <td><?php echo $port['tx_power'] ? $port['tx_power'] : '-'; ?></td>
                <td><?php echo $port['distance_km'] ? $port['distance_km'] : '-'; ?></td>
                <td>
                    <?php if($port['status'] === 'inactive'): ?>
                        <a href="?page=network&section=olt&action=assign&port_id=<?php echo $port['id']; ?>" class="btn btn-primary btn-small">Assign</a>
                    <?php else: ?>
                        <a href="?page=network&section=olt&action=unassign&port_id=<?php echo $port['id']; ?>" class="btn btn-danger btn-small" onclick="return confirm('Unassign customer?')">Unassign</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php 
            endwhile;
        } else {
        ?>
            <tr><td colspan="10" style="text-align: center; padding: 2rem;">No ports found</td></tr>
        <?php } ?>
    </tbody>
</table>

<?php else: ?>
<div class="alert alert-danger">OLT not found</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>🌐 Mikrotik Management</h2>

<div style="margin-bottom: 1.5rem;">
    <a href="?page=network&section=mikrotik&action=add" class="btn btn-primary">+ Add Mikrotik Router</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>IP Address</th>
            <th>Model</th>
            <th>Location</th>
            <th>Status</th>
            <th>API</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if($data->num_rows > 0) {
            while($mikrotik = $data->fetch_assoc()): 
        ?>
            <tr>
                <td>#<?php echo $mikrotik['id']; ?></td>
                <td><?php echo htmlspecialchars($mikrotik['name']); ?></td>
                <td><?php echo $mikrotik['ip_address']; ?></td>
                <td><?php echo htmlspecialchars($mikrotik['model']); ?></td>
                <td><?php echo htmlspecialchars($mikrotik['location']); ?></td>
                <td>
                    <span class="badge badge-<?php echo ($mikrotik['status'] === 'online' ? 'success' : 'danger'); ?>">
                        <?php echo ucfirst($mikrotik['status']); ?>
                    </span>
                </td>
                <td>
                    <?php echo $mikrotik['api_enabled'] ? '✓ Enabled' : '✗ Disabled'; ?>
                </td>
                <td>
                    <a href="?page=network&section=mikrotik&action=view&id=<?php echo $mikrotik['id']; ?>" class="btn btn-primary btn-small">View</a>
                    <a href="?page=network&section=mikrotik&action=test&id=<?php echo $mikrotik['id']; ?>" class="btn btn-info btn-small">Test</a>
                </td>
            </tr>
        <?php 
            endwhile;
        } else {
        ?>
            <tr><td colspan="8" style="text-align: center; padding: 2rem;">No mikrotiks found</td></tr>
        <?php } ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>

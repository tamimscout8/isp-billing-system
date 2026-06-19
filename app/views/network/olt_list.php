<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>🛰️ OLT Management</h2>

<div style="margin-bottom: 1.5rem;">
    <a href="?page=network&section=olt&action=add" class="btn btn-primary">+ Add OLT</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>IP Address</th>
            <th>Vendor</th>
            <th>Model</th>
            <th>Ports</th>
            <th>Location</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if($data->num_rows > 0) {
            while($olt = $data->fetch_assoc()): 
        ?>
            <tr>
                <td>#<?php echo $olt['id']; ?></td>
                <td><?php echo htmlspecialchars($olt['name']); ?></td>
                <td><?php echo $olt['ip_address']; ?></td>
                <td><?php echo htmlspecialchars($olt['vendor']); ?></td>
                <td><?php echo htmlspecialchars($olt['model']); ?></td>
                <td><?php echo $olt['ports_count']; ?></td>
                <td><?php echo htmlspecialchars($olt['location']); ?></td>
                <td>
                    <span class="badge badge-<?php echo ($olt['status'] === 'online' ? 'success' : 'danger'); ?>">
                        <?php echo ucfirst($olt['status']); ?>
                    </span>
                </td>
                <td>
                    <a href="?page=network&section=olt&action=view&id=<?php echo $olt['id']; ?>" class="btn btn-primary btn-small">Manage</a>
                    <a href="?page=network&section=olt&action=test&id=<?php echo $olt['id']; ?>" class="btn btn-info btn-small">Test</a>
                </td>
            </tr>
        <?php 
            endwhile;
        } else {
        ?>
            <tr><td colspan="9" style="text-align: center; padding: 2rem;">No OLTs found</td></tr>
        <?php } ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>

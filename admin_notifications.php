<?php
include 'connect.php';

$notif_query = mysqli_query($conn, "SELECT * FROM lost_found WHERE status='Pending'");
$notif_count = mysqli_num_rows($notif_query);
?>

<h3>Pending Reports</h3>
<?php if ($notif_count == 0) { echo "<p>No new reports.</p>"; } else { ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Report ID</th>
            <th>Student Name</th>
            <th>ID Number</th>
            <th>Reported On</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($notif = mysqli_fetch_assoc($notif_query)) { ?>
            <tr>
                <td><?php echo $notif['id']; ?></td>
                <td><?php echo $notif['student_name']; ?></td>
                <td><?php echo $notif['id_number']; ?></td>
                <td><?php echo $notif['reported_at']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<?php
include 'connect.php';

$total_reports_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM lost_found");
$total_reports = $total_reports_query ? mysqli_fetch_assoc($total_reports_query)['count'] : 0;

$resolved_reports_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM lost_found WHERE status='Resolved'");
$resolved_reports = $resolved_reports_query ? mysqli_fetch_assoc($resolved_reports_query)['count'] : 0;

$pending_reports_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM lost_found WHERE status='Pending'");
$pending_reports = $pending_reports_query ? mysqli_fetch_assoc($pending_reports_query)['count'] : 0;
?>

<h3>Analytics</h3>
<div class="row">
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <h4>Total Reports</h4>
            <p><?php echo $total_reports; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <h4>Resolved Cases</h4>
            <p><?php echo $resolved_reports; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <h4>Pending Cases</h4>
            <p><?php echo $pending_reports; ?></p>
        </div>
    </div>
</div>

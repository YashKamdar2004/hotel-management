<?php
    require('inc/essentials.php');
    require('inc/db_config.php');
    adminLogin(); 

    // Fetch summary statistics
    $total_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM user_cred"))['count'];
    $total_rooms = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM rooms WHERE status = 1"))['count'];
    $total_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings"))['count'];
    $total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(amount) as total FROM payments WHERE status = 'success' AND (refund_status IS NULL OR refund_status != 'refunded')"))['total'] ?? 0;
    $pending_refunds = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'refund_requested'"))['count'];
    $total_reviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM reviews WHERE review_status = 'approved'"))['count'];

    // Booking statistics
    $pending_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'"))['count'];
    $confirmed_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'confirmed'"))['count'];
    $completed_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'completed'"))['count'];
    $cancelled_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'cancelled'"))['count'];

    // Monthly revenue for last 6 months - Generate all 6 months first
    $months = [];
    $revenues = [];
    
    // Generate last 6 months
    for($i = 5; $i >= 0; $i--) {
        $month_date = date('Y-m-01', strtotime("-$i months"));
        $month_label = date('M Y', strtotime($month_date));
        $months[] = $month_label;
        
        // Get revenue for this specific month
        $year = date('Y', strtotime($month_date));
        $month = date('m', strtotime($month_date));
        
        $revenue_query = "SELECT COALESCE(SUM(b.total_amount), 0) as revenue 
                          FROM bookings b
                          WHERE b.payment_status = 'paid'
                          AND YEAR(b.created_at) = $year
                          AND MONTH(b.created_at) = $month";
        
        $revenue_result = mysqli_query($con, $revenue_query);
        $revenue_row = mysqli_fetch_assoc($revenue_result);
        $revenues[] = (float)$revenue_row['revenue'];
    }

    // Recent bookings
    $recent_bookings_query = "SELECT b.id, u.name as user_name, r.name as room_name, b.total_amount, 
                              b.booking_status, b.payment_status, b.created_at
                              FROM bookings b
                              INNER JOIN user_cred u ON b.user_id = u.id
                              INNER JOIN rooms r ON b.room_id = r.id
                              ORDER BY b.created_at DESC
                              LIMIT 5";
    $recent_bookings = mysqli_query($con, $recent_bookings_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    
    <?php require('inc/header.php');?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4">
                <h3 class="mb-4">DASHBOARD</h3>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-primary shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Users</h6>
                                        <h2 class="mb-0"><?php echo $total_users; ?></h2>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-info shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Rooms</h6>
                                        <h2 class="mb-0"><?php echo $total_rooms; ?></h2>
                                    </div>
                                    <i class="bi bi-door-open fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-warning shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Bookings</h6>
                                        <h2 class="mb-0"><?php echo $total_bookings; ?></h2>
                                    </div>
                                    <i class="bi bi-calendar-check fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-success shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Revenue</h6>
                                        <h2 class="mb-0">₹<?php echo number_format($total_revenue, 2); ?></h2>
                                    </div>
                                    <i class="bi bi-currency-rupee fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-danger shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Pending Refunds</h6>
                                        <h2 class="mb-0"><?php echo $pending_refunds; ?></h2>
                                    </div>
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-secondary shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Reviews</h6>
                                        <h2 class="mb-0"><?php echo $total_reviews; ?></h2>
                                    </div>
                                    <i class="bi bi-star fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Statistics -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Booking Statistics</h5>
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted">Pending</h6>
                                            <h3 class="text-warning"><?php echo $pending_bookings; ?></h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted">Confirmed</h6>
                                            <h3 class="text-info"><?php echo $confirmed_bookings; ?></h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted">Completed</h6>
                                            <h3 class="text-success"><?php echo $completed_bookings; ?></h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted">Cancelled</h6>
                                            <h3 class="text-danger"><?php echo $cancelled_bookings; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Booking Overview</h5>
                                <canvas id="bookingPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Monthly Revenue (Last 6 Months)</h5>
                        <canvas id="revenueChart" height="80"></canvas>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Recent Bookings</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>User Name</th>
                                        <th>Room Name</th>
                                        <th>Amount</th>
                                        <th>Booking Status</th>
                                        <th>Payment Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if(mysqli_num_rows($recent_bookings) > 0) {
                                        while($booking = mysqli_fetch_assoc($recent_bookings)) {
                                            $booking_badge = '';
                                            switch($booking['booking_status']) {
                                                case 'pending': $booking_badge = 'bg-warning'; break;
                                                case 'confirmed': $booking_badge = 'bg-info'; break;
                                                case 'completed': $booking_badge = 'bg-success'; break;
                                                case 'cancelled': $booking_badge = 'bg-danger'; break;
                                                case 'refund_requested': $booking_badge = 'bg-warning'; break;
                                                default: $booking_badge = 'bg-secondary';
                                            }
                                            
                                            $payment_badge = $booking['payment_status'] == 'paid' ? 'bg-success' : 'bg-warning';
                                            
                                            echo "<tr>
                                                <td>#{$booking['id']}</td>
                                                <td>{$booking['user_name']}</td>
                                                <td>{$booking['room_name']}</td>
                                                <td>₹" . number_format($booking['total_amount'], 2) . "</td>
                                                <td><span class='badge {$booking_badge}'>{$booking['booking_status']}</span></td>
                                                <td><span class='badge {$payment_badge}'>{$booking['payment_status']}</span></td>
                                                <td>" . date('d M Y', strtotime($booking['created_at'])) . "</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No bookings found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require('inc/scripts.php');?>

    <script>
        // Booking Pie Chart
        const pieCtx = document.getElementById('bookingPieChart').getContext('2d');
        const bookingPieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Cancelled', 'Confirmed', 'Pending'],
                datasets: [{
                    data: [<?php echo $completed_bookings; ?>, <?php echo $cancelled_bookings; ?>, <?php echo $confirmed_bookings; ?>, <?php echo $pending_bookings; ?>],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode($revenues); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>

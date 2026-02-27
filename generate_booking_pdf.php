<?php
ob_start(); // Start output buffering
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('vendor/autoload.php');

session_start();

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('index.php');
}

if(!isset($_GET['id'])){
    redirect('my_bookings.php');
}

$booking_id = filteration($_GET)['id'];

// Fetch booking details
$query = "SELECT b.*, r.name as room_name, r.price, u.email as user_email
          FROM bookings b 
          INNER JOIN rooms r ON b.room_id = r.id 
          INNER JOIN user_cred u ON b.user_id = u.id
          WHERE b.id = ? AND b.user_id = ?";

$result = select($query, [$booking_id, $_SESSION['uId']], 'ii');

if(mysqli_num_rows($result) == 0){
    redirect('my_bookings.php');
}

$booking = mysqli_fetch_assoc($result);

// Fetch payment details
$payment_query = "SELECT * FROM payments WHERE booking_id = ?";
$payment_result = select($payment_query, [$booking_id], 'i');
$payment = mysqli_num_rows($payment_result) > 0 ? mysqli_fetch_assoc($payment_result) : null;

ob_end_clean(); // Clear any output before PDF generation

// Create PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// Set document information
$pdf->SetCreator('Hotel Management System');
$pdf->SetAuthor($booking['name']);
$pdf->SetTitle('Booking Invoice #' . $booking_id);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Hotel Header
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 10, 'HB HOTEL', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Booking Confirmation', 0, 1, 'C');
$pdf->Ln(10);

// Booking Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Booking Details', 0, 1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$html = '
<table cellpadding="5">
    <tr>
        <td width="30%"><b>Booking ID:</b></td>
        <td width="70%">#' . $booking['id'] . '</td>
    </tr>
    <tr>
        <td><b>Booking Date:</b></td>
        <td>' . date('d M Y', strtotime($booking['created_at'])) . '</td>
    </tr>
    <tr>
        <td><b>Booking Status:</b></td>
        <td>' . strtoupper($booking['booking_status']) . '</td>
    </tr>
    <tr>
        <td><b>Payment Status:</b></td>
        <td>' . strtoupper($booking['payment_status']) . '</td>
    </tr>
</table>
';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Guest Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Guest Details', 0, 1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$html = '
<table cellpadding="5">
    <tr>
        <td width="30%"><b>Name:</b></td>
        <td width="70%">' . $booking['name'] . '</td>
    </tr>
    <tr>
        <td><b>Email:</b></td>
        <td>' . $booking['user_email'] . '</td>
    </tr>
    <tr>
        <td><b>Phone:</b></td>
        <td>' . $booking['phonenum'] . '</td>
    </tr>
    <tr>
        <td><b>Address:</b></td>
        <td>' . $booking['address'] . '</td>
    </tr>
</table>
';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Room Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Room Details', 0, 1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$checkin = date('d M Y', strtotime($booking['checkin']));
$checkout = date('d M Y', strtotime($booking['checkout']));
$nights = date_diff(date_create($booking['checkin']), date_create($booking['checkout']))->days;

$html = '
<table cellpadding="5">
    <tr>
        <td width="30%"><b>Room Name:</b></td>
        <td width="70%">' . $booking['room_name'] . '</td>
    </tr>
    <tr>
        <td><b>Check-in:</b></td>
        <td>' . $checkin . '</td>
    </tr>
    <tr>
        <td><b>Check-out:</b></td>
        <td>' . $checkout . '</td>
    </tr>
    <tr>
        <td><b>Number of Nights:</b></td>
        <td>' . $nights . '</td>
    </tr>
</table>
';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Payment Details
if($payment && isset($payment['created_at'])) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 7, 'Payment Details', 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 10);
    $html = '
    <table cellpadding="5">
        <tr>
            <td width="30%"><b>Transaction ID:</b></td>
            <td width="70%">' . $payment['transaction_id'] . '</td>
        </tr>
        <tr>
            <td><b>Payment Method:</b></td>
            <td>' . strtoupper($payment['payment_method']) . '</td>
        </tr>
        <tr>
            <td><b>Payment Date:</b></td>
            <td>' . date('d M Y H:i', strtotime($payment['created_at'])) . '</td>
        </tr>
    </table>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(5);
}

// Amount Summary
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Amount Summary', 0, 1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$html = '
<table cellpadding="5" border="1">
    <tr style="background-color: #f0f0f0;">
        <td width="70%"><b>Description</b></td>
        <td width="30%" align="right"><b>Amount</b></td>
    </tr>
    <tr>
        <td>Room Charges (' . $nights . ' nights x Rs.' . number_format($booking['price'], 2) . ')</td>
        <td align="right">Rs.' . number_format($booking['total_amount'], 2) . '</td>
    </tr>
    <tr style="background-color: #e8f5e9;">
        <td><b>Total Amount</b></td>
        <td align="right"><b>Rs.' . number_format($booking['total_amount'], 2) . '</b></td>
    </tr>
</table>
';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(10);

// Footer
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 5, 'Thank you for choosing HB Hotel!', 0, 1, 'C');
$pdf->Cell(0, 5, 'For any queries, please contact us at support@hbhotel.com', 0, 1, 'C');

// Output PDF
$pdf->Output('Booking_Invoice_' . $booking_id . '.pdf', 'D');
?>

<?php
/**
 * booking_queries.php
 * -------------------
 * Centralised functions for inserting a booking order and its details.
 * Uses the generic `insert()` helper (presumably a prepared‑statement wrapper)
 * that you already have in `essentials.php`.
 *
 * @param int    $user_id      ID of the logged‑in user
 * @param int    $room_id      ID of the room being booked
 * @param string $checkin      Check‑in date (YYYY‑MM‑DD)
 * @param string $checkout     Check‑out date (YYYY‑MM‑DD)
 * @param string $order_id     Generated order identifier (e.g. ORD12345)
 *
 * @return bool  true on success, false on failure
 */
function insertBookingOrder($user_id, $room_id, $checkin, $checkout, $order_id)
{
    $query = "
        INSERT INTO `booking_order`
        (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`)
        VALUES (?, ?, ?, ?, ?);
    ";

    $values = [$user_id, $room_id, $checkin, $checkout, $order_id];
    // 'iisss' = int, int, string, string, string
    return insert($query, $values, 'iisss');
}

/**
 * Insert the detailed booking record.
 *
 * @param string $booking_id   Generated booking identifier (e.g. HB12345)
 * @param string $room_name    Name of the room
 * @param float  $price        Price per night (or total)
 * @param float  $total_pay    Total amount paid
 * @param string $user_name    Customer name
 * @param string $phone        Customer phone number
 * @param string $address      Customer address
 *
 * @return bool  true on success, false on failure
 */
function insertBookingDetails(
    $booking_id,
    $room_name,
    $price,
    $total_pay,
    $user_name,
    $phone,
    $address
) {
    $query = "
        INSERT INTO `booking_details`
        (`booking_id`, `room_name`, `price`, `total_pay`,
         `user_name`, `phonenum`, `address`)
        VALUES (?, ?, ?, ?, ?, ?, ?);
    ";

    $values = [
        $booking_id,
        $room_name,
        $price,
        $total_pay,
        $user_name,
        $phone,
        $address
    ];
    // 'issssss' = int, string, string, string, string, string, string
    return insert($query, $values, 'issssss');
}
?>

<?php
require '../function/config.php';

header('Content-Type: application/json');

if (!isset($_POST['id_event'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID event tidak ditemukan']);
    exit;
}

$id_event = intval($_POST['id_event']);

// Mulai transaksi
mysqli_begin_transaction($db);

try {
    // 1. Hapus invoice terkait
    $invoiceQuery = "DELETE FROM invoice WHERE id_event = $id_event";
    mysqli_query($db, $invoiceQuery);

    // 2. Hapus booking terkait slot
    $bookingQuery = "DELETE b FROM paddock_booking b
                     JOIN paddock_slot s ON b.slot_id = s.id_slot
                     WHERE s.id_event = $id_event";
    mysqli_query($db, $bookingQuery);

    // 3. Hapus slot
    $slotQuery = "DELETE FROM paddock_slot WHERE id_event = $id_event";
    mysqli_query($db, $slotQuery);

    // 4. Hapus event
    $eventQuery = "DELETE FROM event WHERE id_event = $id_event";
    mysqli_query($db, $eventQuery);

    // Commit jika semua berhasil
    mysqli_commit($db);

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    mysqli_rollback($db);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

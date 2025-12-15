<?php
// --- FILE: process_appointment_status.php ---
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_functions.php'; // NEW: Include the email helper

// Placeholder flash function (ensure this is available, e.g., in config.php)
if (!function_exists('flash')) {
    function flash($key, $message = null) {
        if ($message) {
            $_SESSION['flash'][$key] = $message;
        } else {
            $msg = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
    }
}


// 1. Security Check
if (empty($_SESSION['user_id'])) {
    flash('error', 'Authentication required.');
    header('Location: index.php');
    exit;
}

// 2. Input Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || $_POST['action'] !== 'approve') {
    flash('error', 'Invalid request for appointment approval.');
    header('Location: staff.php');
    exit;
}

$appointmentId = (int)$_POST['id'];

try {
    // Start a transaction for safety
    $pdo->beginTransaction();

    // 3. Fetch Appointment Details and current status
    // Fetch everything needed for the email before the update
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        flash('error', 'Appointment record not found.');
        header('Location: staff.php');
        exit;
    }

    // Check if the appointment is already approved (prevent re-send/re-update)
    if ($appointment['status'] === 'Approved') {
        flash('warning', 'Appointment ID ' . $appointmentId . ' is already approved. No action taken.');
        $pdo->commit(); 
        header('Location: staff.php');
        exit;
    }

    // 4. Update the appointment status
    $updateStmt = $pdo->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
    $updateStmt->execute([$appointmentId]);

    // Commit the database changes
    $pdo->commit();

    // 5. Send the Approval Email (This block ONLY executes IF the DB update succeeded)
    $emailSent = sendAppointmentApprovalEmail($appointment);

    if ($emailSent) {
        flash('success', 'Appointment successfully **Approved** and Confirmation Email sent to ' . htmlspecialchars($appointment['email']) . '.');
    } else {
        // Log the email failure, but still report DB success
        error_log("Email sending failed for appointment ID: " . $appointmentId);
        flash('success', 'Appointment successfully **Approved**. WARNING: Failed to send confirmation email. Check server logs.');
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database Error in process_appointment_status: " . $e->getMessage());
    flash('error', 'A database error occurred during approval: ' . $e->getMessage());

} catch (Exception $e) {
    error_log("General Error in process_appointment_status: " . $e->getMessage());
    flash('error', 'An unexpected error occurred: ' . $e->getMessage());
}

header('Location: staff.php');
exit;
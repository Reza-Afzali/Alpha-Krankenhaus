<?php
// --- FILE: email_functions.php ---

/**
 * Sends an approval email to the patient for a confirmed appointment.
 * * NOTE: For production use, replace the native mail() function 
 * with a robust library like PHPMailer or a transactional email service 
 * (e.g., SendGrid, Mailgun) for reliable delivery.
 *
 * @param array $appointmentDetails An associative array containing appointment data.
 * @return bool True on successful mail acceptance (by PHP), false otherwise.
 */
function sendAppointmentApprovalEmail(array $appointmentDetails): bool
{
    // Extract necessary details
    $patientName = $appointmentDetails['first_name'] . ' ' . $appointmentDetails['last_name'];
    $patientEmail = $appointmentDetails['email'];
    $apptDate = $appointmentDetails['appt_date'];
    $apptTime = $appointmentDetails['appt_time'];
    // Assuming 'doctor' is a column in your appointments table for the confirmed doctor
    $doctor = $appointmentDetails['doctor'] ?? 'Not Assigned'; 
    $department = $appointmentDetails['department'];

    // Hospital email address (MUST be a valid, trusted sender on your server)
    $hospitalEmail = "appointments@alphahospital.com";
    
    // --- Email Content ---
    $subject = "Your Alpha Hospital Appointment is Confirmed!";

    $message = "Dear " . htmlspecialchars($patientName) . ",\n\n";
    $message .= "We are pleased to confirm your appointment at Alpha Hospital.\n\n";
    $message .= "--- Appointment Details ---\n";
    $message .= "Date: " . htmlspecialchars($apptDate) . "\n";
    $message .= "Time: " . htmlspecialchars($apptTime) . "\n";
    $message .= "Department: " . htmlspecialchars($department) . "\n";
    $message .= "Doctor: " . htmlspecialchars($doctor) . "\n";
    $message .= "---------------------------\n\n";
    $message .= "Please arrive 15 minutes early for check-in. If you need to reschedule, please call us.\n\n";
    $message .= "Thank you for choosing Alpha Hospital.\n";
    $message .= "Alpha Hospital Team\n";

    // --- Email Headers (Crucial for successful delivery) ---
    $headers = "From: Alpha Hospital Appointments <" . $hospitalEmail . ">\r\n";
    $headers .= "Reply-To: " . $hospitalEmail . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send the email
    return mail($patientEmail, $subject, $message, $headers);
}
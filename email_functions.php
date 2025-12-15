<?php
// --- FILE: email_functions.php ---

/**

* Sendet dem Patienten eine Bestätigungs-E-Mail für einen bestätigten Termin.

* @param array $appointmentDetails Ein assoziatives Array mit Termindaten.

* @return bool True bei erfolgreicher E-Mail-Zustellung (durch PHP), andernfalls false.

*/
function sendAppointmentApprovalEmail(array $appointmentDetails): bool
{
    // Notwendige Details extrahieren
    $patientName = $appointmentDetails['first_name'] . ' ' . $appointmentDetails['last_name'];
    $patientEmail = $appointmentDetails['email'];
    $apptDate = $appointmentDetails['appt_date'];
    $apptTime = $appointmentDetails['appt_time'];
    // Angenommen, „Arzt“ ist eine Spalte in Ihrer Termintabelle für den bestätigten Arzt
    $doctor = $appointmentDetails['doctor'] ?? 'Not Assigned';
    $department = $appointmentDetails['department'];

    // Krankenhaus-E-Mail-Adresse (MUSS ein gültiger, vertrauenswürdiger Absender auf Ihrem Server sein)
    $hospitalEmail = "appointments@alphahospital.com";
    // --- E-Mail-Inhalt ---
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

    // --- E-Mail-Header (entscheidend für die erfolgreiche Zustellung) ---
    $headers = "From: Alpha Hospital Appointments <" . $hospitalEmail . ">\r\n";
    $headers .= "Reply-To: " . $hospitalEmail . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Senden Sie die E-Mail
    return mail($patientEmail, $subject, $message, $headers);
}
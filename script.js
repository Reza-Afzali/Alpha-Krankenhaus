document.addEventListener("DOMContentLoaded", function () {
    const timeSelect = document.getElementById("time-modal");
    const dateInput = document.getElementById("date-modal");
    // IMPORTANT: Ensure your doctor select field has the ID 'doctor-modal'.
    const doctorInput = document.getElementById("doctor-modal"); 

    /* --- DATE UTILITY FUNCTIONS --- */
    function isWorkingDay(dateToCheck) {
        const day = dateToCheck.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        return day >= 1 && day <= 5; // Monday (1) to Friday (5)
    }

    function findFirstValidAppointmentDate(referenceDate) {
        let d = new Date(referenceDate);
        d.setDate(d.getDate() + 1); // Start from tomorrow

        let day = d.getDay();
        if (day === 6) { // Saturday -> go to Monday (+2 days)
            d.setDate(d.getDate() + 2);
        } else if (day === 0) { // Sunday -> go to Monday (+1 day)
            d.setDate(d.getDate() + 1);
        }
        return d.toISOString().split('T')[0];
    }

    /* --- TIME SLOT GENERATOR (DYNAMIC) --- */
    /**
     * Fills the time select dropdown, disabling options in bookedSlots array.
     * @param {string[]} bookedSlots - Array of booked times (e.g., ["08:00", "09:45"]).
     */
    function populateTimeSlots(bookedSlots = []) {
        if (!timeSelect) return;

        // Clear existing options, but keep the first 'placeholder' option (if any)
        while (timeSelect.options.length > 0 && timeSelect.options[0].value !== "") {
            timeSelect.remove(0);
        }
         // Add a default disabled placeholder if it's not the first option
        if (timeSelect.options.length === 0 || timeSelect.options[0].value !== "") {
             const defaultOpt = document.createElement("option");
             defaultOpt.value = "";
             defaultOpt.innerText = "Select Time";
             defaultOpt.disabled = true;
             defaultOpt.selected = true;
             timeSelect.appendChild(defaultOpt);
        }

        
        // Convert booked slots array to a Set for fast lookup
        const bookedSet = new Set(bookedSlots);

        for (let h = 8; h < 17; h++) {
            const hr = h > 12 ? h - 12 : h;
            const ampm = h >= 12 ? "PM" : "AM";
            ["00", "45"].forEach(m => {
                // Value in 24-hour format (e.g., "08:00")
                const value = (h < 10 ? '0' : '') + `${h}:${m}`; 
                const display = `${hr}:${m} ${ampm}`;
                
                const opt = document.createElement("option");
                opt.value = value; 
                opt.innerText = display;

                if (bookedSet.has(value)) {
                    opt.disabled = true; // Disable if booked
                    opt.innerText += " (Booked)";
                }

                timeSelect.appendChild(opt);
            });
        }
        // Force selection to the placeholder or the first available option
        if (timeSelect.value === null || timeSelect.value === "") {
            timeSelect.value = timeSelect.options[0].value;
        }
    }

    /* --- AVAILABILITY FETCHER --- */
    async function fetchUnavailableSlots() {
        if (!dateInput || !doctorInput || !timeSelect) return;

        const date = dateInput.value;
        const doctor = doctorInput.value;

        // Only fetch if a valid date AND a doctor are selected
        if (!date || !doctor || doctor === "") {
            // If date/doctor is missing, show all slots
            populateTimeSlots([]); 
            return;
        }

        try {
            // NOTE: Calling the NEW PHP file 'fetch_booked_slots.php'.
            const url = `fetch_booked_slots.php?date=${date}&doctor=${doctor}`;
            const res = await fetch(url);
            
            if (res.ok) {
                const bookedSlots = await res.json();
                populateTimeSlots(bookedSlots);
            } else {
                showToast("Error fetching availability.", 'error');
                populateTimeSlots([]);
            }
        } catch (err) {
            console.error("Network error fetching slots:", err);
            populateTimeSlots([]);
        }
    }
    
    // --- Initialization and Event Listeners ---
    
    // Set min date and initial slots on load
    if (dateInput) {
        const today = new Date();
        const minWorkingDate = findFirstValidAppointmentDate(today);
        
        if (!dateInput.getAttribute('min')) {
             dateInput.setAttribute('min', minWorkingDate);
        }
        if (!dateInput.value) {
            dateInput.value = minWorkingDate; 
        }

        // Trigger slot update when date or doctor changes
        dateInput.addEventListener('change', fetchUnavailableSlots);
        dateInput.addEventListener('input', function() {
            // Auto-correction logic (for weekends)
            const selectedDate = new Date(this.value);
            if (selectedDate instanceof Date && !isNaN(selectedDate) && !isWorkingDay(selectedDate)) {
                let day = selectedDate.getDay();
                let daysToAdd = 0;
                if (day === 6) { daysToAdd = 2; } 
                else if (day === 0) { daysToAdd = 1; } 
                
                if (daysToAdd > 0) {
                    selectedDate.setHours(0, 0, 0, 0); 
                    selectedDate.setDate(selectedDate.getDate() + daysToAdd);
                    this.value = selectedDate.toISOString().split('T')[0];
                    validateField(this); 
                    showToast("Weekend appointments are not possible. Date corrected to the next Monday.", 'warning');
                }
            } else {
                validateField(this);
            }
            fetchUnavailableSlots(); // Update slots on any valid/corrected change
        });
    }

    if (doctorInput) {
        doctorInput.addEventListener('change', fetchUnavailableSlots);
    }
    
    // Initial population of slots and fetch
    populateTimeSlots([]); 
    setTimeout(fetchUnavailableSlots, 100); 

    /* 2. MODAL TOGGLE LOGIC */
    const loginOverlay = document.getElementById("loginOverlay");
    const apptOverlay = document.getElementById("appointmentOverlay");
    const loginView = document.getElementById("loginView");
    const registerView = document.getElementById("registerView");
    const registerOverlay = document.getElementById('registerOverlay'); 
    const closeRegisterBtn = document.getElementById('closeRegister');
    const openRegisterModalBtn = document.getElementById('openRegisterModal');
    
    document.getElementById("headerLoginBtn")?.addEventListener("click", () => {
        loginOverlay.classList.add("show");
        loginView.classList.remove("hidden");
        if (registerView) registerView.classList.add("hidden"); 
    });
    document.querySelectorAll(".btn-book-appointment").forEach(btn => {
        btn.addEventListener("click", () => apptOverlay.classList.add("show"));
    });
    document.getElementById("showRegister")?.addEventListener("click", () => {
        if (loginView) loginView.classList.add("hidden");
        if (registerView) registerView.classList.remove("hidden");
    });
    document.getElementById("showLogin")?.addEventListener("click", () => {
        if (registerView) registerView.classList.add("hidden");
        if (loginView) loginView.classList.remove("hidden");
    });
    document.getElementById("closeLogin")?.addEventListener("click", () => loginOverlay.classList.remove("show"));
    document.getElementById("closeAppointment")?.addEventListener("click", () => apptOverlay.classList.remove("show"));
    
    // Admin Register Modal Logic - Note: This logic is duplicated in admin.php's inline script 
    if (registerOverlay) {
        closeRegisterBtn?.addEventListener('click', () => {
            registerOverlay.classList.remove('show');
            registerOverlay.classList.add('hidden');
        });

        registerOverlay.addEventListener('click', (e) => {
            if (e.target === registerOverlay) {
                registerOverlay.classList.remove('show');
                registerOverlay.classList.add('hidden');
            }
        });
    }
    openRegisterModalBtn?.addEventListener('click', () => {
        registerOverlay?.classList.add('show'); 
        registerOverlay?.classList.remove('hidden'); 
    });


    /* 3. UNIVERSAL FORM HANDLING & AJAX SUBMISSION FIX */
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", async e => {
            const isApptForm = form.id === 'apptForm';
            //  Target the register form for AJAX
            const isRegisterForm = form.id === 'registerForm';

            // Stop submission and run client-side validation
            if (!validateForm(form)) { 
                 e.preventDefault(); 
                 // Find and display the first error message as a toast (flash message style)
                 const firstInvalid = form.querySelector('.invalid');
                 if (firstInvalid) {
                     firstInvalid.focus();
                     // Use the error message content from the span next to the input
                     showToast(firstInvalid.nextElementSibling.textContent || "Bitte korrigieren Sie die Fehler.", 'error');
                 }
                 return; 
            }

            // Use AJAX for both appointment and register forms
            if (isApptForm || isRegisterForm) { 
                e.preventDefault(); 
                const formData = new FormData(form);
                //  Correctly set the action URL
                let actionUrl = isApptForm ? 'process_appointment.php' : 'process_register.php'; 
                
                try {
                    const res = await fetch(actionUrl, { method: "POST", body: formData });
                    
                    if (res.ok) {
                        const text = await res.text();
                        
                        if (isRegisterForm) {
                            showToast("Benutzer erfolgreich registriert!", 'success'); 
                            form.reset();
                            // Close the modal
                            document.getElementById('registerOverlay')?.classList.remove("show");
                            document.getElementById('registerOverlay')?.classList.add("hidden");
                            // Reload the admin page to see the new user in the list
                            window.location.reload(); 
                        } else {
                            // Appointment success logic
                            showToast("Termin erfolgreich angefragt!", 'success'); 
                            form.reset();
                            apptOverlay.classList.remove("show");
                            fetchUnavailableSlots(); 
                        }
                    } else {
                        // Server-side validation failure (e.g., email already exists)
                        const errorText = await res.text();
                        // Server error text is displayed as an error toast
                        showToast(errorText || "Fehler bei der Registrierung/Terminbuchung.", 'error');
                    }
                } catch (err) { 
                    console.error("Network error:", err);
                    showToast("Netzwerkfehler aufgetreten.", 'error'); 
                }
            } 
            // Other forms (loginForm) submit normally after validation
        });
    });
    
    /* --- VALIDATION UTILITIES (FIXED DOB LOGIC) --- */
    function validateField(input) {
        // Look for the error span right after the input
        const errorSpan = input.nextElementSibling;
        let isValid = true;
        let msg = "";
        
        // Always reset invalid state and error message before validation
        input.classList.remove("invalid");
        if (errorSpan?.classList.contains("error-msg")) errorSpan.textContent = "";

        const value = input.value.trim();
        
        // --- 1. UNIVERSAL REQUIRED CHECK ---
        const isRegisterForm = input.form && input.form.id === 'registerForm';
        const isRequiredRegisterField = isRegisterForm; 
        
        // Check if required in HTML (for appt form) OR required by JS logic (for register form)
        if (input.hasAttribute("required") && !value) { 
             isValid = false; msg = "Feld erforderlich"; 
        } else if (isRequiredRegisterField && !value) {
             // For register form, if required and empty
             if (input.name !== 'phone' && input.type !== 'submit') {
                isValid = false; 
                msg = "Feld erforderlich."; 
             }
        }
        
        // --- 2. EMAIL VALIDATION ---
        else if (isValid && input.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { 
            isValid = false; msg = "Ungültiges E-Mail-Format"; 
        } 
        
        // --- 3. PASSWORD MATCH CHECK (REGISTRATION) ---
        else if (isValid && input.id === "reg-confirm-password") {
            const pass = document.getElementById("reg-password");
            if (pass && value !== pass.value) { isValid = false; msg = "Passwörter stimmen nicht überein"; }
        }
        
        // DOB VALIDATION (No Future Date & 18+ Age Check)
        else if (isValid && input.name === 'dob') {
            const dob = new Date(value);
            const now = new Date();
            // Set time components to 0 for accurate date-only comparison against today
            const dobDateOnly = new Date(dob.getFullYear(), dob.getMonth(), dob.getDate());
            const nowDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            const minAge = 18; // Enforce 18+ as requested by the user
            // Calculate the date 'minAge' years after DOB
            const ageDate = new Date(dob.getFullYear() + minAge, dob.getMonth(), dob.getDate());

            if (isNaN(dob) || value === '') { 
                 isValid = false; msg = "Geburtsdatum ist erforderlich.";
            } 
            // 1. Check if DOB is in the future (DOB date > Current date)
            else if (dobDateOnly > nowDateOnly) {
                isValid = false;
                msg = "Geburtsdatum darf nicht in der Zukunft liegen.";
            }
            // 2. Check if the user is 18+
            else if (ageDate > now) { 
                isValid = false;
                msg = `Benutzer muss mindestens ${minAge} Jahre alt sein. Kann niemanden unter 18 hinzufügen.`;
            }
        }
        
        // --- 5. ROLE SELECTION CHECK (REGISTRATION) ---
        else if (isValid && input.name === 'role' && value === "") {
             isValid = false; msg = "Bitte wählen Sie eine Rolle aus.";
        }
        
        // --- 6. APPOINTMENT DATE VALIDATION ---
        else if (isValid && input.id === "date-modal") {
            const selectedDate = new Date(input.value);
            const today = new Date();
            const selectedDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());
            const tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
            
            if (selectedDay < tomorrow) {
                isValid = false;
                msg = "Termine müssen ab morgen gebucht werden.";
            } else if (!isWorkingDay(selectedDay)) {
                isValid = false;
                msg = "Termine sind nur von Montag bis Freitag möglich.";
            }
        }

        if (!isValid) { 
            //  Ensure invalid class is applied for styling
            input.classList.add("invalid"); 
            if (errorSpan) errorSpan.textContent = msg; 
        }
        return isValid;
    }

    function validateForm(form) {
        let valid = true;
        // Validate all fields with the 'validate-me' class
        form.querySelectorAll(".validate-me").forEach(i => {
            // Check if element is visible and validate it
            // Run validation and update the overall 'valid' status
            if (i.offsetParent !== null && !validateField(i)) {
                 valid = false;
            }
        });
        return valid;
    }

    // Update showToast to handle success/error types
    function showToast(message, type = 'error') {
        // Ensure toast element exists (it's defined in index.php)
        const toast = document.getElementById("toastNotification") || document.querySelector('.toast:not(.auto-dismiss-toast)');
        if (toast) { 
            toast.textContent = message; 
            toast.classList.add("visible"); 
            
            // Set color based on type
            if (type === 'success') {
                // Use var(--color-tertiary) for success
                toast.style.background = 'var(--color-tertiary, #108779)'; 
            } else { // 'error' or default
                toast.style.background = '#c82333'; // Use error color
            }

            // Ensure toast is repositioned for visibility in the modal context
            toast.style.position = 'fixed'; 
            toast.style.left = '50%'; 
            toast.style.transform = 'translateX(-50%)'; 
            toast.style.bottom = '30px'; 
            toast.style.top = 'auto'; 

            setTimeout(() => toast.classList.remove("visible"), 4000); 
        }
    }

    // Attach validation listeners to blur event for immediate feedback
    document.querySelectorAll(".validate-me").forEach(i => i.addEventListener("blur", () => validateField(i)));
    // Also attach to change event for selects and dates
    document.querySelectorAll("select.validate-me, input[type='date'].validate-me").forEach(i => i.addEventListener("change", () => validateField(i)));
});
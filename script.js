document.addEventListener("DOMContentLoaded", function () {
    const timeSelect = document.getElementById("time-modal");
    const dateInput = document.getElementById("date-modal");
    // Stellen Sie sicher, dass Ihr Arztauswahlfeld die ID 'doctor-modal' hat.
    const doctorInput = document.getElementById("doctor-modal"); 

  /* --- DATUMS-HILFSFUNKTIONEN --- */
    function isWorkingDay(dateToCheck) {
        const day = dateToCheck.getDay(); // 0 = Sonntag, 1 = Montag, ..., 6 = Samstag
        return day >= 1 && day <= 5; // Montag (1) bis Freitag (5)
    }

    function findFirstValidAppointmentDate(referenceDate) {
        let d = new Date(referenceDate);
        d.setDate(d.getDate() + 1); // Start ab morgen

        let day = d.getDay();
        if (day === 6) { // Samstag -> geht zu Montag (+2 Tage)
            d.setDate(d.getDate() + 2);
        } else if (day === 0) { // Sonntag -> geht zu Montag (+1 Tag)
            d.setDate(d.getDate() + 1);
        }
        return d.toISOString().split('T')[0];
    }

    /* --- ZEITSLOT-GENERATOR --- */

/**
* Füllt das Dropdown-Menü zur Zeitauswahl und deaktiviert dabei Optionen im Array „bookedSlots“.

* @param {string[]} bookedSlots - Array mit gebuchten Zeiten (z. B. ["08:00", "09:45"]).

*/
    function populateTimeSlots(bookedSlots = []) {
        if (!timeSelect) return;

        // Vorhandene Optionen löschen, aber die erste „Platzhalter“-Option (falls vorhanden) beibehalten
        while (timeSelect.options.length > 0 && timeSelect.options[0].value !== "") {
            timeSelect.remove(0);
        }
         // Füge einen standardmäßig deaktivierten Platzhalter hinzu, falls es sich nicht um die erste Option handelt.
        if (timeSelect.options.length === 0 || timeSelect.options[0].value !== "") {
             const defaultOpt = document.createElement("option");
             defaultOpt.value = "";
             defaultOpt.innerText = "Select Time";
             defaultOpt.disabled = true;
             defaultOpt.selected = true;
             timeSelect.appendChild(defaultOpt);
        }

        
       // Konvertiere das Array der gebuchten Slots in ein Set für schnelles Nachschlagen
        const bookedSet = new Set(bookedSlots);

        for (let h = 8; h < 17; h++) {
            const hr = h > 12 ? h - 12 : h;
            const ampm = h >= 12 ? "PM" : "AM";
            ["00", "45"].forEach(m => {
               // Wert im 24-Stunden-Format (z. B. "08:00")
                const value = (h < 10 ? '0' : '') + `${h}:${m}`; 
                const display = `${hr}:${m} ${ampm}`;
                
                const opt = document.createElement("option");
                opt.value = value; 
                opt.innerText = display;

                if (bookedSet.has(value)) {
                    opt.disabled = true; // Deaktivieren, wenn gebucht
                    opt.innerText += " (Booked)";
                }

                timeSelect.appendChild(opt);
            });
        }
       // Erzwinge die Auswahl des Platzhalters oder der ersten verfügbaren Option
        if (timeSelect.value === null || timeSelect.value === "") {
            timeSelect.value = timeSelect.options[0].value;
        }
    }

    /* --- VERFÜGBARKEITSABFRAGE --- */ 
    async function fetchUnavailableSlots() {
        if (!dateInput || !doctorInput || !timeSelect) return;

        const date = dateInput.value;
        const doctor = doctorInput.value;

       // Nur abrufen, wenn ein gültiges Datum UND ein Arzt ausgewählt sind
        if (!date || !doctor || doctor === "") {
            // Falls Datum/Arzt fehlt, werden alle Einträge angezeigt.
            populateTimeSlots([]); 
            return;
        }

        try {
            // HINWEIS: Aufruf der NEUEN PHP-Datei 'fetch_booked_slots.php'.
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
    
   // --- Initialisierung und Ereignis-Listener ---

// Mindestdatum und Startzeit beim Laden festlegen
    if (dateInput) {
        const today = new Date();
        const minWorkingDate = findFirstValidAppointmentDate(today);
        
        if (!dateInput.getAttribute('min')) {
             dateInput.setAttribute('min', minWorkingDate);
        }
        if (!dateInput.value) {
            dateInput.value = minWorkingDate; 
        }

        // Aktualisierung des Auslösers bei Datums- oder Arztwechsel
        dateInput.addEventListener('change', fetchUnavailableSlots);
        dateInput.addEventListener('input', function() {
           // Autokorrekturlogik (für Wochenenden)
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
            fetchUnavailableSlots(); // Aktualisiere die Slots bei jeder gültigen/korrigierten Änderung
        });
    }

    if (doctorInput) {
        doctorInput.addEventListener('change', fetchUnavailableSlots);
    }
    
   // Initialisierung der Slots und Abruf
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
    
    // Logik des Admin-Registrierungsmodus – 
    // Hinweis: Diese Logik ist im Inline-Skript von admin.php dupliziert.
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


    /* 3. Behebung des Problems mit der universellen Formularverarbeitung 
    und der AJAX-Übermittlung */
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", async e => {
            const isApptForm = form.id === 'apptForm';
           // Das Registrierungsformular für AJAX ansprechen
            const isRegisterForm = form.id === 'registerForm';

            // Die Übermittlung stoppen und die clientseitige Validierung ausführen
            if (!validateForm(form)) { 
                 e.preventDefault(); 
                 // Die erste Fehlermeldung suchen und als Toast (Blitznachricht) anzeigen
                 const firstInvalid = form.querySelector('.invalid');
                 if (firstInvalid) {
                     firstInvalid.focus();
                     // Verwende den Inhalt der Fehlermeldung aus dem Span-Element neben dem Eingabefeld.
                     showToast(firstInvalid.nextElementSibling.textContent || "Bitte korrigieren Sie die Fehler.", 'error');
                 }
                 return; 
            }

            // Verwenden Sie AJAX sowohl für Termin- als auch für Registrierungsformulare
            if (isApptForm || isRegisterForm) { 
                e.preventDefault(); 
                const formData = new FormData(form);
                // Die Aktions-URL korrekt festlegen
                let actionUrl = isApptForm ? 'process_appointment.php' : 'process_register.php'; 
                
                try {
                    const res = await fetch(actionUrl, { method: "POST", body: formData });
                    
                    if (res.ok) {
                        const text = await res.text();
                        
                        if (isRegisterForm) {
                            showToast("Benutzer erfolgreich registriert!", 'success'); 
                            form.reset();
                            // Schließen Sie das Modal.
                            document.getElementById('registerOverlay')?.classList.remove("show");
                            document.getElementById('registerOverlay')?.classList.add("hidden");
                            // Laden Sie die Admin-Seite neu, um den neuen Benutzer in der Liste zu sehen.
                            window.location.reload(); 
                        } else {
                            // Terminerfolgslogik
                            showToast("Termin erfolgreich angefragt!", 'success'); 
                            form.reset();
                            apptOverlay.classList.remove("show");
                            fetchUnavailableSlots(); 
                        }
                    } else {
                        // Serverseitiger Validierungsfehler (z. B. E-Mail-Adresse existiert bereits)
                        const errorText = await res.text();
                        // Der Serverfehlertext wird als Fehlermeldung angezeigt.
                        showToast(errorText || "Fehler bei der Registrierung/Terminbuchung.", 'error');
                    }
                } catch (err) { 
                    console.error("Network error:", err);
                    showToast("Netzwerkfehler aufgetreten.", 'error'); 
                }
            } 
            // Andere Formulare (Login-Formular) werden nach der Validierung normal übermittelt.
        });
    });
    
  /* --- VALIDIERUNGSHILFEN (LOGIK FÜR FESTES GEBURTSDATUM) --- */
    function validateField(input) {
        // Suche nach dem Fehlerbereich direkt nach der Eingabe
        const errorSpan = input.nextElementSibling;
        let isValid = true;
        let msg = "";
        
        // Ungültigen Zustand und Fehlermeldung vor der Validierung immer zurücksetzen.
        input.classList.remove("invalid");
        if (errorSpan?.classList.contains("error-msg")) errorSpan.textContent = "";

        const value = input.value.trim();
        
   // --- 1. UNIVERSAL ERFORDERLICHE PRÜFUNG ---
        const isRegisterForm = input.form && input.form.id === 'registerForm';
        const isRequiredRegisterField = isRegisterForm; 
        
        // Prüfen, ob es im HTML-Code (für das Terminformular) oder in der JavaScript-Logik 
        // (für das Registrierungsformular) erforderlich ist
        if (input.hasAttribute("required") && !value) { 
             isValid = false; msg = "Feld erforderlich"; 
        } else if (isRequiredRegisterField && !value) {
             // Für das Registrierungsformular, falls erforderlich und leer
             if (input.name !== 'phone' && input.type !== 'submit') {
                isValid = false; 
                msg = "Feld erforderlich."; 
             }
        }
        
       // --- 2. E-Mail-Validierung ---
        else if (isValid && input.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { 
            isValid = false; msg = "Ungültiges E-Mail-Format"; 
        } 
        
       // --- 3. PASSWORTÜBERPRÜFUNG (REGISTRIERUNG) ---
        else if (isValid && input.id === "reg-confirm-password") {
            const pass = document.getElementById("reg-password");
            if (pass && value !== pass.value) { isValid = false; msg = "Passwörter stimmen nicht überein"; }
        }
        
       // Geburtsdatumsvalidierung (Kein zukünftiges Datum & Altersprüfung ab 18 Jahren)
        else if (isValid && input.name === 'dob') {
            const dob = new Date(value);
            const now = new Date();
           // Setzen Sie die Zeitkomponenten auf 0, um einen genauen Datumsvergleich mit dem heutigen Datum zu ermöglichen.
            const dobDateOnly = new Date(dob.getFullYear(), dob.getMonth(), dob.getDate());
            const nowDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            const minAge = 18; 
            // Altersfreigabe ab 18 Jahren erzwingen (Benutzerwunsch)

           //Mindestalter Jahre nach dem Geburtsdatum berechnen (Datum „minAge“)

            const ageDate = new Date(dob.getFullYear() + minAge, dob.getMonth(), dob.getDate());

            if (isNaN(dob) || value === '') { 
                 isValid = false; msg = "Geburtsdatum ist erforderlich.";
            } 
            // 1. Prüfen, ob das Geburtsdatum in der Zukunft liegt (Geburtsdatum > Aktuelles Datum)
            else if (dobDateOnly > nowDateOnly) {
                isValid = false;
                msg = "Geburtsdatum darf nicht in der Zukunft liegen.";
            }
          // 2. Prüfen, ob der Nutzer 18+ ist
            else if (ageDate > now) { 
                isValid = false;
                msg = `Benutzer muss mindestens ${minAge} Jahre alt sein. Kann niemanden unter 18 hinzufügen.`;
            }
        }
        
        // --- 5. ROLLENAUSWAHLPRÜFUNG (REGISTRIERUNG) ---
        else if (isValid && input.name === 'role' && value === "") {
             isValid = false; msg = "Bitte wählen Sie eine Rolle aus.";
        }
        
        // --- 6. Bestätigung des Termindatums ---
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
            // Sicherstellen, dass ungültige Klassen für das Styling angewendet werden
            input.classList.add("invalid"); 
            if (errorSpan) errorSpan.textContent = msg; 
        }
        return isValid;
    }

    function validateForm(form) {
        let valid = true;
     // Alle Felder mit der Klasse 'validate-me' validieren
        form.querySelectorAll(".validate-me").forEach(i => {
           // Prüfen, ob das Element sichtbar ist und es validieren

           // Validierung durchführen und den Gesamtstatus „gültig“ aktualisieren

            if (i.offsetParent !== null && !validateField(i)) {
                 valid = false;
            }
        });
        return valid;
    }

    // Aktualisiere showToast, um Erfolgs-/Fehlertypen zu verarbeiten
    function showToast(message, type = 'error') {
        // Sicherstellen, dass das Toast-Element existiert (es ist in index.php definiert)
        const toast = document.getElementById("toastNotification") || document.querySelector('.toast:not(.auto-dismiss-toast)');
        if (toast) { 
            toast.textContent = message; 
            toast.classList.add("visible"); 
            
           // Farbe basierend auf dem Typ festlegen
            if (type === 'success') {
                // Verwende var(--color-tertiary) für Erfolg
                toast.style.background = 'var(--color-tertiary, #108779)'; 
            } else { // 'error' oder Standardwert
                toast.style.background = '#c82333'; // Fehlerfarbe verwenden
            }

            // Sicherstellen, dass die Toast-Benachrichtigung im Modal-Kontext 
            // für die Sichtbarkeit neu positioniert wird
            toast.style.position = 'fixed'; 
            toast.style.left = '50%'; 
            toast.style.transform = 'translateX(-50%)'; 
            toast.style.bottom = '30px'; 
            toast.style.top = 'auto'; 

            setTimeout(() => toast.classList.remove("visible"), 4000); 
        }
    }

    // Validierungs-Listener an das Blur-Ereignis anhängen, um sofortiges Feedback zu erhalten
    document.querySelectorAll(".validate-me").forEach(i => i.addEventListener("blur", () => validateField(i)));
    // Auch an Änderungsereignisse für Auswahllisten und Datumsangaben anhängen.
    document.querySelectorAll("select.validate-me, input[type='date'].validate-me").forEach(i => i.addEventListener("change", () => validateField(i)));
});
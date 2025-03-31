// Document Ready Function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the date picker
    initializeDatePicker();
    
    // Set up form validation listeners
    setupFormValidation();
    
    // Initialize progress steps
    updateProgressSteps(1);
});

// Initialize Flatpickr Date Picker
function initializeDatePicker() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#appointmentDate", {
            minDate: "today",
            maxDate: new Date().fp_incr(60), // Allow booking up to 60 days in advance
            disable: [
                function(date) {
                    // Disable weekends
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                // Update available time slots based on selected date
                updateTimeSlots(dateStr);
            }
        });
    } else {
        console.error("Flatpickr library not loaded");
        // Fallback to basic date input
        document.getElementById('appointmentDate').type = 'date';
    }
}

// Update available time slots based on selected date
function updateTimeSlots(dateStr) {
    // In a real application, this would make an AJAX call to get available slots
    // For demo purposes, we'll randomly make some slots unavailable
    
    // Reset all time slots
    const timeSlots = document.querySelectorAll('.time-slot');
    timeSlots.forEach(slot => {
        if (!slot.classList.contains('unavailable')) {
            slot.classList.remove('unavailable');
            slot.setAttribute('onclick', 'selectTimeSlot(this)');
        }
    });
    
    // Randomly make some slots unavailable
    // Use the date string as a seed for pseudo-randomness
    const seed = dateStr.split('-').reduce((a, b) => a + parseInt(b), 0);
    const rand = (max) => (seed * 9301 + 49297) % 233280 % max;
    
    // Make 1-3 slots unavailable
    const numUnavailable = 1 + rand(3);
    for (let i = 0; i < numUnavailable; i++) {
        const slotIndex = rand(timeSlots.length);
        if (!timeSlots[slotIndex].classList.contains('unavailable')) {
            timeSlots[slotIndex].classList.add('unavailable');
            timeSlots[slotIndex].removeAttribute('onclick');
        }
    }
}

// Function to select a time slot
function selectTimeSlot(element) {
    // Clear previous selections
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Select the clicked time slot
    element.classList.add('selected');
    
    // Update hidden input with selected time
    document.getElementById('selectedTime').value = element.getAttribute('data-time');
}

// Function to move to next step
function nextStep(currentStep, nextStep) {
    // Validate current step
    if (!validateStep(currentStep)) {
        return false;
    }
    
    // Hide current step
    document.getElementById(`step${currentStep}`).style.display = 'none';
    
    // Show next step
    document.getElementById(`step${nextStep}`).style.display = 'block';
    
    // Update progress steps
    updateProgressSteps(nextStep);
    
    // Scroll to top of form
    document.querySelector('.booking-step').scrollIntoView({ behavior: 'smooth' });
    
    return true;
}

// Function to go back to previous step
function prevStep(currentStep, prevStep) {
    // Hide current step
    document.getElementById(`step${currentStep}`).style.display = 'none';
    
    // Show previous step
    document.getElementById(`step${prevStep}`).style.display = 'block';
    
    // Update progress steps
    updateProgressSteps(prevStep);
    
    return true;
}

// Validate each step before proceeding
function validateStep(step) {
    switch(step) {
        case 1:
            // Check if at least one service is selected
            const serviceInterests = document.querySelectorAll('input[name="serviceInterest[]"]:checked');
            if (serviceInterests.length === 0) {
                alert('Please select at least one service you are interested in.');
                return false;
            }
            return true;
            
        case 2:
            // Check if date and time are selected
            const appointmentDate = document.getElementById('appointmentDate').value;
            const selectedTime = document.getElementById('selectedTime').value;
            
            if (!appointmentDate) {
                alert('Please select an appointment date.');
                return false;
            }
            
            if (!selectedTime) {
                alert('Please select an available time slot.');
                return false;
            }
            
            return true;
            
        case 3:
            // Basic validation for required fields
            const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'propertyType'];
            const missingFields = [];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    missingFields.push(fieldId);
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Validate email format
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                document.getElementById('email').classList.add('is-invalid');
                return false;
            }
            
            // Check terms agreement
            if (!document.getElementById('termsAgreement').checked) {
                alert('Please agree to the Terms and Conditions to proceed.');
                return false;
            }
            
            if (missingFields.length > 0) {
                alert(`Please fill in all required fields to proceed.`);
                return false;
            }
            
            return true;
            
        default:
            return true;
    }
}

// Update progress steps indicator
function updateProgressSteps(currentStep) {
    // Reset all steps
    document.querySelectorAll('.progress-step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        
        // Mark steps as completed or active
        if (index + 1 < currentStep) {
            step.classList.add('completed');
        } else if (index + 1 === currentStep) {
            step.classList.add('active');
        }
    });
}

// Set up form validation
function setupFormValidation() {
    const form = document.getElementById('consultationForm');
    
    // Add validation classes on blur
    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

// Show booking confirmation
function showConfirmation() {
    // Validate final step
    if (!validateStep(3)) {
        return false;
    }
    
    // Collect form data
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const address = document.getElementById('address').value;
    const appointmentDate = document.getElementById('appointmentDate').value;
    const selectedTime = document.getElementById('selectedTime').value;
    
    // Format date for display
    const formattedDate = new Date(appointmentDate).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    });
    
    // Format time for display
    const timeHour = parseInt(selectedTime.split(':')[0]);
    const formattedTime = timeHour > 12 ? 
        `${timeHour - 12}:00 PM` : 
        `${timeHour}:00 AM`;
    
    // Get selected services
    const selectedServices = [];
    document.querySelectorAll('input[name="serviceInterest[]"]:checked').forEach(checkbox => {
        const label = document.querySelector(`label[for="${checkbox.id}"]`).textContent.trim();
        selectedServices.push(label);
    });
    
    // Get consultation type
    const consultationType = document.querySelector('input[name="consultationType"]:checked').value;
    const consultationTypeLabel = consultationType === 'in-person' ? 'In-Person' : 'Virtual';
    
    // Update confirmation page
    document.getElementById('confirmService').textContent = selectedServices.join(', ');
    document.getElementById('confirmDate').textContent = formattedDate;
    document.getElementById('confirmTime').textContent = formattedTime;
    document.getElementById('confirmType').textContent = consultationTypeLabel;
    document.getElementById('confirmName').textContent = `${firstName} ${lastName}`;
    document.getElementById('confirmEmail').textContent = email;
    document.getElementById('confirmPhone').textContent = phone;
    document.getElementById('confirmAddress').textContent = address;
    
    // Hide form step and show confirmation
    document.getElementById('step3').style.display = 'none';
    document.getElementById('step4').style.display = 'block';
    
    // Update progress steps
    updateProgressSteps(4);
    
    const formData = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: phone,
        address: address,
        appointmentDate: appointmentDate,
        selectedTime: selectedTime,
        selectedServices: selectedServices,
        consultationType: consultationType
    }

    fetch('consultation.php?submitted', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify( formData )
    })
    .then(response => response.json())
    .then(response => console.log(JSON.stringify(response)))
    
    // Scroll to top of confirmation
    document.querySelector('.booking-step').scrollIntoView({ behavior: 'smooth' });
    
    return true;
}

// Helper function for installation booking (for future implementation)
function unlockInstallationBooking() {
    // This would typically be called after a successful consultation
    const installationTab = document.getElementById('installation-tab');
    installationTab.classList.remove('disabled');
    installationTab.removeAttribute('aria-selected');
    installationTab.removeChild(installationTab.querySelector('.bi-lock-fill'));
}

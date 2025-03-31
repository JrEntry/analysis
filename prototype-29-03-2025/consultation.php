<?php
    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    if ( isset($_GET["submitted"]) ) {
        $data = json_decode(file_get_contents("php://input"), true);
        try {
            // Insert user data
            $user_sql = "INSERT INTO consultations (consultation_id, user_id, preferred_datetime, service_interest, description, consultation_type, status, created_at) 
                          VALUES (?, ?, ?, ?, ?)";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("sssss", $full_name, $email, $username, $password_hash, $phone);
            $user_stmt->execute();
            
            // Get the new user ID
            $user_id = $conn->insert_id;
            
            // Insert customer profile data (if provided)
            if (!empty($address) || !empty($property_type) || isset($_POST['energy_source']) || isset($_POST['monthly_bill'])) {
                $energy_source = isset($_POST['energy_source']) ? sanitize_input($_POST['energy_source']) : null;
                $monthly_bill = isset($_POST['monthly_bill']) ? floatval($_POST['monthly_bill']) : null;
                
                $profile_sql = "INSERT INTO customer_profile (user_id, address, property_type, primary_energy_source, monthly_energy_bill) 
                                VALUES (?, ?, ?, ?, ?)";
                $profile_stmt = $conn->prepare($profile_sql);
                $profile_stmt->bind_param("isssd", $user_id, $address, $property_type, $energy_source, $monthly_bill);
                $profile_stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Set success message
            $success_message = 'Registration successful! You can now <a href="login.php">login</a> to your account.';
            
            // Close statements
            $user_stmt->close();
            $profile_stmt->close();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = 'Registration failed: ' . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rolsa Technologies - Schedule Your Service</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    
    <!-- Flatpickr Calendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <!-- Custom Styles -->
    <style>
        :root {
            --yellow: #ECA400;
            --light-bg: #F5F0F6;
            --dark-blue: #16262E;
            --green: #0B6E4F;
            --light-green: #C7F2A7;
        }
        
        body {
            background-color: var(--light-bg);
            color: var(--dark-blue);
        }
        
        /* Custom Brand Colors */
        .bg-brand-green {
            background-color: var(--green) !important;
        }
        
        .bg-brand-light {
            background-color: var(--light-bg) !important;
        }
        
        .bg-brand-dark {
            background-color: var(--dark-blue) !important;
        }
        
        .bg-brand-yellow {
            background-color: var(--yellow) !important;
        }
        
        .bg-brand-light-green {
            background-color: var(--light-green) !important;
        }
        
        .text-brand-green {
            color: var(--green) !important;
        }
        
        .text-brand-yellow {
            color: var(--yellow) !important;
        }
        
        .text-brand-dark {
            color: var(--dark-blue) !important;
        }
        
        /* Buttons */
        .btn-brand-primary {
            background-color: var(--green);
            color: white;
            border: none;
        }
        
        .btn-brand-primary:hover {
            background-color: #095a41;
            color: white;
        }
        
        .btn-brand-secondary {
            background-color: var(--yellow);
            color: var(--dark-blue);
            border: none;
        }
        
        .btn-brand-secondary:hover {
            background-color: #d69200;
            color: var(--dark-blue);
        }
        
        /* Progress Steps */
        .booking-progress {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            padding: 0 20px;
        }
        
        .booking-progress::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #ddd;
            z-index: 1;
        }
        
        .progress-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 120px;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ddd;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .progress-step.active .step-number {
            background-color: var(--green);
            color: white;
        }
        
        .progress-step.completed .step-number {
            background-color: var(--yellow);
            color: var(--dark-blue);
        }
        
        .step-label {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .progress-step.active .step-label {
            color: var(--green);
        }
        
        .step-desc {
            font-size: 0.85rem;
            color: #777;
            max-width: 150px;
        }
        
        /* Custom Nav Tabs */
        .booking-tabs .nav-link {
            color: var(--dark-blue);
            font-weight: 600;
            padding: 1rem;
            border: none;
            border-radius: 0;
            background-color: #f1f1f1;
        }
        
        .booking-tabs .nav-link.active {
            color: var(--green);
            background-color: white;
            border-top: 3px solid var(--green);
        }
        
        .booking-tabs .nav-link.disabled {
            color: #aaa;
            background-color: #f8f8f8;
            cursor: not-allowed;
        }
        
        /* Form Styling */
        .form-label {
            font-weight: 500;
            color: var(--dark-blue);
        }
        
        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 0.25rem rgba(11, 110, 79, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--green);
            border-color: var(--green);
        }
        
        /* Time Slots */
        .time-slot-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        @media (min-width: 768px) {
            .time-slot-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        .time-slot {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .time-slot:hover {
            background-color: #e9e9e9;
        }
        
        .time-slot.selected {
            background-color: var(--light-green);
            border-color: var(--green);
            color: var(--green);
            font-weight: 600;
        }
        
        .time-slot.unavailable {
            background-color: #f9f9f9;
            color: #bbb;
            cursor: not-allowed;
            text-decoration: line-through;
        }
        
        /* File Upload */
        .file-upload-container {
            border: 2px dashed #ddd;
            padding: 2rem;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload-container:hover {
            border-color: var(--green);
            background-color: rgba(11, 110, 79, 0.05);
        }
        
        .file-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 1rem;
        }
        
        .file-item {
            background-color: #f5f5f5;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-remove {
            color: #dc3545;
            cursor: pointer;
        }
        
        /* Form Sections */
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--green);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        /* Navigation Logo */
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            margin-right: 10px;
        }
        
        /* Installation Locked Section */
        .installation-locked {
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .lock-icon {
            font-size: 3rem;
            color: #aaa;
            margin-bottom: 1rem;
        }
        
        /* Footer Customization */
        .footer-social {
            width: 36px;
            height: 36px;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .footer-social:hover {
            background-color: var(--green);
        }

        .btn-yellow-rolsa {
            background-color: #ECA400 !important;
            color: #16262E !important;
        }

        .btn-dark-rolsa {
            background-color: #16262E !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <!-- Page Header -->
    <header class="bg-brand-green text-white py-5 text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Book Your Green Energy Service</h1>
            <p class="lead">Start your journey towards energy independence with a personalized consultation, followed by professional installation of your chosen green energy solutions.</p>
        </div>
    </header>
    
    <!-- Main Booking Section -->
    <section class="py-5">
        <div class="container">
            <!-- Booking Progress Steps -->
            <div class="booking-progress">
                <div class="progress-step">
                    <div class="step-number">1</div>
                    <div class="step-label">Select Service</div>
                    <div class="step-desc">Choose what you need</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">2</div>
                    <div class="step-label">Schedule</div>
                    <div class="step-desc">Pick a date & time</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <div class="step-label">Your Details</div>
                    <div class="step-desc">Complete booking info</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">4</div>
                    <div class="step-label">Confirmation</div>
                    <div class="step-desc">Receive booking details</div>
                </div>
            </div>
            
            <!-- Service Selection Tabs -->
            <div class="booking-tabs">
                <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="consultation-tab" data-bs-toggle="tab" data-bs-target="#consultation-pane" type="button" role="tab" aria-controls="consultation-pane" aria-selected="true">
                            <i class="bi bi-chat-left-text me-2"></i>Consultation Booking
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link disabled" id="installation-tab" data-bs-toggle="tab" data-bs-target="#installation-pane" type="button" role="tab" aria-controls="installation-pane" aria-selected="false">
                            <i class="bi bi-tools me-2"></i>Installation Booking
                            <i class="bi bi-lock-fill ms-2 small"></i>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="bookingTabsContent">
                    <!-- Consultation Booking Tab -->
                    <div class="tab-pane fade show active" id="consultation-pane" role="tabpanel" aria-labelledby="consultation-tab" tabindex="0">
                        <div class="card border-0 shadow">
                            <div class="card-body p-4">
                                <!-- Alert Message -->
                                <div class="alert bg-brand-light-green border-0 d-flex align-items-center mb-4">
                                    <i class="bi bi-info-circle-fill text-brand-green me-3 fs-4"></i>
                                    <div>
                                        <strong>Important:</strong> A consultation is required before scheduling an installation. During your consultation, our experts will assess your property's suitability for green energy solutions and provide personalized recommendations.
                                    </div>
                                </div>
                                
                                <!-- Multi-Step Booking Form -->
                                <form id="consultationForm">
                                    <!-- Step 1: Service Selection -->
                                    <div class="booking-step" id="step1">
                                        <h4 class="section-title">What are you interested in?</h4>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="solarOption" name="serviceInterest[]" value="solar">
                                                            <label class="form-check-label fw-bold mb-2" for="solarOption">
                                                                Solar Panel Systems
                                                            </label>
                                                        </div>
                                                        <p class="small text-muted mb-0">Generate your own clean electricity and reduce your energy bills with our high-efficiency solar panels.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="evOption" name="serviceInterest[]" value="ev">
                                                            <label class="form-check-label fw-bold mb-2" for="evOption">
                                                                EV Charging Solutions
                                                            </label>
                                                        </div>
                                                        <p class="small text-muted mb-0">Power your electric vehicle with clean energy using our home charging solutions.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="smartOption" name="serviceInterest[]" value="smart">
                                                            <label class="form-check-label fw-bold mb-2" for="smartOption">
                                                                Smart Home Systems
                                                            </label>
                                                        </div>
                                                        <p class="small text-muted mb-0">Optimize your energy usage with intelligent monitoring and control systems.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h4 class="section-title">Consultation Type</h4>
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="consultationType" id="inPersonConsultation" value="in-person" checked>
                                                            <label class="form-check-label fw-bold mb-2" for="inPersonConsultation">
                                                                <i class="bi bi-house me-2"></i>In-Person Consultation
                                                            </label>
                                                        </div>
                                                        <p class="small text-muted mb-0">Our expert will visit your property to assess suitability and discuss options in person.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="consultationType" id="virtualConsultation" value="virtual">
                                                            <label class="form-check-label fw-bold mb-2" for="virtualConsultation">
                                                                <i class="bi bi-camera-video me-2"></i>Virtual Consultation
                                                            </label>
                                                        </div>
                                                        <p class="small text-muted mb-0">Discuss your needs via video call. You may need to share photos or videos of your property.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-brand-primary px-4" onclick="nextStep(1, 2)">Continue</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 2: Date and Time Selection -->
                                    <div class="booking-step" id="step2" style="display: none;">
                                        <h4 class="section-title">Select a Date</h4>
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="appointmentDate" placeholder="Select Date" readonly>
                                                    <label for="appointmentDate">Appointment Date</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h4 class="section-title">Available Time Slots</h4>
                                        <p class="text-muted mb-3">Please select a preferred time slot for your consultation:</p>
                                        
                                        <div class="time-slot-container mb-4">
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="09:00">9:00 AM</div>
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="10:00">10:00 AM</div>
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="11:00">11:00 AM</div>
                                            <div class="time-slot unavailable" data-time="12:00">12:00 PM</div>
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="13:00">1:00 PM</div>
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="14:00">2:00 PM</div>
                                            <div class="time-slot unavailable" data-time="15:00">3:00 PM</div>
                                            <div class="time-slot" onclick="selectTimeSlot(this)" data-time="16:00">4:00 PM</div>
                                        </div>
                                        <input type="hidden" id="selectedTime" name="selectedTime">
                                        
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2, 1)">Back</button>
                                            <button type="button" class="btn btn-brand-primary px-4" onclick="nextStep(2, 3)">Continue</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 3: Contact Details -->
                                    <div class="booking-step" id="step3" style="display: none;">
                                        <h4 class="section-title">Your Contact Information</h4>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label for="firstName" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="lastName" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" required>
                                            </div>
                                        </div>
                                        
                                        <h4 class="section-title">Property Details</h4>
                                        
                                        <div class="row mb-4">
                                            <div class="col-12 mb-3">
                                                <label for="address" class="form-label">Property Address</label>
                                                <input type="text" class="form-control" id="address" name="address" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="propertyType" class="form-label">Property Type</label>
                                                <select class="form-select" id="propertyType" name="propertyType" required>
                                                    <option value="" selected disabled>Select property type</option>
                                                    <option value="detached">Detached House</option>
                                                    <option value="semi-detached">Semi-Detached House</option>
                                                    <option value="terraced">Terraced House</option>
                                                    <option value="apartment">Apartment/Flat</option>
                                                    <option value="commercial">Commercial Property</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="propertySize" class="form-label">Property Size (approx sq ft)</label>
                                                <input type="number" class="form-control" id="propertySize" name="propertySize">
                                            </div>
                                        </div>
                                        
                                        <h4 class="section-title">Additional Information</h4>
                                        
                                        <div class="row mb-4">
                                            <div class="col-12 mb-3">
                                                <label for="additionalNotes" class="form-label">Additional Notes or Questions</label>
                                                <textarea class="form-control" id="additionalNotes" name="additionalNotes" rows="3" placeholder="Please share any specific questions or information that would be helpful for our consultant."></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="termsAgreement" name="termsAgreement" required>
                                            <label class="form-check-label" for="termsAgreement">
                                                I agree to the <a href="#" class="text-brand-green">Terms and Conditions</a> and <a href="#" class="text-brand-green">Privacy Policy</a>
                                            </label>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3, 2)">Back</button>
                                            <button type="button" class="btn btn-brand-primary px-4" onclick="showConfirmation()">Book Consultation</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 4: Confirmation -->
                                    <div class="booking-step" id="step4" style="display: none;">
                                        <div class="text-center mb-4">
                                            <div class="bg-brand-light-green rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                <i class="bi bi-check-lg text-brand-green" style="font-size: 3rem;"></i>
                                            </div>
                                            <h2 class="mt-4 text-brand-dark">Consultation Booked!</h2>
                                            <p class="text-muted">We've received your booking request and will confirm it shortly.</p>
                                        </div>
                                        
                                        <div class="card bg-brand-light mb-4">
                                            <div class="card-body">
                                                <h5 class="card-title">Booking Details</h5>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Service:</strong> <span id="confirmService">Solar Panel Consultation</span></p>
                                                        <p><strong>Date:</strong> <span id="confirmDate">March 15, 2025</span></p>
                                                        <p><strong>Time:</strong> <span id="confirmTime">10:00 AM</span></p>
                                                        <p><strong>Type:</strong> <span id="confirmType">In-Person</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Name:</strong> <span id="confirmName">John Doe</span></p>
                                                        <p><strong>Email:</strong> <span id="confirmEmail">john.doe@example.com</span></p>
                                                        <p><strong>Phone:</strong> <span id="confirmPhone">07700 900123</span></p>
                                                        <p><strong>Address:</strong> <span id="confirmAddress">123 Example Street</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="alert bg-white border-start border-4 border-brand-yellow">
                                            <div class="d-flex">
                                                <i class="bi bi-lightbulb-fill text-brand-yellow me-3 fs-4"></i>
                                                <div>
                                                    <h5 class="alert-heading">What happens next?</h5>
                                                    <p class="mb-0">You'll receive a confirmation email with your booking details. Our consultant will reach out before your appointment to confirm and answer any questions you might have.</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="account.html" class="btn btn-outline-secondary px-4">
                                                <i class="bi bi-person me-2"></i>View in My Account
                                            </a>
                                            <a href="index.html" class="btn btn-brand-primary px-4">
                                                <i class="bi bi-house-door me-2"></i>Return to Home
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="/assets/libs/booking-script.js"></script>
    </body>
</html>
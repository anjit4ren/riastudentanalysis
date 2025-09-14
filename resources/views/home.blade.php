<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        Student Analysis System | RIA
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="{{ asset('build/css/public-registration.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="background-animation">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <div id="paymentResult"
        @if (session()->has('success')) data-success="{{ session('success') ? 'true' : 'false' }}"
         data-message="{{ session('message') }}" @endif>

    </div>

    <div id="portalStatusContainer">
        @foreach ($portalStatus as $status)
            <div class="portal-status-item" data-portalstatus="{{ $status->status_name }}"
                data-portalmessage="{{ $status->message }}">
            </div>
        @endforeach
    </div>

    {{-- Clear session after the div is rendered --}}
    @if (session()->has('success'))
        @php
            session()->forget(['success', 'message']);
        @endphp
    @endif

    <div class="container">
        <div class="sidebar">
            <div class="logo-section">
                <div class="logo">
                    <img width="80px" height="80px" src="{{ asset('images/rpmun1.png') }}" />
                </div>
                <h1 class="event-title">
                    @if (isset($formData['data']['active_event']['event_name']))
                        {{ $formData['data']['active_event']['event_name'] }}
                    @else
                        RPMUN X
                    @endif
                </h1>
                <p class="event-subtitle">Reliance Education Network</p>
            </div>

            <div class="event-details">
                <div class="detail-item">
                    <span>✅</span>
                    <span>Reliance Public Model United Nation</span>
                </div>

                <div class="detail-item">
                    <span>📍</span>
                    <span>Reliance Public School</span>
                </div>

                <div class="detail-item">
                    <span>📅</span>
                    <span>
                        @if (isset($formData['data']['active_event']['registration_info']['start_date']))
                            {{ \Carbon\Carbon::parse($formData['data']['active_event']['registration_info']['start_date'])->format('M d') }}
                            -
                            {{ \Carbon\Carbon::parse($formData['data']['active_event']['registration_info']['end_date'])->format('d, Y') }}
                        @else
                        @endif
                    </span>
                </div>

                <div class="detail-item">
                    <span>🧑🏻‍🤝‍🧑🏻</span>
                    <span>Estimate Delegates: 200+</span>
                </div>
            </div>


            <div class="price-card">
                @php
                    // Extract pricing data with fallbacks
                    $earlyBirdPrice =
                        $formData['data']['active_event']['registration_info']['early_bird_price'] ?? 2999;
                    $generalPrice = $formData['data']['active_event']['registration_info']['general_price'] ?? 3000;
                    $ebLastDate = isset($formData['data']['active_event']['registration_info']['eb_last_date'])
                        ? \Carbon\Carbon::parse($formData['data']['active_event']['registration_info']['eb_last_date'])
                        : \Carbon\Carbon::parse('2024-07-25');

                    $isEarlyBird = now()->lte($ebLastDate); // Check if current date is before/equal to last date
                @endphp

                <div class="price-amount">Rs. {{ number_format($isEarlyBird ? $earlyBirdPrice : $generalPrice) }}/-
                </div>

                <div class="price-note">
                    @if ($isEarlyBird)
                        Early Bird Offer <br />
                        <small><del>Rs. {{ number_format($generalPrice) }}/-</del></small> <br />
                        <span class="early-deadline">
                            Offer valid till {{ $ebLastDate->format('M d, Y') }}
                        </span>
                    @else
                        Regular Price
                    @endif
                </div>
            </div>


        </div>

        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Register Now</h2>
                <p class="form-subtitle">Where Young Minds Convene to Negotiate</p>
            </div>

            <div class="form-content">
                <div class="progress-bar">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="step active" data-step="1" style="color: white">1</div>
                    <div class="step" data-step="2" style="color: white">2</div>
                    <div class="step" data-step="3" style="color: white">3</div>
                </div>

                <form id="registrationForm">
                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" data-step="1">
                        <h3 class="step-title1">Personal Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="fullName">Full Name *</label>
                                <input type="text" id="fullName" name="fullName" required
                                    placeholder="Enter your full name" />
                                <div class="error-message">Please enter your full name</div>
                            </div>
                            <div class="form-group">
                                <label for="age">Age *</label>
                                <input type="number" id="age" name="age" required min="10"
                                    max="20" placeholder="Enter your age" />
                                <div class="error-message">Please enter a valid age (10-20)</div>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender *</label>
                                <div class="gender-selection">
                                    <input type="radio" id="gender-male" name="gender" value="male" required />
                                    <label for="gender-male">Male</label>

                                    <input type="radio" id="gender-female" name="gender" value="female" required />
                                    <label for="gender-female">Female</label>

                                    {{-- <input type="radio" id="gender-other" name="gender" value="other" required />
                                    <label for="gender-other">Other</label> --}}
                                </div>
                                <div class="error-message">Please select your Gender</div>
                            </div>
                            <div class="form-group full-width">
                                <label for="institution">School / College Name *</label>
                                <input type="text" id="institution" name="institution" required
                                    placeholder="Enter your school or college name" />
                                <div class="error-message">Please enter your institution name</div>
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact Number *</label>
                                <input type="tel" id="contact" name="contact" required
                                    placeholder="+977 98XXXXXXXX" />
                                <div class="error-message">Please enter a valid contact number</div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required
                                    placeholder="your.email@example.com" />
                                <div class="error-message">Please enter a valid email address</div>
                            </div>

                            <div class="form-group">
                                <label for="district">Select District *</label>
                                <select id="district" name="district" required>
                                    <option value="">Select District</option>
                                    @foreach ($formData['data']['districts'] as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                <div class="error-message">Please select a district</div>
                            </div>


                        </div>
                    </div>

                    <!-- Step 2: MUN Details -->
                    <div class="form-step" data-step="2">
                        <h3 class="step-title1">MUN Registration Details</h3>


                        <div class="form-grid">

                            <div class="form-group">
                                <label for="delegate_type">Delegate Type *</label>
                                <select id="delegate_type" name="delegate_type" required>
                                    <option value="">Select Delegate Type</option>
                                    @foreach ($formData['data']['delegate_types'] as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="error-message">Please select delegate type</div>
                            </div>

                            <div class="form-group">
                                <label for="dietary">Dietary Preference *</label>
                                <select id="dietary" name="dietary" required>
                                    <option value="">Select Preference</option>
                                    @foreach ($formData['data']['food_preferences'] as $preference)
                                        <option value="{{ $preference['id'] }}">{{ $preference['name'] }}</option>
                                    @endforeach
                                </select>

                                <div class="error-message">Please select a dietary preference</div>
                            </div>

                            <!-- Residence Type Selection -->
                            <div class="form-group">
                                <label for="residence_type">Residence Type *</label>
                                <select id="residence_type" name="residence_type" required>
                                    <option value="">Select Residence Type</option>
                                    @foreach ($formData['data']['residence_types'] as $residence)
                                        <option value="{{ $residence['id'] }}">{{ $residence['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="error-message">Please select residence type</div>
                            </div>





                        </div>

                        <div class="committee-section">
                            <label class="committee-main-label" style="padding: 8px">Committee Preferences * (Select
                                up to 3)</label>
                            <br />

                            <div class="committee-grid" style="padding-top: 15px">
                                @foreach ($formData['data']['committees'] as $committee)
                                    <div class="committee-card">
                                        <input type="checkbox" id="committee-{{ $committee['id'] }}"
                                            name="committees[]" value="{{ $committee['id'] }}" />
                                        <label for="committee-{{ $committee['id'] }}" class="committee-label">
                                            <div class="committee-name">{{ $committee['short_name'] }}</div>
                                            <div class="committee-desc">{{ $committee['name'] }}</div>

                                        </label>



                                    </div>
                                @endforeach
                            </div>
                            <div class="error-message" id="committeeError">Please select at least one committee</div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="additionalInfo">MUN Experiences, thus far</label>
                                <textarea id="additionalInfo" name="additionalInfo" rows="3" placeholder="Any MUN Experience, thus far..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Registration Summary & Payment -->
                    <div class="form-step" data-step="3">
                        <h3 class="step-title1">CHECKOUT & PAYMENT</h3>

                        <div class="checkout-content">
                            <!-- Registration Summary -->
                            <div class="summary-card registration-card">
                                <h3 class="card-title">
                                    <span class="card-icon">📋</span>
                                    Registration Summary
                                </h3>
                                <div class="registration-summary" id="registrationSummary">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>



                            <!-- Payment Details -->
                            <!-- Payment Details -->
                            <div class="summary-card">
                                <h3 class="card-title">
                                    <span class="card-icon">💳</span>
                                    Payment Details
                                </h3>
                                <div class="payment-breakdown">
                                    <div class="payment-item">
                                        <span>Registration Fee</span>
                                        <span class="payment-amount" id="paymentAmount">
                                            Rs. {{ number_format($formData['data']['cost']) }}
                                        </span>
                                    </div>

                                    <!-- Residence Fee (hidden by default) -->
                                    <div class="payment-item" id="residenceFeeItem" style="display: none;">
                                        <span>Residence Fee</span>
                                        <span class="payment-amount" id="residenceFeeAmount">
                                            Rs. 1,000
                                        </span>
                                    </div>

                                    <div class="payment-item payment-total">
                                        <span>Total Amount</span>
                                        <span class="total-amount" id="totalAmount"
                                            data-cost="{{ number_format($formData['data']['cost']) }}">
                                            Rs. {{ number_format($formData['data']['cost']) }}
                                        </span>
                                    </div>
                                </div>
                            </div>






                            <!-- Payment Method -->
                            <div class="summary-card">
                                <h3 class="card-title">
                                    <span class="card-icon">🔒</span>
                                    Payment Method
                                </h3>
                                <div class="payment-gateway">
                                    <div class="esewa-logo">eSewa</div>
                                    <div class="payment-info">
                                        <div class="payment-title">Pay with eSewa</div>
                                        <div class="payment-subtitle">Secure digital wallet</div>
                                    </div>
                                    <div class="security-badge">🔐 Secure</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary" id="prevBtn"
                            onclick="changeStep(-1)">Previous</button>
                        <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Overlay -->
    <div class="success-overlay" id="unifiedPopup" style="display: none;">
        <div class="success-card">
            <div class="success-icon">✓</div>
            <h3 class="popup-title">Title</h3>
            <p class="popup-message">Message</p>
            <button class="btn btn-primary" id="popupCloseBtn">Close</button>
        </div>
    </div>






    <script>
        // Pass PHP data to JavaScript
        const formData = @json($formData['data'] ?? []);
        const eventData = formData.active_event || {};
        const registrationInfo = eventData.registration_info || {};
        const committees = formData.committees || [];
        const foodPreferences = formData.food_preferences || [];
        const residenceTypes = formData.residence_types || [];
        const delegateTypes = formData.delegate_types || [];
    </script>

    <script src="{{ asset('build/js/pages/public-registration.js') }}"></script>
</body>

</html>

<?php 
//Template Name: Refund Page

get_header(); ?>
<div class = "contact-us-main-wrapper">
    <div class="banner-pages" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/refund-image.jpg' ); ?>'); background-position: center top;">
        <div class = "container">
            <div class = "row">
                <div class = "col-lg-6 col-md-6 col-sm-12">
                    <h1>Refund</h1>
                    <p>Something didn’t go as planned with your booking? No worries—just fill out the form below and we’ll take a look.</p>
                    <p>WMake sure to include your booking details so we can process your request faster. We’ll get back to you as soon as possible.</p>
                </div>
                <div class = "col-lg-6 col-md-6 col-sm-12">
                </div>

            </div>
        </div>
    </div>

</div>

<div class = "container refund-page">

<form id="mp-refund-form" class="container mt-4">

  <div class="card p-4 shadow-sm">
    <h4 class="mb-2">Refund Request</h4>

    <!-- Alert container -->
    <div id="mp-refund-message"></div>

    <p class="text-muted">
      Please complete the form below to request a refund. Make sure your booking details are correct.
    </p>

    <!-- Customer Info -->
    <h5 class="mt-4">Customer Information</h5>
    <div class="row">
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
      </div>
      <div class="col-md-4 mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
      </div>
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="phone" placeholder="Phone Number" required>
      </div>
    </div>

    <!-- Booking Details -->
    <h5 class="mt-3">Booking Details</h5>
    <p class="text-muted small">
      Enter your booking reference from your confirmation email or receipt.
    </p>

    <!-- Hidden nonce -->
    <input type="hidden" name="nonce" id="refund_nonce">

    <div class="row">
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="booking_reference"
          placeholder="Booking Reference (e.g. paymongo_xxx)" required>
      </div>

      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="payment_id"
          placeholder="Payment ID (optional)">
      </div>

      <div class="col-md-4 mb-3">
        <input type="number" step="0.01" class="form-control" name="amount"
          placeholder="Amount Paid" required>
      </div>
    </div>

    <div class="mb-3">
      <select class="form-select form-control" name="service_type" required>
        <option value="">Select Service</option>
        <option value="court">Court Booking</option>
        <option value="coach">Coach Booking</option>
      </select>
    </div>

    <!-- Refund Details -->
    <h5 class="mt-3">Refund Details</h5>

    <div class="mb-3">
      <select class="form-select form-control" name="reason" required>
        <option value="">Select Reason</option>
        <option value="cancelled">Cancelled Booking</option>
        <option value="double_payment">Double Payment</option>
        <option value="technical_issue">Technical Issue</option>
        <option value="other">Other</option>
      </select>
    </div>

    <div class="mb-3">
      <textarea class="form-control" name="reason_note" rows="3"
        placeholder="Additional details (optional)"></textarea>
    </div>

    <!-- Refund Method -->
    <h5 class="mt-3">Refund Method</h5>

    <div class="mb-3">
        <select class="form-select form-control" name="method" id="refund_method" required>
        <option value="">Select Method</option>
        <option value="gcash">GCash</option>
        <option value="maya">Maya</option>
        <option value="bank">Bank Transfer</option>
      </select>
    </div>

    <!-- Wallet Fields -->
    <div id="wallet_fields" class="row d-none">
      <div class="col-md-6 mb-3">
        <input type="text" class="form-control" name="wallet_name"
          placeholder="Account Name (GCash/Maya)">
      </div>
      <div class="col-md-6 mb-3">
        <input type="text" class="form-control" name="wallet_number"
          placeholder="Mobile Number">
      </div>
    </div>

    <!-- Bank Fields -->
    <div id="bank_fields" class="row d-none">
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="bank_name"
          placeholder="Bank Name">
      </div>
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="bank_account_name"
          placeholder="Account Name">
      </div>
      <div class="col-md-4 mb-3">
        <input type="text" class="form-control" name="bank_account_number"
          placeholder="Account Number">
      </div>
    </div>

    <!-- Consent -->
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="consent" required>
      <label class="form-check-label">
        I confirm that the information provided is accurate.
      </label>
    </div>

    <button type="submit" class="btn general-btn">
      Submit Refund Request
    </button>

  </div>

</form>
 </div>
<?php get_footer(); ?>
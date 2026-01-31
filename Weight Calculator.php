<?php
// Register the shortcode and output the shipping quote form with embedded CSS and JS
function shipping_quote_form_shortcode() {
    ob_start();
    ?>
    <div class="shipping-quote-container">
        <div class="tabs">
            <div class="tab active" data-tab="full-quote">FULL QUOTE 🚀</div>
            <div class="tab" data-tab="quick-quote">QUICK QUOTE 🚀</div>
        </div>

        <!-- Full Quote Tab -->
        <div class="tab-content" id="full-quote">
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label>Dimensions <span class="required">*</span> <span class="info-icon">ⓘ</span></label>
                        <select>
                            <option>PLEASE SELECT A PACKAGE TYPE</option>
                        </select>
                        <input type="text" placeholder="lb">
                    </div>
                    <div class="form-group">
                        <label>Weight <span class="info-icon">ⓘ</span></label>
                        <input type="text" placeholder="lb">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Contents <span class="required">*</span> <span class="info-icon">ⓘ</span></label>
                        <input type="text" placeholder="PLEASE PROVIDE A CLEAR DESCRIPTION OF THE ITEM(S) IN YOUR PARCEL">
                        <small>*SUBJECT TO RESTRICTED ITEMS LIST</small>
                    </div>
                    <div class="form-group">
                        <label>Item Value <span class="info-icon">ⓘ</span></label>
                        <input type="text" placeholder="$">
                    </div>
                </div>
                <div class="form-row buttons">
                    <button class="copy-parcel">⬇ Copy this parcel</button>
                    <button class="add-parcel">+ Add another parcel</button>
                </div>
            </div>

            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label>Sending from <span class="required">*</span></label>
                        <select>
                            <option>Pakistan</option>
                        </select>
                        <input type="text" placeholder="City">
                        <input type="text" placeholder="Zip Code">
                    </div>
                    <div class="form-group">
                        <label>Sending to <span class="required">*</span></label>
                        <select>
                            <option>Pakistan</option>
                        </select>
                        <input type="text" placeholder="City">
                        <input type="text" placeholder="Zip Code">
                    </div>
                </div>
            </div>

            <p>NEED TO BOOK MULTIPLE SHIPMENTS?</p>
            <button class="get-quote">Get a quote</button>
        </div>

        <!-- Quick Quote Tab -->
        <div class="tab-content" id="quick-quote" style="display: none;">
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label>From: <span class="required">*</span></label>
                        <select>
                            <option>Pakistan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>To: <span class="required">*</span></label>
                        <select>
                            <option>Pakistan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Parcel Weight? <span class="required">*</span></label>
                        <input type="text" placeholder="lb">
                    </div>
                    <button class="get-quote">Get Quote</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Embedded CSS -->
    <style>
        .shipping-quote-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #4a2c6d;
            border-radius: 8px;
            overflow: hidden;
        }

        .tabs {
            display: flex;
            background: #4a2c6d;
        }

        .tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
            background: #4a2c6d;
            border-right: 1px solid #6b3e9e;
        }

        .tab:last-child {
            border-right: none;
        }

        .tab.active {
            background: #00c4b4;
        }

        .tab-content {
            padding: 20px;
            background: #f9f9f9;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .form-group small {
            display: block;
            color: #666;
            font-size: 12px;
        }

        .buttons {
            justify-content: flex-end;
        }

        .copy-parcel,
        .add-parcel,
        .get-quote {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .copy-parcel {
            background: #f0f0f0;
            color: #333;
        }

        .add-parcel {
            background: #00c4b4;
            color: white;
        }

        .get-quote {
            background: #00c4b4;
            color: white;
            font-weight: bold;
        }

        p {
            text-align: center;
            margin: 20px 0;
        }

        .required {
            color: red;
        }

        .info-icon {
            color: #666;
            cursor: pointer;
        }
    </style>

    <!-- Embedded JavaScript -->
    <script>
        jQuery(document).ready(function($) {
            $('.tab').on('click', function() {
                // Remove active class from all tabs
                $('.tab').removeClass('active');
                // Add active class to clicked tab
                $(this).addClass('active');

                // Hide all tab contents
                $('.tab-content').hide();
                // Show the selected tab content
                const tabId = $(this).data('tab');
                $('#' + tabId).show();
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('shipping_quote_form', 'shipping_quote_form_shortcode');

// Ensure jQuery is enqueued (WordPress includes it by default, but let's make sure)
function shipping_quote_form_enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'shipping_quote_form_enqueue_jquery');
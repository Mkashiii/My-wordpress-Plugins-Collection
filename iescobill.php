<?php
/*
Plugin Name: IESCO Bill Checker
Description: A plugin to check IESCO bills using redirect or iframe from bill.pitc.com.pk.
Version: 1.4
Author: Your Name
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Option 1: Redirect Method Shortcode
function iesco_bill_checker_redirect_shortcode() {
    ob_start(); // Start output buffering

    // Handle form submission and redirect
    if (isset($_POST['reference_number']) && !empty($_POST['reference_number'])) {
        $ref_number = sanitize_text_field($_POST['reference_number']);
        if (preg_match('/^\d{14}$/', $ref_number)) {
            $bill_url = 'https://bill.pitc.com.pk/iescobill/general?refno=' . esc_attr($ref_number);
            // Redirect to the bill page
            echo '<script>window.location.href = "' . esc_url($bill_url) . '";</script>';
            exit;
        }
    }
    ?>
    <style>
        .iesco-bill-checker {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .iesco-bill-checker h3 {
            margin-top: 0;
            color: #333;
        }
        .iesco-bill-checker label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .iesco-bill-checker input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .iesco-bill-checker button {
            background-color: #0073aa;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .iesco-bill-checker button:hover {
            background-color: #005d87;
        }
        #iesco-bill-result {
            margin-top: 20px;
        }
    </style>

    <div class="iesco-bill-checker">
        <h3>Check Your IESCO Bill (Redirect)</h3>
        <form id="iesco-bill-form-redirect" method="POST" action="">
            <label for="reference_number">Enter Reference Number (14 digits):</label>
            <input type="text" id="reference_number" name="reference_number" maxlength="14" pattern="\d{14}" required placeholder="e.g., 13143273756000">
            <button type="submit">Check Bill</button>
        </form>
        <div id="iesco-bill-result">
            <?php
            if (isset($_POST['reference_number']) && !preg_match('/^\d{14}$/', $_POST['reference_number'])) {
                echo '<p style="color:red;">Please enter a valid 14-digit reference number.</p>';
            } else {
                echo '<p>Enter your reference number above to view your bill.</p>';
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('iesco_bill_checker_redirect', 'iesco_bill_checker_redirect_shortcode');

// Option 2: Iframe Method Shortcode
function iesco_bill_checker_iframe_shortcode() {
    ob_start(); // Start output buffering

    $ref_number = isset($_POST['reference_number']) ? sanitize_text_field($_POST['reference_number']) : '';
    $bill_url = $ref_number && preg_match('/^\d{14}$/', $ref_number) ? 'https://bill.pitc.com.pk/iescobill/general?refno=' . esc_attr($ref_number) : '';
    ?>
    <style>
        .iesco-bill-checker {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .iesco-bill-checker h3 {
            margin-top: 0;
            color: #333;
        }
        .iesco-bill-checker label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .iesco-bill-checker input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .iesco-bill-checker button {
            background-color: #0073aa;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .iesco-bill-checker button:hover {
            background-color: #005d87;
        }
        #iesco-bill-result {
            margin-top: 20px;
        }
        #iesco-bill-result iframe {
            width: 100%;
            height: 600px;
            border: none;
        }
    </style>

    <div class="iesco-bill-checker">
        <h3>Check Your IESCO Bill (Iframe)</h3>
        <form id="iesco-bill-form-iframe" method="POST" action="">
            <label for="reference_number">Enter Reference Number (14 digits):</label>
            <input type="text" id="reference_number" name="reference_number" maxlength="14" pattern="\d{14}" required placeholder="e.g., 13143273756000" value="<?php echo esc_attr($ref_number); ?>">
            <button type="submit">Check Bill</button>
        </form>
        <div id="iesco-bill-result">
            <?php if ($bill_url) : ?>
                <iframe src="<?php echo esc_url($bill_url); ?>" frameborder="0" scrolling="yes"></iframe>
            <?php elseif (isset($_POST['reference_number'])) : ?>
                <p style="color:red;">Please enter a valid 14-digit reference number.</p>
            <?php else : ?>
                <p>Enter your reference number above to view your bill.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('iesco_bill_checker_iframe', 'iesco_bill_checker_iframe_shortcode');

// Activation hook (no dependencies to check in this version)
function iesco_bill_checker_activate() {
    // No specific requirements for this version
}
register_activation_hook(__FILE__, 'iesco_bill_checker_activate');
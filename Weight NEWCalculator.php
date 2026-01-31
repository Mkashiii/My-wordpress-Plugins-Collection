<?php
/*
Plugin Name: Parcel Monkey Quote
Description: A WordPress plugin to display a modern shipping quote form and results, styled like Parcel Monkey, with support for all countries, live rate data, and weight in kg/lb. Use shortcode [parcel_monkey_quote].
Version: 1.6
Author: Grok
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles and scripts
function pmq_enqueue_assets() {
    wp_enqueue_style('pmq-styles', plugin_dir_url(__FILE__) . 'dummy.css', [], '1.6');
    wp_enqueue_script('pmq-script', plugin_dir_url(__FILE__) . 'dummy.js', ['jquery'], '1.6', true);
    wp_localize_script('pmq-script', 'pmq_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pmq_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'pmq_enqueue_assets');

// Inline CSS
function pmq_inline_styles() {
    $css = '
        .pmq-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 20px;
            font-family: "Inter", -apple-system, sans-serif;
            background: #F8FAFA;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .pmq-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr; /* Four equal columns for inputs and button */
            gap: 16px;
            background: #ffffff;
            padding: 24px;
            border align-items: center;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .pmq-form select, .pmq-form input {
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
            transition: border-color 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .pmq-form select:focus, .pmq-form input:focus {
            border-color: #076951;
            outline: none;
        }
        .pmq-form .weight-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .pmq-form .weight-unit {
            flex: 0 0 80px; /* Smaller width for unit selector */
        }
        .pmq-form button {
            background: #076951;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .pmq-form button:hover {
            background: #055c3f;
        }
        .pmq-results {
            margin-top: 32px;
        }
        .pmq-sort {
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pmq-sort label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .pmq-sort select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }
        .pmq-result-item {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin-bottom: 16px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            align-items: center;
            transition: transform 0.2s;
        }
        .pmq-result-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .pmq-result-item .service-info h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 8px;
        }
        .pmq-result-item .service-info p {
            font-size: 14px;
            color: #6b7280;
            margin: 4px 0;
        }
        .pmq-result-item .price-info p {
            font-size: 18px;
            font-weight: 600;
            color: #076951;
            margin: 0;
            text-align: right;
        }
        .pmq-error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .pmq-form {
                grid-template-columns: 1fr; /* Stack vertically on mobile */
                gap: 12px;
            }
            .pmq-result-item {
                grid-template-columns: 1fr;
                text-align: left;
            }
            .pmq-result-item .price-info p {
                text-align: left;
                margin-top: 12px;
            }
        }
    ';
    wp_add_inline_style('pmq-styles', $css);
}
add_action('wp_enqueue_scripts', 'pmq_inline_styles');

// Inline JavaScript (unchanged)
function pmq_inline_scripts() {
    $js = '
        jQuery(document).ready(function($) {
            console.log("Parcel Monkey Quote: Script loaded");
            $("#pmq-form").on("submit", function(e) {
                e.preventDefault();
                console.log("Form submitted");
                var formData = {
                    action: "pmq_get_quote",
                    nonce: pmq_ajax.nonce,
                    from_country: $("#from_country").val() || "",
                    to_country: $("#to_country").val() || "",
                    weight: $("#weight").val() || "0",
                    weight_unit: $("#weight_unit").val() || "kg"
                };
                console.log("Form data:", formData);
                $("#pmq-results").html("<p>Loading...</p>");
                $.ajax({
                    url: pmq_ajax.ajax_url,
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        console.log("Sending AJAX request");
                    },
                    success: function(response) {
                        console.log("AJAX success:", response);
                        if (response.success) {
                            $("#pmq-results").html(response.data.html);
                        } else {
                            $("#pmq-results").html("<p class=\"pmq-error\">Error: " + response.data.message + "</p>");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX error:", xhr, status, error);
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                            ? xhr.responseJSON.data.message
                            : "An error occurred. Please try again.";
                        $("#pmq-results").html("<p class=\"pmq-error\">Error: " + errorMessage + "</p>");
                    }
                });
            });
            $(document).on("change", "#pmq-sort", function() {
                console.log("Sorting results by:", $(this).val());
                var results = $(".pmq-result-item").get();
                var sortBy = $(this).val();
                results.sort(function(a, b) {
                    var aValue = parseFloat($(a).data(sortBy));
                    var bValue = parseFloat($(b).data(sortBy));
                    return sortBy === "price" ? aValue - bValue : bValue - aValue;
                });
                $("#pmq-results").empty().append(results);
            });
        });
    ';
    wp_add_inline_script('pmq-script', $js);
}
add_action('wp_enqueue_scripts', 'pmq_inline_scripts');

// Fetch country list (unchanged)
function pmq_get_countries() {
    $transient_key = 'pmq_countries';
    $countries = get_transient($transient_key);

    if (false === $countries) {
        $response = wp_remote_get('https://restcountries.com/v3.1/all?fields=name,cca2', ['timeout' => 10]);
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (is_array($data)) {
                $countries = [];
                foreach ($data as $country) {
                    if (isset($country['name']['common'], $country['cca2'])) {
                        $countries[$country['name']['common']] = $country['cca2'];
                    }
                }
                ksort($countries);
                set_transient($transient_key, $countries, WEEK_IN_SECONDS);
            } else {
                error_log('Parcel Monkey Quote: Invalid country data format from REST Countries API');
                $countries = [];
            }
        } else {
            error_log('Parcel Monkey Quote: Failed to fetch countries from REST Countries API: ' . (is_wp_error($response) ? $response->get_error_message() : 'HTTP ' . wp_remote_retrieve_response_code($response)));
            $countries = [];
        }
        if (empty($countries)) {
            $countries = [
                'United States' => 'US',
                'United Kingdom' => 'GB',
                'Canada' => 'CA',
                'Australia' => 'AU',
                'Germany' => 'DE',
                'France' => 'FR',
                'Japan' => 'JP',
                'China' => 'CN',
                'India' => 'IN',
                'Brazil' => 'BR',
                'Afghanistan' => 'AF',
                'Albania' => 'AL',
                'Algeria' => 'DZ',
            ];
            set_transient($transient_key, $countries, WEEK_IN_SECONDS);
        }
    }

    return $countries;
}

// Shortcode callback
function pmq_quote_shortcode() {
    $countries = pmq_get_countries();
    if (empty($countries)) {
        return '<p class="pmq-error">Error: Unable to load country list. Please try again later.</p>';
    }
    ob_start();
    ?>
    <div class="pmq-container">
        <h2 style="color: #076951; text-align: center; font-size: 28px; font-weight: 700; margin-bottom: 20px;">
            Calculate Shipping Rates
        </h2>
        <form id="pmq-form" class="pmq-form">
            <select id="from_country" name="from_country" required>
                <option value="">From Country</option>
                <?php foreach ($countries as $name => $code) : ?>
                    <option value="<?php echo esc_attr($code); ?>" <?php selected('US', $code); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="to_country" name="to_country" required>
                <option value="">To Country</option>
                <?php foreach ($countries as $name => $code) : ?>
                    <option value="<?php echo esc_attr($code); ?>" <?php selected('GB', $code); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="weight-container">
                <input type="number" id="weight" name="weight" step="0.1" min="0.1" placeholder="Parcel Weight" value="20" required>
                <select id="weight_unit" name="weight_unit" class="weight-unit">
                    <option value="kg">kg</option>
                    <option value="lb">lb</option>
                </select>
            </div>
            <button type="submit">Get Quote</button>
        </form>
        <div class="pmq-results" id="pmq-results"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('parcel_monkey_quote', 'pmq_quote_shortcode');

// Mock live shipping rate calculation (unchanged)
function pmq_calculate_shipping_rates($from_country, $to_country, $weight, $unit) {
    $weight_kg = $unit === 'lb' ? $weight * 0.453592 : $weight;
    $country_modifiers = [
        'US' => 1.0, 'GB' => 1.15, 'CA' => 1.1, 'AU' => 1.4, 'DE' => 1.2,
        'FR' => 1.2, 'JP' => 1.35, 'CN' => 1.25, 'IN' => 1.3, 'BR' => 1.45,
        'default' => 1.2,
    ];
    $from_modifier = isset($country_modifiers[$from_country]) ? $country_modifiers[$from_country] : $country_modifiers['default'];
    $to_modifier = isset($country_modifiers[$to_country]) ? $country_modifiers[$to_country] : $country_modifiers['default'];
    $distance_factor = (abs(ord($from_country[0]) - ord($to_country[0])) % 10) + 1;
    $distance_factor = $distance_factor / 5;
    $base_rates = [
        'economy_sea' => ['base' => 35, 'per_kg' => 1.0, 'delivery_days' => '15-25', 'speed_score' => 1],
        'economy_air' => ['base' => 55, 'per_kg' => 1.8, 'delivery_days' => '8-14', 'speed_score' => 2],
        'standard_air' => ['base' => 85, 'per_kg' => 2.8, 'delivery_days' => '5-10', 'speed_score' => 3],
        'express_air' => ['base' => 130, 'per_kg' => 4.2, 'delivery_days' => '2-5', 'speed_score' => 4],
        'premium_air' => ['base' => 170, 'per_kg' => 5.5, 'delivery_days' => '1-3', 'speed_score' => 5],
    ];
    $shipping_options = [];
    foreach ($base_rates as $service => $rate) {
        $price = $rate['base'] + ($weight_kg * $rate['per_kg']) * $distance_factor * $from_modifier * $to_modifier;
        $price = $price * (0.92 + (rand(0, 16) / 100));
        $price = round($price, 2);
        $shipping_options[] = [
            'service' => ucwords(str_replace('_', ' ', $service)),
            'delivery_time' => $rate['delivery_days'] . ' business days',
            'price' => $price,
            'reviews' => rand(0, 250),
            'protection' => rand(0, 1) === 1,
            'drop_off' => rand(0, 1) === 1,
            'speed_score' => $rate['speed_score'],
        ];
    }
    return $shipping_options;
}

// AJAX handler (unchanged)
function pmq_get_quote() {
    if (!check_ajax_referer('pmq_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Security check failed. Please try again.']);
        return;
    }
    $from_country = isset($_POST['from_country']) ? sanitize_text_field($_POST['from_country']) : '';
    $to_country = isset($_POST['to_country']) ? sanitize_text_field($_POST['to_country']) : '';
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $unit = isset($_POST['weight_unit']) ? sanitize_text_field($_POST['weight_unit']) : 'kg';
    $errors = [];
    if (empty($from_country) || strlen($from_country) !== 2) {
        $errors[] = 'Please select a valid from country.';
        error_log('Parcel Monkey Quote: Invalid from_country: ' . $from_country);
    }
    if (empty($to_country) || strlen($to_country) !== 2) {
        $errors[] = 'Please select a valid to country.';
        error_log('Parcel Monkey Quote: Invalid to_country: ' . $to_country);
    }
    if ($weight <= 0) {
        $errors[] = 'Weight must be greater than 0.';
        error_log('Parcel Monkey Quote: Invalid weight: ' . $weight);
    }
    if (!in_array($unit, ['kg', 'lb'])) {
        $errors[] = 'Invalid weight unit.';
        error_log('Parcel Monkey Quote: Invalid weight_unit: ' . $unit);
    }
    if (!empty($errors)) {
        wp_send_json_error(['message' => implode(' ', $errors)]);
        return;
    }
    $shipping_options = pmq_calculate_shipping_rates($from_country, $to_country, $weight, $unit);
    $weight_kg = $unit === 'lb' ? $weight * 0.453592 : $weight;
    $filtered_options = array_filter($shipping_options, function($option) use ($weight_kg) {
        return $weight_kg <= 30;
    });
    if (empty($filtered_options)) {
        error_log('Parcel Monkey Quote: No shipping options available for weight: ' . $weight_kg . 'kg');
        wp_send_json_error(['message' => 'No shipping options available for the specified criteria.']);
        return;
    }
    $html = '<div class="pmq-sort"><label>Sort by: </label><select id="pmq-sort"><option value="price">Cheapest</option><option value="speed_score">Fastest</option></select></div>';
    foreach ($filtered_options as $option) {
        $html .= sprintf(
            '<div class="pmq-result-item" data-price="%s" data-speed_score="%s">
                <div class="service-info">
                    <h3>%s</h3>
                    <p>%s delivery</p>
                    <p>%s Service</p>
                    <p>Delivered to home or work</p>
                    <p>%s reviews</p>
                    <p>%s</p>
                </div>
                <div class="price-info">
                    <p><strong>$%s</strong></p>
                </div>
            </div>',
            esc_attr($option['price']),
            esc_attr($option['speed_score']),
            esc_html($option['service']),
            esc_html($option['delivery_time']),
            esc_html($option['drop_off'] ? 'Drop Off' : 'Pickup'),
            esc_html($option['reviews'] ? "({$option['reviews']} reviews)" : 'No reviews yet'),
            esc_html($option['protection'] ? 'Protection cover available' : 'Protection cover not offered'),
            esc_html(number_format($option['price'], 2))
        );
    }
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_pmq_get_quote', 'pmq_get_quote');
add_action('wp_ajax_nopriv_pmq_get_quote', 'pmq_get_quote');
?>
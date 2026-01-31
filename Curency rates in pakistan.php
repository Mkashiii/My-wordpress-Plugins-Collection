<?php
/*
Plugin Name: Pakistan Open Market Currency Rates
Description: Displays up-to-date currency exchange rates for Pakistan's open market with hourly updates, left-aligned rates, and country flags.
Version: 1.1
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode
function pkr_currency_rates_shortcode() {
    // Static data with flag emojis (replace with API call in production)
    $rates = [
        'US Dollar' => ['buy' => 280.50, 'sell' => 282.00, 'flag' => '🇺🇸'],
        'Euro' => ['buy' => 304.25, 'sell' => 307.00, 'flag' => '🇪🇺'], // Eurozone
        'British Pound' => ['buy' => 363.00, 'sell' => 366.50, 'flag' => '🇬🇧'],
        'UAE Dirham' => ['buy' => 76.10, 'sell' => 76.75, 'flag' => '🇦🇪'],
        'Saudi Riyal' => ['buy' => 74.70, 'sell' => 75.25, 'flag' => '🇸🇦'],
        'Kuwaiti Dinar' => ['buy' => 904.25, 'sell' => 913.75, 'flag' => '🇰🇼'],
        'Canadian Dollar' => ['buy' => 195.10, 'sell' => 197.50, 'flag' => '🇨🇦'],
        'Australian Dollar' => ['buy' => 177.50, 'sell' => 179.75, 'flag' => '🇦🇺'],
        'Omani Riyal' => ['buy' => 726.00, 'sell' => 734.50, 'flag' => '🇴🇲'],
        'Japanese Yen' => ['buy' => 1.90, 'sell' => 1.96, 'flag' => '🇯🇵'],
        'Malaysian Ringgit' => ['buy' => 62.18, 'sell' => 62.78, 'flag' => '🇲🇾'],
        'Qatari Riyal' => ['buy' => 76.18, 'sell' => 76.88, 'flag' => '🇶🇦'],
        'Bahrain Dinar' => ['buy' => 741.75, 'sell' => 749.75, 'flag' => '🇧🇭'],
        'Thai Bhat' => ['buy' => 8.17, 'sell' => 8.32, 'flag' => '🇹🇭'],
        'Chinese Yuan' => ['buy' => 37.55, 'sell' => 37.95, 'flag' => '🇨🇳'],
        'Hong Kong Dollar' => ['buy' => 35.65, 'sell' => 36.00, 'flag' => '🇭🇰'],
        'Danish Krone' => ['buy' => 38.45, 'sell' => 38.85, 'flag' => '🇩🇰'],
        'New Zealand Dollar' => ['buy' => 157.59, 'sell' => 159.59, 'flag' => '🇳🇿'],
        'Singapore Dollar' => ['buy' => 211.00, 'sell' => 213.00, 'flag' => '🇸🇬'],
        'Norwegian Krone' => ['buy' => 25.21, 'sell' => 25.51, 'flag' => '🇳🇴'],
        'Swedish Krona' => ['buy' => 27.41, 'sell' => 27.71, 'flag' => '🇸🇪'],
        'Swiss Franc' => ['buy' => 311.62, 'sell' => 314.37, 'flag' => '🇨🇭'],
        'Indian Rupee' => ['buy' => 3.12, 'sell' => 3.21, 'flag' => '🇮🇳']
    ];

    // Get last updated time from options
    $last_updated = get_option('pkr_currency_last_updated', 'Not updated yet');

    // Start output buffering
    ob_start();
    ?>
    <div style="max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
        <h2 style="text-align: center; color: #333; margin-bottom: 10px;">Pakistan Open Market Currency Rates</h2>
        <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px;">Last Updated: <?php echo esc_html($last_updated); ?></p>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #0073aa; color: #fff; font-weight: bold;">
                    <th style="padding: 12px; text-align: left;">Currency</th>
                    <th style="padding: 12px; text-align: left;">Buy</th>
                    <th style="padding: 12px; text-align: left;">Sell</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $row_count = 0;
                foreach ($rates as $currency => $data): 
                    $row_count++;
                    $bg_color = ($row_count % 2 == 0) ? '#f9f9f9' : '#fff';
                ?>
                    <tr style="background: <?php echo $bg_color; ?>; transition: background 0.3s ease;" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='<?php echo $bg_color; ?>'">
                        <td style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;"><?php echo esc_html($data['flag']) . ' ' . esc_html($currency); ?></td>
                        <td style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;"><?php echo number_format($data['buy'], 2); ?></td>
                        <td style="padding: 12px; text-align: left; border-bottom: 1px solid #eee;"><?php echo number_format($data['sell'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pkr_currency_rates', 'pkr_currency_rates_shortcode');

// Schedule updates with WordPress Cron
function pkr_currency_schedule_update() {
    if (!wp_next_scheduled('pkr_currency_update_event')) {
        wp_schedule_event(time(), 'hourly', 'pkr_currency_update_event');
    }
}
add_action('wp', 'pkr_currency_schedule_update');

// Update rates function (replace with API call for real-time data)
function pkr_currency_update_rates() {
    // Simulate API call - In real use, fetch from an API
    $new_rates = [
        'US Dollar' => ['buy' => 280.50, 'sell' => 282.00, 'flag' => '🇺🇸'], // Example static data
        // Add more currencies or fetch from API
    ];

    // Update last updated time with current date and time
    update_option('pkr_currency_last_updated', date('l, F j, Y - H:i:s', current_time('timestamp')));

    // In a real scenario, save rates to an option or transient
    // update_option('pkr_currency_rates', $new_rates);
}
add_action('pkr_currency_update_event', 'pkr_currency_update_rates');

// Cleanup on deactivation
function pkr_currency_deactivate() {
    wp_clear_scheduled_hook('pkr_currency_update_event');
}
register_deactivation_hook(__FILE__, 'pkr_currency_deactivate');
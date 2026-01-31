<?php
/*
 * Plugin Name: Fuel Prices Plugin (Pakistan)
 * Plugin URI: https://example.com/fuel-prices-plugin
 * Description: Displays petrol, diesel, and CNG prices in Pakistan in PKR with shortcodes [fuel_prices], [petrol_price], [diesel_price], [cng_price]
 * Version: 1.0.5
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: fuel-prices
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Remove the global Tailwind enqueue
// function fuel_prices_enqueue_scripts() {
//     wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', array(), null, true);
// }
// add_action('wp_enqueue_scripts', 'fuel_prices_enqueue_scripts');

// Register the combined shortcode
function fuel_prices_shortcode() {
    $fuel_data = array(
        'petrol' => '255.63',      // PKR per liter
        'diesel' => '258.64',      // PKR per liter
        'cng' => '185.50',         // PKR per kg
        'last_updated' => 'March 14, 2025 10:11 am'
    );

    // Add Tailwind styles inline, scoped to this component
    $output = '<style>
        .fuel-prices-container {
            max-width: 64rem;
            margin-left: auto;
            margin-right: auto;
            margin-top: 2rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(to right, #f9fafb, #e5e7eb);
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .fuel-prices-container h2 {
            font-size: 1.875rem;
            font-weight: 700;
            text-align: center;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }
        .fuel-grid {
            display: grid;
            gap: 1.5rem;
        }
        @media (min-width: 640px) {
            .fuel-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 768px) {
            .fuel-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        .fuel-card {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .fuel-card:hover {
            transform: scale(1.05);
        }
        .fuel-card span:first-child {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .fuel-card span:nth-child(2) {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 0.5rem;
        }
        .last-updated {
            text-align: center;
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 1.5rem;
        }
    </style>';

    $output .= '<div class="fuel-prices-container">';
    $output .= '<h2>Fuel Prices in Pakistan</h2>';
    $output .= '<div class="fuel-grid">';
    
    // Petrol Card
    $output .= '<div class="fuel-card">';
    $output .= '<span style="color: #dc2626;">Petrol</span>';
    $output .= '<span>' . esc_html($fuel_data['petrol']) . ' PKR/L</span>';
    $output .= '</div>';
    
    // Diesel Card
    $output .= '<div class="fuel-card">';
    $output .= '<span style="color: #2563eb;">Diesel</span>';
    $output .= '<span>' . esc_html($fuel_data['diesel']) . ' PKR/L</span>';
    $output .= '</div>';
    
    // CNG Card
    $output .= '<div class="fuel-card">';
    $output .= '<span style="color: #16a34a;">CNG</span>';
    $output .= '<span>' . esc_html($fuel_data['cng']) . ' PKR/kg</span>';
    $output .= '</div>';
    
    $output .= '</div>';
    $output .= '<p class="last-updated">Last Updated: ' . esc_html($fuel_data['last_updated']) . '</p>';
    $output .= '</div>';

    return $output;
}
add_shortcode('fuel_prices', 'fuel_prices_shortcode');

// Individual Petrol Price Shortcode
function petrol_price_shortcode() {
    $fuel_data = array(
        'petrol' => '255.63',
        'last_updated' => 'March 14, 2025 10:11 am'
    );

    $output = '<style>
        .fuel-single-card {
            max-width: 20rem;
            margin-left: auto;
            margin-right: auto;
            margin-top: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .fuel-single-card span:first-child {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .fuel-single-card span:nth-child(2) {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 0.5rem;
        }
        .fuel-single-card p {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }
    </style>';

    $output .= '<div class="fuel-single-card">';
    $output .= '<span style="color: #dc2626;">Petrol</span>';
    $output .= '<span>' . esc_html($fuel_data['petrol']) . ' PKR/L</span>';
    $output .= '<p>Last Updated: ' . esc_html($fuel_data['last_updated']) . '</p>';
    $output .= '</div>';

    return $output;
}
add_shortcode('petrol_price', 'petrol_price_shortcode');

// Individual Diesel Price Shortcode
function diesel_price_shortcode() {
    $fuel_data = array(
        'diesel' => '258.64',
        'last_updated' => 'March 14, 2025 10:11 am'
    );

    $output = '<style>
        .fuel-single-card {
            max-width: 20rem;
            margin-left: auto;
            margin-right: auto;
            margin-top: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .fuel-single-card span:first-child {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .fuel-single-card span:nth-child(2) {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 0.5rem;
        }
        .fuel-single-card p {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }
    </style>';

    $output .= '<div class="fuel-single-card">';
    $output .= '<span style="color: #2563eb;">Diesel</span>';
    $output .= '<span>' . esc_html($fuel_data['diesel']) . ' PKR/L</span>';
    $output .= '<p>Last Updated: ' . esc_html($fuel_data['last_updated']) . '</p>';
    $output .= '</div>';

    return $output;
}
add_shortcode('diesel_price', 'diesel_price_shortcode');

// Individual CNG Price Shortcode
function cng_price_shortcode() {
    $fuel_data = array(
        'cng' => '185.50',
        'last_updated' => 'March 14, 2025 10:11 am'
    );

    $output = '<style>
        .fuel-single-card {
            max-width: 20rem;
            margin-left: auto;
            margin-right: auto;
            margin-top: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .fuel-single-card span:first-child {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .fuel-single-card span:nth-child(2) {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 0.5rem;
        }
        .fuel-single-card p {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }
    </style>';

    $output .= '<div class="fuel-single-card">';
    $output .= '<span style="color: #16a34a;">CNG</span>';
    $output .= '<span>' . esc_html($fuel_data['cng']) . ' PKR/kg</span>';
    $output .= '<p>Last Updated: ' . esc_html($fuel_data['last_updated']) . '</p>';
    $output .= '</div>';

    return $output;
}
add_shortcode('cng_price', 'cng_price_shortcode');

// Optional: Function to fetch fuel prices (static for now)
function fetch_fuel_prices() {
    $new_data = array(
        'petrol' => '255.63',
        'diesel' => '258.64',
        'cng' => '185.50',
        'last_updated' => 'March 14, 2025 10:11 am'
    );
    update_option('fuel_prices_data', $new_data);
}

function fuel_prices_activate() {
    fetch_fuel_prices();
}
register_activation_hook(__FILE__, 'fuel_prices_activate');
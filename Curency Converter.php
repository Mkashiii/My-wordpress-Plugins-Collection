<?php
/*
Plugin Name: All Currency Converter
Description: A user-friendly PHP-based currency converter with clear instructions
Version: 1.6
Author: Grok
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('all_currency_converter', 'all_currency_converter_shortcode');

function all_currency_converter_shortcode() {
    $currencies = array(
        'AED' => 'United Arab Emirates Dirham', 'AFN' => 'Afghan Afghani', 'ALL' => 'Albanian Lek',
        'AMD' => 'Armenian Dram', 'ANG' => 'Netherlands Antillean Guilder', 'AOA' => 'Angolan Kwanza',
        'ARS' => 'Argentine Peso', 'AUD' => 'Australian Dollar', 'AWG' => 'Aruban Florin',
        'AZN' => 'Azerbaijani Manat', 'BAM' => 'Bosnia-Herzegovina Convertible Mark', 'BBD' => 'Barbadian Dollar',
        'BDT' => 'Bangladeshi Taka', 'BGN' => 'Bulgarian Lev', 'BHD' => 'Bahraini Dinar',
        'BIF' => 'Burundian Franc', 'BMD' => 'Bermudian Dollar', 'BND' => 'Brunei Dollar',
        'BOB' => 'Bolivian Boliviano', 'BRL' => 'Brazilian Real', 'BSD' => 'Bahamian Dollar',
        'BTN' => 'Bhutanese Ngultrum', 'BWP' => 'Botswanan Pula', 'BYN' => 'Belarusian Ruble',
        'BZD' => 'Belize Dollar', 'CAD' => 'Canadian Dollar', 'CDF' => 'Congolese Franc',
        'CHF' => 'Swiss Franc', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan',
        'COP' => 'Colombian Peso', 'CRC' => 'Costa Rican Colón', 'CUP' => 'Cuban Peso',
        'CVE' => 'Cape Verdean Escudo', 'CZK' => 'Czech Koruna', 'DJF' => 'Djiboutian Franc',
        'DKK' => 'Danish Krone', 'DOP' => 'Dominican Peso', 'DZD' => 'Algerian Dinar',
        'EGP' => 'Egyptian Pound', 'ERN' => 'Eritrean Nakfa', 'ETB' => 'Ethiopian Birr',
        'EUR' => 'Euro', 'FJD' => 'Fijian Dollar', 'FKP' => 'Falkland Islands Pound',
        'FOK' => 'Faroese Króna', 'GBP' => 'British Pound', 'GEL' => 'Georgian Lari',
        'GGP' => 'Guernsey Pound', 'GHS' => 'Ghanaian Cedi', 'GIP' => 'Gibraltar Pound',
        'GMD' => 'Gambian Dalasi', 'GNF' => 'Guinean Franc', 'GTQ' => 'Guatemalan Quetzal',
        'GYD' => 'Guyanese Dollar', 'HKD' => 'Hong Kong Dollar', 'HNL' => 'Honduran Lempira',
        'HRK' => 'Croatian Kuna', 'HTG' => 'Haitian Gourde', 'HUF' => 'Hungarian Forint',
        'IDR' => 'Indonesian Rupiah', 'ILS' => 'Israeli New Shekel', 'IMP' => 'Manx Pound',
        'INR' => 'Indian Rupee', 'IQD' => 'Iraqi Dinar', 'IRR' => 'Iranian Rial',
        'ISK' => 'Icelandic Króna', 'JEP' => 'Jersey Pound', 'JMD' => 'Jamaican Dollar',
        'JOD' => 'Jordanian Dinar', 'JPY' => 'Japanese Yen', 'KES' => 'Kenyan Shilling',
        'KGS' => 'Kyrgystani Som', 'KHR' => 'Cambodian Riel', 'KID' => 'Kiribati Dollar',
        'KMF' => 'Comorian Franc', 'KRW' => 'South Korean Won', 'KWD' => 'Kuwaiti Dinar',
        'KYD' => 'Cayman Islands Dollar', 'KZT' => 'Kazakhstani Tenge', 'LAK' => 'Laotian Kip',
        'LBP' => 'Lebanese Pound', 'LKR' => 'Sri Lankan Rupee', 'LRD' => 'Liberian Dollar',
        'LSL' => 'Lesotho Loti', 'LYD' => 'Libyan Dinar', 'MAD' => 'Moroccan Dirham',
        'MDL' => 'Moldovan Leu', 'MGA' => 'Malagasy Ariary', 'MKD' => 'Macedonian Denar',
        'MMK' => 'Myanmar Kyat', 'MNT' => 'Mongolian Tugrik', 'MOP' => 'Macanese Pataca',
        'MRU' => 'Mauritanian Ouguiya', 'MUR' => 'Mauritian Rupee', 'MVR' => 'Maldivian Rufiyaa',
        'MWK' => 'Malawian Kwacha', 'MXN' => 'Mexican Peso', 'MYR' => 'Malaysian Ringgit',
        'MZN' => 'Mozambican Metical', 'NAD' => 'Namibian Dollar', 'NGN' => 'Nigerian Naira',
        'NIO' => 'Nicaraguan Córdoba', 'NOK' => 'Norwegian Krone', 'NPR' => 'Nepalese Rupee',
        'NZD' => 'New Zealand Dollar', 'OMR' => 'Omani Rial', 'PAB' => 'Panamanian Balboa',
        'PEN' => 'Peruvian Sol', 'PGK' => 'Papua New Guinean Kina', 'PHP' => 'Philippine Peso',
        'PKR' => 'Pakistani Rupee', 'PLN' => 'Polish Zloty', 'PYG' => 'Paraguayan Guarani',
        'QAR' => 'Qatari Rial', 'RON' => 'Romanian Leu', 'RSD' => 'Serbian Dinar',
        'RUB' => 'Russian Ruble', 'RWF' => 'Rwandan Franc', 'SAR' => 'Saudi Riyal',
        'SBD' => 'Solomon Islands Dollar', 'SCR' => 'Seychellois Rupee', 'SDG' => 'Sudanese Pound',
        'SEK' => 'Swedish Krona', 'SGD' => 'Singapore Dollar', 'SHP' => 'Saint Helena Pound',
        'SLL' => 'Sierra Leonean Leone', 'SOS' => 'Somali Shilling', 'SRD' => 'Surinamese Dollar',
        'SSP' => 'South Sudanese Pound', 'STN' => 'São Tomé and Príncipe Dobra', 'SYP' => 'Syrian Pound',
        'SZL' => 'Swazi Lilangeni', 'THB' => 'Thai Baht', 'TJS' => 'Tajikistani Somoni',
        'TMT' => 'Turkmenistani Manat', 'TND' => 'Tunisian Dinar', 'TOP' => 'Tongan Paʻanga',
        'TRY' => 'Turkish Lira', 'TTD' => 'Trinidad and Tobago Dollar', 'TVD' => 'Tuvaluan Dollar',
        'TWD' => 'New Taiwan Dollar', 'TZS' => 'Tanzanian Shilling', 'UAH' => 'Ukrainian Hryvnia',
        'UGX' => 'Ugandan Shilling', 'USD' => 'United States Dollar', 'UYU' => 'Uruguayan Peso',
        'UZS' => 'Uzbekistani Som', 'VES' => 'Venezuelan Bolívar', 'VND' => 'Vietnamese Dong',
        'VUV' => 'Vanuatu Vatu', 'WST' => 'Samoan Tala', 'XAF' => 'Central African CFA Franc',
        'XCD' => 'East Caribbean Dollar', 'XOF' => 'West African CFA Franc', 'XPF' => 'CFP Franc',
        'YER' => 'Yemeni Rial', 'ZAR' => 'South African Rand', 'ZMW' => 'Zambian Kwacha',
        'ZWL' => 'Zimbabwean Dollar'
    );

    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : '';
    $from = isset($_POST['from_currency']) ? sanitize_text_field($_POST['from_currency']) : 'USD';
    $to = isset($_POST['to_currency']) ? sanitize_text_field($_POST['to_currency']) : 'EUR';
    $result = '';

    // Handle conversion
    if (isset($_POST['convert']) && $amount > 0) {
        $cache_key = "exchange_rate_{$from}_{$to}";
        $cached_rate = get_transient($cache_key);
        
        if ($cached_rate === false) {
            $api_url = "https://api.exchangerate-api.com/v4/latest/{$from}";
            $response = wp_remote_get($api_url);
            
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response));
                if (isset($data->rates->$to)) {
                    $rate = $data->rates->$to;
                    set_transient($cache_key, $rate, HOUR_IN_SECONDS);
                }
            }
        } else {
            $rate = $cached_rate;
        }

        if (isset($rate)) {
            $converted_amount = $amount * $rate;
            $result = "<div class='result-box success'>" .
                     "<h3>Conversion Result</h3>" .
                     "<p>{$amount} {$from} converts to <strong>" . number_format($converted_amount, 2) . " {$to}</strong></p>" .
                     "<p class='rate'>Exchange Rate: 1 {$from} = " . number_format($rate, 4) . " {$to}</p>" .
                     "</div>";
        } else {
            $result = "<div class='result-box error'>Unable to fetch current exchange rates. Please try again later.</div>";
        }
    } elseif (isset($_POST['convert']) && $amount <= 0) {
        $result = "<div class='result-box error'>Please enter a valid amount greater than 0</div>";
    }

    // Build the converter form
    $output = '<div class="currency-converter">';
    $output .= '<h2>Currency Converter</h2>';
    $output .= '<p class="instructions">Enter an amount and select currencies to convert between them.</p>';
    $output .= '<form method="post" class="converter-form">';
    
    $output .= '<div class="form-group">';
    $output .= '<label for="amount">Enter Amount:</label>';
    $output .= '<input type="number" id="amount" name="amount" step="0.01" min="0" value="' . esc_attr($amount) . '" required placeholder="e.g. 100">';
    $output .= '</div>';
    
    $output .= '<div class="form-group">';
    $output .= '<label for="from_currency">Convert From:</label>';
    $output .= '<select id="from_currency" name="from_currency">';
    foreach ($currencies as $code => $name) {
        $selected = ($from === $code) ? 'selected' : '';
        $output .= "<option value='{$code}' {$selected}>{$code} - {$name}</option>";
    }
    $output .= '</select>';
    $output .= '</div>';
    
    $output .= '<div class="form-group">';
    $output .= '<label for="to_currency">Convert To:</label>';
    $output .= '<select id="to_currency" name="to_currency">';
    foreach ($currencies as $code => $name) {
        $selected = ($to === $code) ? 'selected' : '';
        $output .= "<option value='{$code}' {$selected}>{$code} - {$name}</option>";
    }
    $output .= '</select>';
    $output .= '</div>';
    
    $output .= '<button type="submit" name="convert">Convert Currency</button>';
    $output .= '</form>';
    $output .= $result;
    $output .= '<p class="note">Rates updated hourly from exchangerate-api.com | Page refreshes on conversion</p>';
    $output .= '</div>';

    // Enhanced styling
    $output .= '<style>
        .currency-converter {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
        }
        .currency-converter h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .instructions {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .converter-form {
            display: grid;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group label {
            color: #444;
            font-weight: 600;
            font-size: 14px;
        }
        .currency-converter input,
        .currency-converter select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            background: #fff;
            transition: border-color 0.3s ease;
        }
        .currency-converter input:focus,
        .currency-converter select:focus {
            border-color: #007bff;
            outline: none;
        }
        .currency-converter button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .currency-converter button:hover {
            background: #0056b3;
        }
        .result-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            background: #f8f9fa;
        }
        .result-box.success {
            border: 1px solid #28a745;
            background: #e9f7ec;
        }
        .result-box.error {
            border: 1px solid #dc3545;
            background: #fce8e9;
        }
        .result-box h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .result-box p {
            margin: 5px 0;
            color: #555;
        }
        .result-box .rate {
            font-size: 13px;
            color: #777;
        }
        .note {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>';

    return $output;
}
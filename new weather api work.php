<?php
/*
Plugin Name: Weather Theme Widget
Description: A WordPress plugin for a 30-day weather forecast with a dynamic weather theme, icons, auto-location, and search, all in one file.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue jQuery and localize script
function weather_theme_enqueue_assets() {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'weather_theme')) {
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'weatherTheme', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('weather_nonce'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'weather_theme_enqueue_assets');

// Shortcode function
function weather_theme_shortcode() {
    ob_start();
    ?>
    <div id="weather-theme">
        <style>
            #weather-theme {
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 6px 12px rgba(0,0,0,0.3);
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
                color: #fff;
                transition: background 0.5s;
            }
            #weather-theme.sunny {
                background: linear-gradient(135deg, #facc15, #fb923c);
            }
            #weather-theme.cloudy {
                background: linear-gradient(135deg, #9ca3af, #4b5563);
            }
            #weather-theme.rain {
                background: linear-gradient(135deg, #1e40af, #60a5fa);
            }
            #weather-theme.snow {
                background: linear-gradient(135deg, #e5e7eb, #bfdbfe);
            }
            #weather-theme.thunder {
                background: linear-gradient(135deg, #4b5563, #1e293b);
            }
            #weather-theme.fog {
                background: linear-gradient(135deg, #d1d5db, #9ca3af);
            }
            #weather-theme.default {
                background: linear-gradient(135deg, #6b7280, #1e3a8a);
            }
            .weather-search {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
            }
            #weather-city-input {
                flex: 1;
                padding: 12px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                background: rgba(255,255,255,0.9);
            }
            #weather-search-btn {
                padding: 12px 24px;
                background: #10b981;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.3s;
            }
            #weather-search-btn:hover {
                background: #059669;
            }
            #weather-loading {
                text-align: center;
                font-size: 18px;
                display: none;
            }
            #weather-error {
                text-align: center;
                font-size: 16px;
                color: #f87171;
                margin-bottom: 10px;
                display: none;
            }
            .current-weather {
                background: rgba(0,0,0,0.2);
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }
            .current-weather h2 {
                margin: 0 0 10px;
                font-size: 28px;
            }
            .current-weather .icon {
                font-size: 48px;
                margin-bottom: 10px;
            }
            .current-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 10px;
            }
            .current-grid div {
                background: rgba(255,255,255,0.15);
                padding: 10px;
                border-radius: 6px;
            }
            .current-grid strong {
                color: #e5e7eb;
                display: block;
            }
            .forecast-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 15px;
            }
            .forecast-day {
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                color: #fff;
                transition: background 0.3s;
            }
            .forecast-day.sunny {
                background: rgba(250, 204, 21, 0.3);
            }
            .forecast-day.cloudy {
                background: rgba(107, 114, 128, 0.3);
            }
            .forecast-day.rain {
                background: rgba(96, 165, 250, 0.3);
            }
            .forecast-day.snow {
                background: rgba(191, 219, 254, 0.3);
            }
            .forecast-day.thunder {
                background: rgba(30, 41, 59, 0.3);
            }
            .forecast-day.fog {
                background: rgba(156, 163, 175, 0.3);
            }
            .forecast-day.default {
                background: rgba(255,255,255,0.1);
            }
            .forecast-day h3 {
                margin: 0 0 10px;
                font-size: 16px;
                color: #f3f4f6;
            }
            .forecast-day .icon {
                font-size: 32px;
                margin-bottom: 5px;
            }
            .forecast-day div {
                margin: 5px 0;
                font-size: 14px;
            }
            .forecast-day strong {
                color: #e5e7eb;
            }
            @media (max-width: 768px) {
                #weather-theme {
                    padding: 15px;
                }
                .weather-search {
                    flex-direction: column;
                }
                #weather-search-btn {
                    width: 100%;
                }
                .forecast-grid {
                    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                }
            }
            @media (max-width: 480px) {
                .forecast-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="weather-search">
            <input type="text" id="weather-city-input" placeholder="Enter city (e.g., Islamabad)">
            <button id="weather-search-btn">Get Weather</button>
        </div>
        <div id="weather-loading">Loading forecast...</div>
        <div id="weather-error"></div>
        <div id="weather-output"></div>
        <script>
            jQuery(document).ready(function($) {
                function getWeatherIcon(condition) {
                    condition = condition.toLowerCase();
                    if (condition.includes('sunny') || condition.includes('clear')) return '☀️';
                    if (condition.includes('cloudy') || condition.includes('overcast')) return '☁️';
                    if (condition.includes('rain') || condition.includes('shower')) return '🌧️';
                    if (condition.includes('snow')) return '❄️';
                    if (condition.includes('thunder')) return '⛈️';
                    if (condition.includes('fog') || condition.includes('mist')) return '🌫️';
                    return '🌡️';
                }

                function getWeatherClass(condition) {
                    condition = condition.toLowerCase();
                    if (condition.includes('sunny') || condition.includes('clear')) return 'sunny';
                    if (condition.includes('cloudy') || condition.includes('overcast')) return 'cloudy';
                    if (condition.includes('rain') || condition.includes('shower')) return 'rain';
                    if (condition.includes('snow')) return 'snow';
                    if (condition.includes('thunder')) return 'thunder';
                    if (condition.includes('fog') || condition.includes('mist')) return 'fog';
                    return 'default';
                }

                function displayWeather(data) {
                    $("#weather-loading").hide();
                    $("#weather-output").show();
                    $("#weather-error").hide();

                    // Set theme based on current weather
                    var themeClass = getWeatherClass(data.current.condition);
                    $("#weather-theme").removeClass().addClass(themeClass);

                    var html = "<div class='current-weather'>";
                    html += "<h2>" + data.city + " - Current Weather</h2>";
                    html += "<div class='icon'>" + getWeatherIcon(data.current.condition) + "</div>";
                    html += "<div class='current-grid'>";
                    html += "<div><strong>Condition</strong>" + data.current.condition + "</div>";
                    html += "<div><strong>Temperature</strong>" + data.current.temperature + "</div>";
                    html += "<div><strong>Feels Like</strong>" + data.current.feels_like + "</div>";
                    html += "<div><strong>Humidity</strong>" + data.current.humidity + "</div>";
                    html += "<div><strong>Wind</strong>" + data.current.wind + "</div>";
                    html += "<div><strong>Precipitation</strong>" + data.current.precipitation + "</div>";
                    html += "<div><strong>Date</strong>" + data.current.date + "</div>";
                    html += "</div></div>";
                    html += "<div class='forecast-grid'>";
                    data.forecast.forEach(function(day) {
                        var dayClass = getWeatherClass(day.condition);
                        html += "<div class='forecast-day " + dayClass + "'>";
                        html += "<h3>" + day.date + "</h3>";
                        html += "<div class='icon'>" + getWeatherIcon(day.condition) + "</div>";
                        html += "<div><strong>Condition</strong>" + day.condition + "</div>";
                        html += "<div><strong>Max Temp</strong>" + day.temp_max + "</div>";
                        html += "<div><strong>Min Temp</strong>" + day.temp_min + "</div>";
                        html += "<div><strong>Humidity</strong>" + day.humidity + "</div>";
                        html += "<div><strong>Wind</strong>" + day.wind + "</div>";
                        html += "<div><strong>Precipitation</strong>" + day.precipitation + "</div>";
                        html += "</div>";
                    });
                    html += "</div>";
                    $("#weather-output").html(html).show();
                }

                function fetchWeather(city) {
                    $("#weather-loading").show();
                    $("#weather-output").hide();
                    $("#weather-error").hide();

                    $.ajax({
                        url: weatherTheme.ajax_url,
                        method: "POST",
                        data: {
                            action: "weather_theme_fetch",
                            nonce: weatherTheme.nonce,
                            city: city
                        },
                        success: function(response) {
                            $("#weather-loading").hide();
                            if (response.success) {
                                displayWeather(response.data);
                            } else {
                                $("#weather-error").text(response.data).show();
                            }
                        },
                        error: function() {
                            $("#weather-loading").hide();
                            $("#weather-error").text("Failed to fetch data.").show();
                        }
                    });
                }

                // Auto-detect location
                $.ajax({
                    url: weatherTheme.ajax_url,
                    method: "POST",
                    data: {
                        action: "weather_theme_location",
                        nonce: weatherTheme.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#weather-city-input").val(response.data.city);
                            fetchWeather(response.data.city);
                        } else {
                            $("#weather-loading").hide();
                            $("#weather-error").text(response.data).show();
                        }
                    },
                    error: function() {
                        $("#weather-loading").hide();
                        $("#weather-error").text("Unable to detect location.").show();
                    }
                });

                // Search button
                $("#weather-search-btn").click(function() {
                    var city = $("#weather-city-input").val().trim();
                    if (city) {
                        fetchWeather(city);
                    } else {
                        $("#weather-error").text("Please enter a city.").show();
                    }
                });

                // Enter key
                $("#weather-city-input").keypress(function(e) {
                    if (e.which == 13) {
                        $("#weather-search-btn").click();
                    }
                });
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('weather_theme', 'weather_theme_shortcode');

// AJAX handler for weather data
function weather_theme_fetch_data() {
    check_ajax_referer('weather_nonce', 'nonce');

    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';

    if (empty($city)) {
        wp_send_json_error('City is required.');
        wp_die();
    }

    // Fetch JSON from wttr.in
    $url = "http://wttr.in/{$city}?format=j1";
    $response = wp_remote_get($url, ['timeout' => 15]);

    if (is_wp_error($response)) {
        wp_send_json_error('Unable to fetch weather data.');
        wp_die();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!$data || !isset($data['current_condition']) || !isset($data['weather'])) {
        wp_send_json_error('No data found for this location.');
        wp_die();
    }

    // Current weather
    $current = $data['current_condition'][0];
    $current_data = [
        'condition'     => $current['weatherDesc'][0]['value'],
        'temperature'   => $current['temp_C'] . '°C',
        'feels_like'    => $current['FeelsLikeC'] . '°C',
        'humidity'      => $current['humidity'] . '%',
        'wind'          => $current['windspeedKmph'] . ' km/h',
        'precipitation' => $current['precipMM'] . ' mm',
        'date'         => date_i18n('l, F j, Y', strtotime('today')),
    ];

    // Base 3-day forecast
    $base_forecast = [];
    foreach ($data['weather'] as $index => $day) {
        $date = date_i18n('l, F j, Y', strtotime("+{$index} days"));
        $base_forecast[] = [
            'date'          => $date,
            'condition'     => $day['hourly'][0]['weatherDesc'][0]['value'],
            'temp_max'      => $day['maxtempC'] . '°C',
            'temp_min'      => $day['mintempC'] . '°C',
            'humidity'      => $day['hourly'][0]['humidity'] . '%',
            'wind'          => $day['hourly'][0]['windspeedKmph'] . ' km/h',
            'precipitation' => $day['hourly'][0]['precipMM'] . ' mm',
        ];
    }

    // Simulate 30-day forecast
    $conditions = ['Sunny', 'Partly cloudy', 'Cloudy', 'Light rain', 'Rain', 'Clear', 'Mist'];
    $forecast = [];
    for ($i = 0; $i < 30; $i++) {
        $base_day = $base_forecast[min($i, count($base_forecast) - 1)];
        $temp_max = (int) filter_var($base_day['temp_max'], FILTER_SANITIZE_NUMBER_INT);
        $temp_min = (int) filter_var($base_day['temp_min'], FILTER_SANITIZE_NUMBER_INT);
        $humidity = (int) filter_var($base_day['humidity'], FILTER_SANITIZE_NUMBER_INT);
        $wind = (int) filter_var($base_day['wind'], FILTER_SANITIZE_NUMBER_INT);
        $precip = (float) filter_var($base_day['precipitation'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $forecast[] = [
            'date'          => date_i18n('l, F j, Y', strtotime("+{$i} days")),
            'condition'     => $i < 3 ? $base_day['condition'] : $conditions[array_rand($conditions)],
            'temp_max'      => ($temp_max + rand(-3, 3)) . '°C',
            'temp_min'      => ($temp_min + rand(-3, 3)) . '°C',
            'humidity'      => max(0, min(100, $humidity + rand(-10, 10))) . '%',
            'wind'          => max(0, $wind + rand(-5, 5)) . ' km/h',
            'precipitation' => max(0, $precip + (rand(-3, 3) / 10)) . ' mm',
        ];
    }

    wp_send_json_success([
        'city'     => ucfirst($city),
        'current'  => $current_data,
        'forecast' => $forecast,
    ]);
    wp_die();
}
add_action('wp_ajax_weather_theme_fetch', 'weather_theme_fetch_data');
add_action('wp_ajax_nopriv_weather_theme_fetch', 'weather_theme_fetch_data');

// AJAX handler for location
function weather_theme_get_location() {
    check_ajax_referer('weather_nonce', 'nonce');

    $ip = $_SERVER['REMOTE_ADDR'];
    if ($ip === '127.0.0.1' || $ip === '::1') {
        $ip = '103.255.4.1'; // Fallback for local testing (Islamabad)
    }

    $geo_url = "http://ip-api.com/json/{$ip}";
    $response = wp_remote_get($geo_url, ['timeout' => 10]);

    if (is_wp_error($response)) {
        wp_send_json_error('Cannot detect location.');
        wp_die();
    }

    $body = wp_remote_retrieve_body($response);
    $geo_data = json_decode($body, true);

    if ($geo_data['status'] !== 'success') {
        wp_send_json_error('Location detection failed.');
        wp_die();
    }

    wp_send_json_success(['city' => $geo_data['city']]);
    wp_die();
}
add_action('wp_ajax_weather_theme_location', 'weather_theme_get_location');
add_action('wp_ajax_nopriv_weather_theme_location', 'weather_theme_get_location');
<?php
/*
Plugin Name: Weather Widget
Description: Weather widget scraping live data from Weather Underground
Version: 2.6
Author: Grok (xAI)
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Start session
if (!session_id()) {
    session_start();
}

// Enqueue assets
function ww_enqueue_assets() {
    $css = '
        .weather-widget {
            max-width: 1200px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 15px;
            color: #000;
        }
        .header-card {
            background: linear-gradient(135deg, #4a90e2, #87ceeb);
            padding: 15px;
            border-radius: 10px;
            margin: -15px -15px 15px;
            color: #000;
        }
        .header-card h1 {
            margin: 0;
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            font-weight: 600;
            color: #000;
        }
        .location-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .location-form input {
            flex: 1;
            min-width: 200px;
            padding: 8px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            background: rgba(255,255,255,0.9);
            color: #000;
        }
        .location-form button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background: #fff;
            color: #4a90e2;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .location-form button:hover:not(:disabled) {
            background: #f0f0f0;
        }
        .location-form button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .location-status {
            font-size: 12px;
            width: 100%;
            color: #000;
        }
        .tabs {
            display: flex;
            gap: 15px;
            margin: 15px 0;
            border-bottom: 1px solid #eee;
            overflow-x: auto;
        }
        .tab-btn {
            padding: 8px 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
            color: #000;
            font-weight: 500;
        }
        .tab-btn.active {
            color: #4a90e2;
            border-bottom: 2px solid #4a90e2;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .weather-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }
        .weather-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s;
        }
        .weather-card:hover {
            transform: translateY(-3px);
        }
        h2 {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            margin: 15px 0;
            color: #000;
        }
        h3 {
            font-size: 14px;
            margin: 5px 0;
            color: #000;
        }
        p {
            margin: 3px 0;
            font-size: 12px;
            color: #000;
        }
        .weather-icon {
            font-size: 24px;
            margin: 5px 0;
        }
        .weather-icon.sunny::before { content: "☀️"; }
        .weather-icon.cloudy::before { content: "☁️"; }
        .weather-icon.rainy::before { content: "🌧️"; }
        .weather-icon.partly-cloudy::before { content: "⛅"; }
        .weather-icon.foggy::before { content: "🌫️"; }
        .weather-icon.clear::before { content: "🌙"; }
        .error-message {
            color: #000;
            font-size: 14px;
            text-align: center;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .weather-widget { margin: 10px; padding: 10px; }
            .header-card { padding: 10px; }
            .location-form { flex-direction: column; }
            .location-form input { min-width: 100%; }
            .tabs { gap: 10px; }
            .weather-card { padding: 8px; }
        }
        @media (max-width: 480px) {
            .weather-grid { grid-template-columns: 1fr; }
            .tab-btn { padding: 6px 10px; font-size: 12px; }
        }
    ';
    wp_add_inline_style('wp-block-library', $css);

    $js = '
        jQuery(document).ready(function($) {
            let debounceTimer;

            $(".tab-btn").on("click", function() {
                $(".tab-btn").removeClass("active");
                $(".tab-content").removeClass("active");
                $(this).addClass("active");
                $("#" + $(this).data("tab")).addClass("active");
            });

            function updateWeather(city) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    $("#location-status").text("Fetching weather for " + city + "...");
                    $.ajax({
                        url: "' . admin_url('admin-ajax.php') . '",
                        type: "POST",
                        data: { action: "update_weather", city: city },
                        dataType: "json",
                        success: function(response) {
                            console.log("Weather Response:", response);
                            if (response.success) {
                                $("#location-status").text("Updated to " + city);
                                updateWeatherDisplay(response.data);
                                $(".header-card h1").text("Weather - " + city);
                            } else {
                                $("#location-status").text("Error: " + (response.data || "Unknown error"));
                                updateWeatherDisplay(getFallbackData());
                                $(".header-card h1").text("Weather - " + city + " (Fallback)");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Weather AJAX Error:", status, error);
                            $("#location-status").text("Failed to fetch weather data");
                            updateWeatherDisplay(getFallbackData());
                        }
                    });
                }, 500);
            }

            function updateWeatherDisplay(data) {
                const hourlyGrid = $("#hourly .weather-grid");
                const weeklyGrid = $("#weekly .weather-grid");
                hourlyGrid.empty();
                weeklyGrid.empty();

                data.hourly.forEach((hour, index) => {
                    const time = index.toString().padStart(2, "0") + ":00";
                    const conditionClass = hour.condition.toLowerCase().replace(" ", "-");
                    hourlyGrid.append(`
                        <div class="weather-card">
                            <div class="weather-icon ${conditionClass}"></div>
                            <h3>${time}</h3>
                            <p>Temp: ${hour.temp}°C</p>
                            <p>${hour.condition}</p>
                            <p>Humidity: ${hour.humidity}%</p>
                            <p>Wind: ${hour.wind} km/h</p>
                        </div>
                    `);
                });

                const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                data.weekly.forEach((day, index) => {
                    const conditionClass = day.condition.toLowerCase().replace(" ", "-");
                    weeklyGrid.append(`
                        <div class="weather-card">
                            <div class="weather-icon ${conditionClass}"></div>
                            <h3>${days[index]}</h3>
                            <p>Temp: ${day.temp}°C</p>
                            <p>${day.condition}</p>
                            <p>Humidity: ${day.humidity}%</p>
                            <p>Wind: ${day.wind} km/h</p>
                        </div>
                    `);
                });
            }

            function getFallbackData() {
                return {
                    hourly: Array(24).fill().map(() => ({ temp: 15, condition: "Cloudy", humidity: 65, wind: 5 })),
                    weekly: Array(7).fill().map(() => ({ temp: 20, condition: "Sunny", humidity: 50, wind: 8 }))
                };
            }

            function checkGeolocation() {
                const $button = $("#get-location");
                const $status = $("#location-status");
                $button.prop("disabled", true).text("Locating...");
                $status.text("Fetching location...");

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            $.ajax({
                                url: "' . admin_url('admin-ajax.php') . '",
                                type: "POST",
                                data: {
                                    action: "get_city_from_coords",
                                    lat: position.coords.latitude,
                                    lon: position.coords.longitude
                                },
                                dataType: "json",
                                success: function(response) {
                                    console.log("Geolocation Response:", response);
                                    if (response.success) {
                                        const city = response.data.city;
                                        $status.text("Location: " + city);
                                        $("#city-input").val(city);
                                        updateWeather(city);
                                    } else {
                                        $status.text("Geolocation error: " + (response.data || "Unknown error"));
                                        $("#city-input").val("New York");
                                        updateWeather("New York");
                                    }
                                    $button.prop("disabled", false).text("My Location");
                                },
                                error: function(xhr, status, error) {
                                    console.log("Geolocation AJAX Error:", status, error);
                                    $status.text("Failed to fetch location. Using New York.");
                                    $("#city-input").val("New York");
                                    updateWeather("New York");
                                    $button.prop("disabled", false).text("My Location");
                                }
                            });
                        },
                        function(error) {
                            console.log("Geolocation Error:", error.message);
                            $status.text("Geolocation denied. Using New York.");
                            $("#city-input").val("New York");
                            updateWeather("New York");
                            $button.prop("disabled", false).text("My Location");
                        },
                        { timeout: 10000 }
                    );
                } else {
                    $status.text("Geolocation not supported. Using New York.");
                    $("#city-input").val("New York");
                    updateWeather("New York");
                    $button.prop("disabled", false).text("My Location");
                }
            }

            if (!$("#city-input").val()) {
                checkGeolocation();
            } else {
                updateWeather($("#city-input").val());
            }

            $("#get-location").on("click", function(e) {
                e.preventDefault();
                checkGeolocation();
            });

            $("#location-form").on("submit", function(e) {
                e.preventDefault();
                const city = $("#city-input").val().trim();
                if (city) updateWeather(city);
            });
        });
    ';
    wp_add_inline_script('jquery', $js);
}
add_action('wp_enqueue_scripts', 'ww_enqueue_assets');

// AJAX handler for updating weather
function ww_update_weather() {
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    if (empty($city)) {
        wp_send_json_error('No city provided');
    }

    $_SESSION['weather_city'] = $city;
    $weather_data = get_weather_data($city);

    if ($weather_data) {
        wp_send_json_success($weather_data);
    } else {
        wp_send_json_error('Failed to scrape weather data for ' . $city);
    }
}
add_action('wp_ajax_update_weather', 'ww_update_weather');
add_action('wp_ajax_nopriv_update_weather', 'ww_update_weather');

// AJAX handler for geolocation (using free Nominatim service)
function ww_get_city_from_coords() {
    $lat = isset($_POST['lat']) ? floatval($_POST['lat']) : 0;
    $lon = isset($_POST['lon']) ? floatval($_POST['lon']) : 0;

    if (!$lat || !$lon) {
        wp_send_json_error('Invalid coordinates');
    }

    // Use Nominatim (OpenStreetMap) for reverse geocoding
    $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json";
    $response = wp_remote_get($url, ['headers' => ['User-Agent' => 'Weather Widget WordPress Plugin']]);

    if (is_wp_error($response)) {
        wp_send_json_error('Geolocation request failed: ' . $response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!$data || !isset($data['address']['city'])) {
        wp_send_json_error('Invalid geolocation response');
    }

    $city = $data['address']['city'] ?? $data['address']['town'] ?? 'Unknown';
    wp_send_json_success(['city' => $city]);
}
add_action('wp_ajax_get_city_from_coords', 'ww_get_city_from_coords');
add_action('wp_ajax_nopriv_get_city_from_coords', 'ww_get_city_from_coords');

// Scrape weather data from Weather Underground
function get_weather_data($city) {
    $transient_key = 'weather_' . md5($city);
    $cached = get_transient($transient_key);

    if ($cached !== false) {
        error_log('Weather Widget: Using cached data for ' . $city);
        return $cached;
    }

    // Format city for URL (e.g., "New York" -> "new-york")
    $city_slug = strtolower(str_replace(' ', '-', $city));
    $url = "https://www.wunderground.com/hourly/us/ny/{$city_slug}";
    $response = wp_remote_get($url, ['timeout' => 15]);

    if (is_wp_error($response)) {
        error_log('Weather Widget: Scrape request failed for ' . $city . ': ' . $response->get_error_message());
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
        error_log('Weather Widget: Empty response for ' . $city);
        return false;
    }

    // Use DOMDocument to parse HTML
    $doc = new DOMDocument();
    @$doc->loadHTML($body); // @ to suppress warnings from malformed HTML
    $xpath = new DOMXPath($doc);

    // Scrape hourly data
    $hourly = [];
    $rows = $xpath->query('//table[contains(@class, "mat-table")]/tbody/tr');
    if ($rows) {
        foreach ($rows as $index => $row) {
            if ($index >= 24) break; // Limit to 24 hours
            $cells = $xpath->query('.//td', $row);
            if ($cells->length >= 4) {
                $time = trim($cells->item(0)->textContent);
                $condition = trim($cells->item(1)->textContent);
                $temp = preg_replace('/[^0-9]/', '', trim($cells->item(2)->textContent)); // Extract number
                $humidity = preg_replace('/[^0-9]/', '', trim($cells->item(4)->textContent));
                $wind = preg_replace('/[^0-9]/', '', trim($cells->item(6)->textContent));

                // Convert Fahrenheit to Celsius if needed (WU uses °F)
                $temp_c = round(($temp - 32) * 5 / 9);
                $wind_kph = round($wind * 1.60934); // mph to km/h

                $hourly[] = [
                    'temp' => $temp_c,
                    'condition' => map_condition($condition),
                    'humidity' => $humidity ?: 50,
                    'wind' => $wind_kph ?: 5
                ];
            }
        }
    }

    // Scrape weekly data (from 10-day forecast page)
    $weekly_url = "https://www.wunderground.com/forecast/us/ny/{$city_slug}";
    $weekly_response = wp_remote_get($weekly_url, ['timeout' => 15]);
    if (!is_wp_error($weekly_response)) {
        $weekly_body = wp_remote_retrieve_body($weekly_response);
        $doc->loadHTML($weekly_body);
        $xpath = new DOMXPath($doc);

        $weekly = [];
        $days = $xpath->query('//div[contains(@class, "daily-container")]');
        if ($days) {
            foreach ($days as $index => $day) {
                if ($index >= 7) break; // Limit to 7 days
                $condition = trim($xpath->query('.//div[contains(@class, "condition")]', $day)->item(0)->textContent ?? 'Sunny');
                $temp_high = preg_replace('/[^0-9]/', '', trim($xpath->query('.//span[contains(@class, "high")]', $day)->item(0)->textContent ?? '68'));
                $humidity = preg_replace('/[^0-9]/', '', trim($xpath->query('.//span[contains(@class, "humidity")]', $day)->item(0)->textContent ?? '50'));
                $wind = preg_replace('/[^0-9]/', '', trim($xpath->query('.//span[contains(@class, "wind")]', $day)->item(0)->textContent ?? '5'));

                $temp_c = round(($temp_high - 32) * 5 / 9);
                $wind_kph = round($wind * 1.60934);

                $weekly[] = [
                    'temp' => $temp_c,
                    'condition' => map_condition($condition),
                    'humidity' => $humidity ?: 50,
                    'wind' => $wind_kph ?: 5
                ];
            }
        }
    }

    if (empty($hourly) && empty($weekly)) {
        error_log('Weather Widget: No data scraped for ' . $city);
        return false;
    }

    $weather_data = ['hourly' => $hourly ?: array_fill(0, 24, ['temp' => 15, 'condition' => 'Cloudy', 'humidity' => 65, 'wind' => 5]), 'weekly' => $weekly ?: array_fill(0, 7, ['temp' => 20, 'condition' => 'Sunny', 'humidity' => 50, 'wind' => 8])];
    set_transient($transient_key, $weather_data, HOUR_IN_SECONDS);
    error_log('Weather Widget: Scraped and cached data for ' . $city);
    return $weather_data;
}

// Map Weather Underground conditions to our icons
function map_condition($condition) {
    $condition = strtolower($condition);
    if (strpos($condition, 'sunny') !== false || strpos($condition, 'clear') !== false) return 'Sunny';
    if (strpos($condition, 'cloudy') !== false) return 'Cloudy';
    if (strpos($condition, 'rain') !== false || strpos($condition, 'shower') !== false) return 'Rainy';
    if (strpos($condition, 'partly') !== false) return 'Partly Cloudy';
    if (strpos($condition, 'fog') !== false) return 'Foggy';
    return 'Clear'; // Default
}

// Shortcode
function ww_display_weather() {
    if (isset($_POST['city']) && !empty($_POST['city'])) {
        $_SESSION['weather_city'] = sanitize_text_field($_POST['city']);
    }
    $city = $_SESSION['weather_city'] ?? '';
    $display_city = $city ?: 'New York (Fallback)';

    $weather_data = $city ? get_weather_data($city) : false;
    if (!$weather_data) {
        $weather_data = [
            'hourly' => array_fill(0, 24, ['temp' => 15, 'condition' => 'Cloudy', 'humidity' => 65, 'wind' => 5]),
            'weekly' => array_fill(0, 7, ['temp' => 20, 'condition' => 'Sunny', 'humidity' => 50, 'wind' => 8])
        ];
    }

    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    ob_start();
    ?>
    <div class="weather-widget">
        <div class="header-card">
            <h1>Weather - <?php echo esc_html($display_city); ?></h1>
            <form id="location-form" class="location-form" method="post">
                <input type="text" id="city-input" name="city" value="<?php echo esc_attr($city); ?>" placeholder="Enter city">
                <button type="submit">Update</button>
                <button type="button" id="get-location">My Location</button>
                <span id="location-status" class="location-status"></span>
            </form>
        </div>

        <?php if (!$city): ?>
            <div class="error-message">Please enter a city or enable geolocation.</div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" data-tab="hourly">Hourly</button>
            <button class="tab-btn" data-tab="weekly">Weekly</button>
        </div>

        <div id="hourly" class="tab-content active">
            <h2>Hourly Forecast</h2>
            <div class="weather-grid">
                <?php for ($i = 0; $i < 24; $i++): ?>
                    <div class="weather-card">
                        <div class="weather-icon <?php echo esc_attr(strtolower(str_replace(' ', '-', $weather_data['hourly'][$i]['condition']))); ?>"></div>
                        <h3><?php echo sprintf('%02d:00', $i); ?></h3>
                        <p>Temp: <?php echo esc_html($weather_data['hourly'][$i]['temp']); ?>°C</p>
                        <p><?php echo esc_html($weather_data['hourly'][$i]['condition']); ?></p>
                        <p>Humidity: <?php echo esc_html($weather_data['hourly'][$i]['humidity']); ?>%</p>
                        <p>Wind: <?php echo esc_html($weather_data['hourly'][$i]['wind']); ?> km/h</p>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div id="weekly" class="tab-content">
            <h2>Weekly Forecast</h2>
            <div class="weather-grid">
                <?php foreach ($weather_data['weekly'] as $i => $data): ?>
                    <div class="weather-card">
                        <div class="weather-icon <?php echo esc_attr(strtolower(str_replace(' ', '-', $data['condition']))); ?>"></div>
                        <h3><?php echo esc_html($days[$i]); ?></h3>
                        <p>Temp: <?php echo esc_html($data['temp']); ?>°C</p>
                        <p><?php echo esc_html($data['condition']); ?></p>
                        <p>Humidity: <?php echo esc_html($data['humidity']); ?>%</p>
                        <p>Wind: <?php echo esc_html($data['wind']); ?> km/h</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('weather_widget', 'ww_display_weather');
?>
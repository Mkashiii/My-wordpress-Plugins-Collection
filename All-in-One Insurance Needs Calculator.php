<?php
/*
Plugin Name: All-in-One Insurance Needs Calculator
Description: An all-in-one insurance needs calculator for Health, Auto, Life, Home, and Business insurance, displayed via [insurance_calculator] shortcode.
Version: 1.3
Author: xAI
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode for the calculator
function mywebinsurance_calculator_shortcode() {
    ob_start();
    ?>
    <div style="max-width: 800px; margin: 0 auto; padding: 24px; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 2px solid #253062; border-radius: 8px; font-family: 'Segoe UI', Arial, sans-serif;">
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 16px; text-align: center; color: #253062;">INSURANCE NEEDS CALCULATOR</h2>
        <form id="insurance-calculator" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Age -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Age</label>
                <input type="number" name="age" min="18" max="100" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Location -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Location (ZIP Code)</label>
                <input type="text" name="location" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Marital Status -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Marital Status</label>
                <select name="marital_status" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;">
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="widowed">Widowed</option>
                </select>
            </div>
            <!-- Number of Dependents -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Number of Dependents</label>
                <input type="number" name="dependents" min="0" max="10" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Income -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Annual Income (USD)</label>
                <input type="number" name="income" min="0" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Assets -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Total Assets (USD)</label>
                <input type="number" name="assets" min="0" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Current Insurance -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Current Insurance (comma-separated, e.g., Health,Auto)</label>
                <input type="text" name="current_insurance" style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Home Ownership -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Do you own a home?</label>
                <select name="home_ownership" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <!-- Car Ownership -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Do you own a car?</label>
                <select name="car_ownership" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <!-- Business Ownership -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Do you own a business?</label>
                <select name="business_ownership" required style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <!-- Health/Lifestyle -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #253062;">Health/Lifestyle Risks (e.g., smoking, chronic illness)</label>
                <input type="text" name="health_factors" style="width: 100%; padding: 8px; border: 1px solid #253062; border-radius: 4px;" />
            </div>
            <!-- Submit Button -->
            <button type="submit" style="width: 100%; background: #1E1E38; color: #fff; padding: 8px; border-radius: 4px; font-weight: 500; cursor: pointer; transition: background 0.3s;">Calculate</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $age = intval($_POST['age']);
            $location = sanitize_text_field($_POST['location']);
            $marital_status = sanitize_text_field($_POST['marital_status']);
            $dependents = intval($_POST['dependents']);
            $income = intval($_POST['income']);
            $assets = intval($_POST['assets']);
            $current_insurance = sanitize_text_field($_POST['current_insurance']);
            $home_ownership = sanitize_text_field($_POST['home_ownership']);
            $car_ownership = sanitize_text_field($_POST['car_ownership']);
            $business_ownership = sanitize_text_field($_POST['business_ownership']);
            $health_factors = sanitize_text_field($_POST['health_factors']);

            // Initialize recommendations
            $recommendations = [];
            $current_insurance_array = array_map('trim', explode(',', $current_insurance));

            // Health Insurance
            if (!in_array('Health', $current_insurance_array) && !empty($health_factors)) {
                $recommendations['Health'] = [
                    'coverage' => '$250,000–$500,000',
                    'premium' => '$100–$300/month'
                ];
            }

            // Auto Insurance
            if ($car_ownership === 'yes' && !in_array('Auto', $current_insurance_array)) {
                $recommendations['Auto'] = [
                    'coverage' => '$50,000–$100,000',
                    'premium' => '$50–$150/month'
                ];
            }

            // Life Insurance
            if ($dependents > 0 && !in_array('Life', $current_insurance_array)) {
                $life_coverage = ($income * 10) + $assets;
                $recommendations['Life'] = [
                    'coverage' => '$' . number_format($life_coverage, 0),
                    'premium' => '$30–$100/month'
                ];
            }

            // Home Insurance
            if ($home_ownership === 'yes' && !in_array('Home', $current_insurance_array)) {
                $recommendations['Home'] = [
                    'coverage' => '$' . number_format($assets * 0.8, 0),
                    'premium' => '$80–$200/month'
                ];
            }

            // Business Insurance
            if ($business_ownership === 'yes' && !in_array('Business', $current_insurance_array)) {
                $recommendations['Business'] = [
                    'coverage' => '$' . number_format($income * 2, 0),
                    'premium' => '$100–$500/month'
                ];
            }

            // Display results
            if (!empty($recommendations)) {
                ?>
                <div style="margin-top: 24px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 16px; color: #253062;">Recommended Insurance</h3>
                    <div style="display: grid; gap: 16px;">
                        <?php foreach ($recommendations as $type => $details) : ?>
                            <div style="padding: 16px; background: #e6e8f0; border-radius: 4px;">
                                <h4 style="font-size: 18px; font-weight: 500; color: #253062;"><?php echo esc_html($type); ?> Insurance</h4>
                                <p style="color: #253062;"><strong>Coverage:</strong> <?php echo esc_html($details['coverage']); ?></p>
                                <p style="color: #253062;"><strong>Premium Range:</strong> <?php echo esc_html($details['premium']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="/contact" style="display: block; margin-top: 16px; text-align: center; background: #1E1E38; color: #fff; padding: 8px; border-radius: 4px; font-weight: 500; text-decoration: none; transition: background 0.3s;">Get Quotes</a>
                </div>
                <?php
            } else {
                ?>
                <div style="margin-top: 24px; padding: 16px; background: #f0f0f5; color: #253062; border-radius: 4px;">
                    No additional insurance recommendations needed based on your inputs.
                </div>
                <?php
            }
        }
        ?>
    </div>

    <!-- Inline JavaScript for form validation -->
    <script>
        document.getElementById('insurance-calculator').addEventListener('submit', function(e) {
            // Validate age
            const age = document.querySelector('input[name="age"]').value;
            if (age < 18 || age > 100) {
                alert('Please enter an age between 18 and 100.');
                e.preventDefault();
                return;
            }

            // Validate ZIP code (basic check for 5 digits)
            const location = document.querySelector('input[name="location"]').value;
            if (!/^\d{5}$/.test(location)) {
                alert('Please enter a valid 5-digit ZIP code.');
                e.preventDefault();
                return;
            }

            // Validate income and assets
            const income = document.querySelector('input[name="income"]').value;
            const assets = document.querySelector('input[name="assets"]').value;
            if (income < 0 || assets < 0) {
                alert('Income and assets cannot be negative.');
                e.preventDefault();
                return;
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('insurance_calculator', 'mywebinsurance_calculator_shortcode');
?>

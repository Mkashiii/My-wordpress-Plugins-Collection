<?php
/*
Plugin Name: Loan Affordability Calculator
Description: A WordPress plugin to calculate loan affordability based on user inputs, with USD currency.
Version: 1.3
Author: Grok
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function loan_lac_enqueue_assets() {
    if (!is_admin()) {
        // Enqueue Tailwind CSS
        wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', [], '2.2.19');
        // Enqueue Google Fonts for Poppins
        wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap', [], null);
        // Enqueue custom styles
        wp_enqueue_style('lac-styles', plugin_dir_url(__FILE__) . 'css/lac-styles.css', [], '1.3');
        // Enqueue custom JS
        wp_enqueue_script('lac-script', plugin_dir_url(__FILE__) . 'js/lac-script.js', ['jquery'], '1.3', true);
    }
}
add_action('wp_enqueue_scripts', 'loan_lac_enqueue_assets');

// Shortcode function
function lac_shortcode() {
    ob_start();
    ?>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md lac-container" style="border: 2px solid #1A5CE0;">
        <h2 class="text-2xl font-bold text-center mb-6 uppercase" style="color: #1A5CE0; font-family: 'Poppins', sans-serif; font-weight: 700;">Loan Affordability Calculator (USD)</h2>
        <form id="lac-form" class="space-y-4">
            <div>
                <label for="monthly-income" class="block text-sm font-medium">Monthly Income (USD, Before Taxes)</label>
                <input type="number" id="monthly-income" name="monthly-income" class="w-full p-2 border rounded" placeholder="e.g., 5000" required min="0" step="1">
            </div>
            <div>
                <label for="monthly-expenses" class="block text-sm font-medium">Monthly Expenses (USD, Rent, Bills, Debts)</label>
                <input type="number" id="monthly-expenses" name="monthly-expenses" class="w-full p-2 border rounded" placeholder="e.g., 2000" required min="0" step="1">
            </div>
            <div>
                <label for="credit-score" class="block text-sm font-medium">Credit Score</label>
                <select id="credit-score" name="credit-score" class="w-full p-2 border rounded" required>
                    <option value="800">Excellent (800-850)</option>
                    <option value="740">Very Good (740-799)</option>
                    <option value="670">Good (670-739)</option>
                    <option value="580">Fair (580-669)</option>
                    <option value="579">Poor (Below 580)</option>
                </select>
            </div>
            <div>
                <label for="loan-term" class="block text-sm font-medium">Loan Term (Months)</label>
                <input type="number" id="loan-term" name="loan-term" class="w-full p-2 border rounded" placeholder="e.g., 60" required min="1" step="1">
            </div>
            <div>
                <label for="interest-rate" class="block text-sm font-medium">Interest Rate (%)</label>
                <input type="number" id="interest-rate" name="interest-rate" class="w-full p-2 border rounded" placeholder="e.g., 5.5" required min="0" step="0.01">
            </div>
            <div>
                <label for="loan-type" class="block text-sm font-medium">Type of Loan</label>
                <select id="loan-type" name="loan-type" class="w-full p-2 border rounded" required>
                    <option value="personal">Personal Loan</option>
                    <option value="home">Home Loan</option>
                    <option value="auto">Auto Loan</option>
                </select>
            </div>
            <button type="submit" class="w-full py-2 px-4 text-white rounded hover:bg-blue-700" style="background-color: #1A5CE0;">Calculate</button>
        </form>
        <div id="lac-results" class="mt-6 hidden">
            <h3 class="text-xl font-semibold" style="color: #1A5CE0;">Your Loan Affordability (USD)</h3>
            <p><strong>Maximum Loan Amount:</strong> USD $<span id="max-loan"></span></p>
            <p><strong>Estimated Monthly EMI:</strong> USD $<span id="max-emi"></span></p>
            <p><strong>Suggested Loan Term:</strong> <span id="suggested-term"></span> months</p>
            <p><strong>Total Interest Payable:</strong> USD $<span id="total-interest"></span></p>
            <p><strong>Debt-to-Income (DTI) Ratio:</strong> <span id="dti"></span>%</p>
            <p id="credit-suggestion" class="mt-4"></p>
            <a href="/loan-comparison" class="mt-4 inline-block py-2 px-4 text-white rounded hover:bg-blue-700" style="background-color: #1A5CE0;">Check Loan Offers</a>
            <p class="mt-2"><a href="/loan-comparison" class="underline" style="color: #1A5CE0;">Compare Lenders</a></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('loan_affordability_calculator', 'lac_shortcode');

// Create CSS file
function loan_lac_create_css_file() {
    $css = '
.lac-container {
    border: 2px solid #1A5CE0;
}
.lac-container input, .lac-container select {
    border-color: #1A5CE0;
}
.lac-container input:focus, .lac-container select:focus {
    outline: none;
    border-color: #1A5CE0;
    box-shadow: 0 0 0 3px rgba(26, 92, 224, 0.2);
}
.lac-container button, .lac-container a {
    transition: background-color 0.3s ease;
}
';
    $css_dir = plugin_dir_path(__FILE__) . 'css/';
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    file_put_contents($css_dir . 'lac-styles.css', $css);
}
register_activation_hook(__FILE__, 'loan_lac_create_css_file');

// Create JS file
function loan_lac_create_js_file() {
    $js = '
jQuery(document).ready(function($) {
    $("#lac-form").on("submit", function(e) {
        e.preventDefault();
        
        // Get inputs
        const income = parseFloat($("#monthly-income").val());
        const expenses = parseFloat($("#monthly-expenses").val());
        const creditScore = parseInt($("#credit-score").val());
        let term = parseInt($("#loan-term").val());
        const interestRate = parseFloat($("#interest-rate").val()) / 100;
        const loanType = $("#loan-type").val();
        
        // Validate inputs
        if (isNaN(income) || isNaN(expenses) || isNaN(term) || isNaN(interestRate)) {
            alert("Please fill in all fields with valid numbers.");
            return;
        }
        
        // Calculate DTI
        const dti = (expenses / income) * 100;
        
        // Adjust interest rate based on credit score
        let adjustedRate = interestRate;
        if (creditScore < 580) adjustedRate += 0.02;
        else if (creditScore < 670) adjustedRate += 0.015;
        else if (creditScore < 740) adjustedRate += 0.01;
        
        // Monthly interest rate
        const monthlyRate = adjustedRate / 12;
        
        // Maximum affordable EMI (30% of disposable income)
        const disposableIncome = income - expenses;
        const maxEMI = disposableIncome * 0.3;
        
        // Calculate maximum loan amount
        const maxLoan = maxEMI * (1 - Math.pow(1 + monthlyRate, -term)) / monthlyRate;
        
        // Calculate EMI for max loan
        const emi = maxLoan * monthlyRate * Math.pow(1 + monthlyRate, term) / (Math.pow(1 + monthlyRate, term) - 1);
        
        // Suggest term to keep EMI manageable
        let suggestedTerm = term;
        if (emi > maxEMI) {
            suggestedTerm = Math.ceil(-Math.log(1 - (maxEMI / (maxLoan * monthlyRate))) / Math.log(1 + monthlyRate));
        }
        
        // Total interest
        const totalInterest = (emi * term) - maxLoan;
        
        // Credit score suggestion
        let creditSuggestion = "";
        if (creditScore < 670) {
            creditSuggestion = "Consider improving your credit score to secure better interest rates.";
        } else if (creditScore < 740) {
            creditSuggestion = "A slightly higher credit score could get you more favorable loan terms.";
        } else {
            creditSuggestion = "Your excellent credit score qualifies you for the best rates!";
        }
        
        // Display results
        $("#max-loan").text(maxLoan.toFixed(2));
        $("#max-emi").text(emi.toFixed(2));
        $("#suggested-term").text(suggestedTerm);
        $("#total-interest").text(totalInterest.toFixed(2));
        $("#dti").text(dti.toFixed(2));
        $("#credit-suggestion").text(creditSuggestion);
        $("#lac-results").removeClass("hidden");
    });
});
';
    $js_dir = plugin_dir_path(__FILE__) . 'js/';
    if (!file_exists($js_dir)) {
        mkdir($js_dir, 0755, true);
    }
    file_put_contents($js_dir . 'lac-script.js', $js);
}
register_activation_hook(__FILE__, 'loan_lac_create_js_file');
?>
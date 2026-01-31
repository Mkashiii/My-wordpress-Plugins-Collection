<?php
/*
Plugin Name: Crypto Investment Profit Calculator
Description: A WordPress plugin to calculate profit/loss from cryptocurrency investments.
Version: 1.2
Author: Grok
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function crypto_cpc_enqueue_assets() {
    if (!is_admin()) {
        // Enqueue Tailwind CSS
        wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', [], '2.2.19');
        // Enqueue Google Fonts for Poppins
        wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap', [], null);
        // Enqueue custom styles
        wp_enqueue_style('cpc-styles', plugin_dir_url(__FILE__) . 'css/cpc-styles.css', [], '1.2');
        // Enqueue custom JS
        wp_enqueue_script('cpc-script', plugin_dir_url(__FILE__) . 'js/cpc-script.js', ['jquery'], '1.2', true);
        // Localize script for AJAX
        wp_localize_script('cpc-script', 'cpcAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    }
}
add_action('wp_enqueue_scripts', 'crypto_cpc_enqueue_assets');

// Shortcode function
function crypto_cpc_shortcode() {
    ob_start();
    ?>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md cpc-container" style="border: 2px solid #E05414;">
        <h2 class="text-2xl font-bold text-center mb-6 uppercase" style="color: #E05414; font-family: 'Poppins', sans-serif; font-weight: 700;">Crypto Investment Profit Calculator (USD)</h2>
        <form id="cpc-form" class="space-y-4">
            <div>
                <label for="crypto" class="block text-sm font-medium" style="color: #E05414;">Cryptocurrency</label>
                <select id="crypto" name="crypto" class="w-full p-2 border rounded" required>
                    <option value="bitcoin">Bitcoin (BTC)</option>
                    <option value="ethereum">Ethereum (ETH)</option>
                    <option value="binancecoin">Binance Coin (BNB)</option>
                    <option value="ripple">Ripple (XRP)</option>
                    <option value="cardano">Cardano (ADA)</option>
                </select>
            </div>
            <div>
                <label for="quantity" class="block text-sm font-medium" style="color: #E05414;">Quantity Purchased</label>
                <input type="number" id="quantity" name="quantity" class="w-full p-2 border rounded" placeholder="e.g., 0.5" required min="0" step="0.0001">
            </div>
            <div>
                <label for="buy-price" class="block text-sm font-medium" style="color: #E05414;">Buy Price (USD)</label>
                <input type="number" id="buy-price" name="buy-price" class="w-full p-2 border rounded" placeholder="e.g., 50000" required min="0" step="0.01">
                <button type="button" id="fetch-price" class="mt-2 py-1 px-3 text-white rounded" style="background-color: #E05414;">Use Current Price</button>
            </div>
            <div>
                <label for="sell-price" class="block text-sm font-medium" style="color: #E05414;">Sell Price (USD)</label>
                <input type="number" id="sell-price" name="sell-price" class="w-full p-2 border rounded" placeholder="e.g., 60000" required min="0" step="0.01">
            </div>
            <div>
                <label for="purchase-date" class="block text-sm font-medium" style="color: #E05414;">Purchase Date</label>
                <input type="date" id="purchase-date" name="purchase-date" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label for="sale-date" class="block text-sm font-medium" style="color: #E05414;">Sale Date</label>
                <input type="date" id="sale-date" name="sale-date" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label for="fee-type" class="block text-sm font-medium" style="color: #E05414;">Trading Fee Type</label>
                <select id="fee-type" name="fee-type" class="w-full p-2 border rounded">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed (USD)</option>
                </select>
            </div>
            <div>
                <label for="fee-amount" class="block text-sm font-medium" style="color: #E05414;">Fee Amount</label>
                <input type="number" id="fee-amount" name="fee-amount" class="w-full p-2 border rounded" placeholder="e.g., 0.1 or 10" required min="0" step="0.01">
            </div>
            <div>
                <label for="tax-rate" class="block text-sm font-medium" style="color: #E05414;">Tax Rate (% - Optional)</label>
                <input type="number" id="tax-rate" name="tax-rate" class="w-full p-2 border rounded" placeholder="e.g., 15" min="0" step="0.01">
            </div>
            <button type="submit" class="w-full py-2 px-4 text-white rounded hover:bg-orange-600" style="background-color: #E05414;">Calculate</button>
        </form>
        <div id="cpc-results" class="mt-6 hidden">
            <h3 class="text-xl font-semibold" style="color: #E05414;">Your Investment Results</h3>
            <p><strong>Net Profit/Loss:</strong> USD $<span id="profit-loss"></span></p>
            <p><strong>ROI Percentage:</strong> <span id="roi"></span>%</p>
            <p><strong>Total Fees Paid:</strong> USD $<span id="total-fees"></span></p>
            <p><strong>Effective Annualized Return:</strong> <span id="annualized-return"></span>%</p>
            <p><strong>Tax Owed:</strong> USD $<span id="tax-owed"></span></p>
            <div class="mt-4">
                <h4 class="text-lg font-medium" style="color: #E05414;">What If Scenario</h4>
                <label for="what-if-price" class="block text-sm font-medium" style="color: #E05414;">If Price Hits (USD):</label>
                <input type="number" id="what-if-price" class="w-full p-2 border rounded mb-2" placeholder="e.g., 100000" min="0" step="0.01">
                <button id="what-if-btn" class="py-1 px-3 text-white rounded" style="background-color: #E05414;">Calculate Scenario</button>
                <p id="what-if-result" class="mt-2"></p>
            </div>
            <button id="export-btn" class="mt-4 py-2 px-4 text-white rounded hover:bg-orange-600" style="background-color: #E05414;">Export Results</button>
            <p class="mt-2"><a href="/portfolio-tracker" class="underline" style="color: #E05414;">Track Your Portfolio</a></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('crypto_profit_calculator', 'crypto_cpc_shortcode');

// Create CSS file
function crypto_cpc_create_css_file() {
    $css = '
.cpc-container {
    border: 2px solid #E05414;
}
.cpc-container input, .cpc-container select {
    border-color: #E05414;
}
.cpc-container input:focus, .cpc-container select:focus {
    outline: none;
    border-color: #E05414;
    box-shadow: 0 0 0 3px rgba(224, 84, 20, 0.2);
}
.cpc-container button, .cpc-container a {
    transition: background-color 0.3s ease;
}
';
    $css_dir = plugin_dir_path(__FILE__) . 'css/';
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    file_put_contents($css_dir . 'cpc-styles.css', $css);
}
register_activation_hook(__FILE__, 'crypto_cpc_create_css_file');

// Create JS file
function crypto_cpc_create_js_file() {
    $js = '
jQuery(document).ready(function($) {
    // Fetch live price
    $("#fetch-price").on("click", function() {
        const crypto = $("#crypto").val();
        $.ajax({
            url: "https://api.coingecko.com/api/v3/simple/price?ids=" + crypto + "&vs_currencies=usd",
            method: "GET",
            success: function(data) {
                const price = data[crypto].usd;
                $("#buy-price").val(price);
                $("#sell-price").val(price);
            },
            error: function() {
                alert("Failed to fetch live price. Please enter manually.");
            }
        });
    });

    // Form submission
    $("#cpc-form").on("submit", function(e) {
        e.preventDefault();
        
        // Get inputs
        const quantity = parseFloat($("#quantity").val());
        const buyPrice = parseFloat($("#buy-price").val());
        const sellPrice = parseFloat($("#sell-price").val());
        const purchaseDate = new Date($("#purchase-date").val());
        const saleDate = new Date($("#sale-date").val());
        const feeType = $("#fee-type").val();
        const feeAmount = parseFloat($("#fee-amount").val());
        const taxRate = parseFloat($("#tax-rate").val()) || 0;
        
        // Validate inputs
        if (isNaN(quantity) || isNaN(buyPrice) || isNaN(sellPrice) || isNaN(feeAmount) || !purchaseDate || !saleDate) {
            alert("Please fill in all required fields with valid values.");
            return;
        }
        if (saleDate < purchaseDate) {
            alert("Sale date must be after purchase date.");
            return;
        }
        
        // Calculate fees
        const buyFee = feeType === "percentage" ? (buyPrice * quantity * feeAmount / 100) : feeAmount;
        const sellFee = feeType === "percentage" ? (sellPrice * quantity * feeAmount / 100) : feeAmount;
        const totalFees = buyFee + sellFee;
        
        // Calculate profit/loss
        const buyTotal = buyPrice * quantity + buyFee;
        const sellTotal = sellPrice * quantity - sellFee;
        const profitLoss = sellTotal - buyTotal;
        
        // Calculate tax
        const taxOwed = profitLoss > 0 ? profitLoss * (taxRate / 100) : 0;
        const netProfitLoss = profitLoss - taxOwed;
        
        // Calculate ROI
        const roi = (profitLoss / buyTotal) * 100;
        
        // Calculate annualized return
        const yearsHeld = (saleDate - purchaseDate) / (1000 * 60 * 60 * 24 * 365);
        const annualizedReturn = yearsHeld > 0 ? (Math.pow(sellTotal / buyTotal, 1 / yearsHeld) - 1) * 100 : roi;
        
        // Display results
        $("#profit-loss").text(netProfitLoss.toFixed(2));
        $("#roi").text(roi.toFixed(2));
        $("#total-fees").text(totalFees.toFixed(2));
        $("#annualized-return").text(annualizedReturn.toFixed(2));
        $("#tax-owed").text(taxOwed.toFixed(2));
        $("#cpc-results").removeClass("hidden");
        
        // Store results for export
        window.cpcResults = {
            netProfitLoss,
            roi,
            totalFees,
            annualizedReturn,
            taxOwed,
            crypto: $("#crypto option:selected").text(),
            quantity,
            buyPrice,
            sellPrice
        };
    });

    // What If scenario
    $("#what-if-btn").on("click", function() {
        const whatIfPrice = parseFloat($("#what-if-price").val());
        if (isNaN(whatIfPrice)) {
            alert("Please enter a valid price for the scenario.");
            return;
        }
        const quantity = parseFloat($("#quantity").val());
        const buyPrice = parseFloat($("#buy-price").val());
        const feeType = $("#fee-type").val();
        const feeAmount = parseFloat($("#fee-amount").val());
        const taxRate = parseFloat($("#tax-rate").val()) || 0;
        
        const buyFee = feeType === "percentage" ? (buyPrice * quantity * feeAmount / 100) : feeAmount;
        const sellFee = feeType === "percentage" ? (whatIfPrice * quantity * feeAmount / 100) : feeAmount;
        const buyTotal = buyPrice * quantity + buyFee;
        const sellTotal = whatIfPrice * quantity - sellFee;
        const profitLoss = sellTotal - buyTotal;
        const taxOwed = profitLoss > 0 ? profitLoss * (taxRate / 100) : 0;
        const netProfitLoss = profitLoss - taxOwed;
        
        $("#what-if-result").text(`If ${$("#crypto option:selected").text()} hits USD $${whatIfPrice.toFixed(2)}, your net profit/loss would be USD $${netProfitLoss.toFixed(2)}.`);
    });

    // Export results
    $("#export-btn").on("click", function() {
        if (!window.cpcResults) {
            alert("Please calculate results first.");
            return;
        }
        const results = window.cpcResults;
        const text = `Crypto Investment Results\n\n` +
                     `Cryptocurrency: ${results.crypto}\n` +
                     `Quantity: ${results.quantity}\n` +
                     `Buy Price: USD $${results.buyPrice.toFixed(2)}\n` +
                     `Sell Price: USD $${results.sellPrice.toFixed(2)}\n` +
                     `Net Profit/Loss: USD $${results.netProfitLoss.toFixed(2)}\n` +
                     `ROI: ${results.roi.toFixed(2)}%\n` +
                     `Total Fees: USD $${results.totalFees.toFixed(2)}\n` +
                     `Annualized Return: ${results.annualizedReturn.toFixed(2)}%\n` +
                     `Tax Owed: USD $${results.taxOwed.toFixed(2)}`;
        const blob = new Blob([text], { type: "text/plain" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "crypto_investment_results.txt";
        a.click();
        URL.revokeObjectURL(url);
    });
});
';
    $js_dir = plugin_dir_path(__FILE__) . 'js/';
    if (!file_exists($js_dir)) {
        mkdir($js_dir, 0755, true);
    }
    file_put_contents($js_dir . 'cpc-script.js', $js);
}
register_activation_hook(__FILE__, 'crypto_cpc_create_js_file');
?>
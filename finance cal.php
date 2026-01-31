<?php
/*
Plugin Name: Finance Calculator
Description: Comprehensive finance calculator for various loan types with a green and blue theme.
Version: 2.3
Author: Your Name
*/

// Shortcode to display the calculator
function finance_calculator_shortcode() {
    wp_enqueue_style('finance-calculator-style', plugin_dir_url(__FILE__) . 'finance-calculator.css', array(), '2.3');
    wp_add_inline_style('finance-calculator-style', finance_calculator_css());
    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery', finance_calculator_js(), 'after');

    ob_start();

    $results = array();
    $show_table = false;
    $active_tab = isset($_POST['active_tab']) ? sanitize_text_field($_POST['active_tab']) : 'amortized';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['calculator_type'])) {
        try {
            switch ($_POST['calculator_type']) {
                case 'amortized': $results = calculate_amortized_loan(); $active_tab = 'amortized'; break;
                case 'deferred': $results = calculate_deferred_loan(); $active_tab = 'deferred'; break;
                case 'bond': $results = calculate_bond(); $active_tab = 'bond'; break;
                case 'mortgage': $results = calculate_mortgage_loan(); $active_tab = 'mortgage'; break;
                case 'bad_credit': $results = calculate_bad_credit_loan(); $active_tab = 'bad_credit'; break;
                case 'auto': $results = calculate_auto_loan(); $active_tab = 'auto'; break;
                case 'debt_consolidation': $results = calculate_debt_consolidation_loan(); $active_tab = 'debt_consolidation'; break;
                case 'personal': $results = calculate_personal_loan(); $active_tab = 'personal'; break;
                case 'business': $results = calculate_business_loan(); $active_tab = 'business'; break;
                case 'student': $results = calculate_student_loan(); $active_tab = 'student'; break;
                case 'view_table':
                    if (isset($_POST['table_data'])) {
                        $results = unserialize(base64_decode(sanitize_text_field($_POST['table_data'])));
                        $active_tab = $results['type'];
                        $show_table = true;
                    }
                    break;
                default: $results['error'] = 'Invalid calculator type.'; break;
            }
        } catch (Exception $e) {
            $results['error'] = 'An error occurred: ' . esc_html($e->getMessage());
        }
    }

    ?>
    <div class="finance-calculator">
        <h2>Finance Calculator</h2>
        <div class="tabs">
            <button class="tab-btn <?php echo $active_tab === 'amortized' ? 'active' : ''; ?>" data-tab="amortized">Amortized Loan</button>
            <button class="tab-btn <?php echo $active_tab === 'deferred' ? 'active' : ''; ?>" data-tab="deferred">Deferred Payment</button>
            <button class="tab-btn <?php echo $active_tab === 'bond' ? 'active' : ''; ?>" data-tab="bond">Bond</button>
            <button class="tab-btn <?php echo $active_tab === 'mortgage' ? 'active' : ''; ?>" data-tab="mortgage">Mortgage</button>
            <button class="tab-btn <?php echo $active_tab === 'bad_credit' ? 'active' : ''; ?>" data-tab="bad_credit">Bad Credit</button>
            <button class="tab-btn <?php echo $active_tab === 'auto' ? 'active' : ''; ?>" data-tab="auto">Auto Loan</button>
            <button class="tab-btn <?php echo $active_tab === 'debt_consolidation' ? 'active' : ''; ?>" data-tab="debt_consolidation">Debt Consolidation</button>
            <button class="tab-btn <?php echo $active_tab === 'personal' ? 'active' : ''; ?>" data-tab="personal">Personal Loan</button>
            <button class="tab-btn <?php echo $active_tab === 'business' ? 'active' : ''; ?>" data-tab="business">Business Loan</button>
            <button class="tab-btn <?php echo $active_tab === 'student' ? 'active' : ''; ?>" data-tab="student">Student Loan</button>
        </div>

        <!-- Amortized Loan -->
        <div id="amortized" class="tab-content <?php echo $active_tab === 'amortized' ? 'active' : ''; ?>">
            <h3>Amortized Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="amortized">
                <input type="hidden" name="active_tab" value="amortized">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="100000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="10" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="6" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Compound</label></td>
                        <td>
                            <select name="compound">
                                <option value="monthly">Monthly (APR)</option>
                                <option value="annually">Annually (APY)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Pay Back</label></td>
                        <td>
                            <select name="payback">
                                <option value="monthly">Every Month</option>
                                <option value="annually">Every Year</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'amortized') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'amortized') display_amortization_table($results); ?>
        </div>

        <!-- Deferred Payment Loan -->
        <div id="deferred" class="tab-content <?php echo $active_tab === 'deferred' ? 'active' : ''; ?>">
            <h3>Deferred Payment Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="deferred">
                <input type="hidden" name="active_tab" value="deferred">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="100000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="10" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="6" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Compound</label></td>
                        <td>
                            <select name="compound">
                                <option value="annually">Annually (APY)</option>
                                <option value="monthly">Monthly (APR)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'deferred') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'deferred') display_deferred_table($results); ?>
        </div>

        <!-- Bond -->
        <div id="bond" class="tab-content <?php echo $active_tab === 'bond' ? 'active' : ''; ?>">
            <h3>Bond Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="bond">
                <input type="hidden" name="active_tab" value="bond">
                <table class="form-table">
                    <tr>
                        <td><label>Predetermined Due Amount</label></td>
                        <td><input type="number" name="due_amount" value="100000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="10" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="6" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Compound</label></td>
                        <td>
                            <select name="compound">
                                <option value="annually">Annually (APY)</option>
                                <option value="monthly">Monthly (APR)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'bond') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'bond') display_bond_table($results); ?>
        </div>

        <!-- Mortgage Loan -->
        <div id="mortgage" class="tab-content <?php echo $active_tab === 'mortgage' ? 'active' : ''; ?>">
            <h3>Mortgage Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="mortgage">
                <input type="hidden" name="active_tab" value="mortgage">
                <table class="form-table">
                    <tr>
                        <td><label>Home Price</label></td>
                        <td><input type="number" name="home_price" value="200000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Down Payment</label></td>
                        <td><input type="number" name="down_payment" value="40000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term (years)</label></td>
                        <td><input type="number" name="years" value="30" min="1" required></td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="4.5" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'mortgage') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'mortgage') display_amortization_table($results); ?>
        </div>

        <!-- Bad Credit Loan -->
        <div id="bad_credit" class="tab-content <?php echo $active_tab === 'bad_credit' ? 'active' : ''; ?>">
            <h3>Bad Credit Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="bad_credit">
                <input type="hidden" name="active_tab" value="bad_credit">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="10000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="5" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="15" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'bad_credit') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'bad_credit') display_amortization_table($results); ?>
        </div>

        <!-- Auto Loan -->
        <div id="auto" class="tab-content <?php echo $active_tab === 'auto' ? 'active' : ''; ?>">
            <h3>Auto Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="auto">
                <input type="hidden" name="active_tab" value="auto">
                <table class="form-table">
                    <tr>
                        <td><label>Car Price</label></td>
                        <td><input type="number" name="car_price" value="25000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Down Payment</label></td>
                        <td><input type="number" name="down_payment" value="5000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term (years)</label></td>
                        <td><input type="number" name="years" value="5" min="1" required></td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="6" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'auto') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'auto') display_amortization_table($results); ?>
        </div>

        <!-- Debt Consolidation -->
        <div id="debt_consolidation" class="tab-content <?php echo $active_tab === 'debt_consolidation' ? 'active' : ''; ?>">
            <h3>Debt Consolidation Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="debt_consolidation">
                <input type="hidden" name="active_tab" value="debt_consolidation">
                <table class="form-table">
                    <tr>
                        <td><label>Total Debt Amount</label></td>
                        <td><input type="number" name="loan_amount" value="20000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="5" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="8" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'debt_consolidation') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'debt_consolidation') display_amortization_table($results); ?>
        </div>

        <!-- Personal Loan -->
        <div id="personal" class="tab-content <?php echo $active_tab === 'personal' ? 'active' : ''; ?>">
            <h3>Personal Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="personal">
                <input type="hidden" name="active_tab" value="personal">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="10000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="3" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="7" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'personal') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'personal') display_amortization_table($results); ?>
        </div>

        <!-- Business Loan -->
        <div id="business" class="tab-content <?php echo $active_tab === 'business' ? 'active' : ''; ?>">
            <h3>Business Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="business">
                <input type="hidden" name="active_tab" value="business">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="50000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="7" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="6.5" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'business') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'business') display_amortization_table($results); ?>
        </div>

        <!-- Student Loan -->
        <div id="student" class="tab-content <?php echo $active_tab === 'student' ? 'active' : ''; ?>">
            <h3>Student Loan Calculator</h3>
            <form method="post" class="calc-form">
                <input type="hidden" name="calculator_type" value="student">
                <input type="hidden" name="active_tab" value="student">
                <table class="form-table">
                    <tr>
                        <td><label>Loan Amount</label></td>
                        <td><input type="number" name="loan_amount" value="30000" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td><label>Credit Score</label></td>
                        <td>
                            <select name="credit_score">
                                <option value="excellent">Excellent (720+)</option>
                                <option value="good">Good (660-719)</option>
                                <option value="fair">Fair (600-659)</option>
                                <option value="poor">Poor (<600)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Loan Term</label></td>
                        <td class="term-input">
                            <input type="number" name="years" value="10" min="0"> years
                            <input type="number" name="months" value="0" min="0"> months
                        </td>
                    </tr>
                    <tr>
                        <td><label>Interest Rate (%)</label></td>
                        <td><input type="number" name="interest_rate" value="5" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Calculate"></td>
                    </tr>
                </table>
            </form>
            <?php if (!empty($results) && $_POST['calculator_type'] === 'student') display_results($results, $show_table); ?>
            <?php if ($show_table && isset($results['type']) && $results['type'] === 'student') display_amortization_table($results); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Inline JavaScript
function finance_calculator_js() {
    return "
    jQuery(document).ready(function($) {
        var activeTab = '" . (isset($_POST['active_tab']) ? esc_js(sanitize_text_field($_POST['active_tab'])) : 'amortized') . "';
        if (activeTab) {
            $('.tab-content').removeClass('active');
            $('.tab-btn').removeClass('active');
            $('#' + activeTab).addClass('active');
            $('.tab-btn[data-tab=\"' + activeTab + '\"]').addClass('active');
        }

        $('.tab-btn').on('click', function(e) {
            e.preventDefault();
            var tabName = $(this).data('tab');
            $('.tab-content').removeClass('active');
            $('.tab-btn').removeClass('active');
            $('#' + tabName).addClass('active');
            $(this).addClass('active');
            $('.tab-content:not(.active) .results, .tab-content:not(.active) .finance-table').remove();
            $('input[name=\"active_tab\"]').val(tabName);
        });

        $('.calc-form').on('submit', function() {
            var tabName = $(this).closest('.tab-content').attr('id');
            $(this).find('input[name=\"active_tab\"]').val(tabName);
        });
    });
    ";
}

// Updated CSS with Green and Blue Theme
function finance_calculator_css() {
    return '
        .finance-calculator {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #ffffff;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #2c3e50;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .finance-calculator h2 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 25px;
            color: #27ae60;
            font-weight: 600;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .tab-btn {
            padding: 10px 20px;
            background: #ecf0f1;
            border: 2px solid #3498db;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            color: #3498db;
            transition: all 0.3s ease;
        }

        .tab-btn:hover, .tab-btn.active {
            background: #3498db;
            color: #ffffff;
            border-color: #2980b9;
        }

        .tab-content {
            display: none;
            padding: 20px;
            background: #f9fbfc;
            border-radius: 8px;
        }

        .tab-content.active {
            display: block;
        }

        .tab-content h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #27ae60;
            font-weight: 500;
        }

        .calc-form {
            margin-bottom: 20px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-table td {
            padding: 12px;
            vertical-align: middle;
        }

        .form-table label {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-table input, .form-table select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            background: #ffffff;
            transition: border-color 0.3s ease;
        }

        .form-table input:focus, .form-table select:focus {
            border-color: #3498db;
            outline: none;
        }

        .term-input {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .term-input input {
            width: 80px;
        }

        .calc-form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .calc-form input[type="submit"]:hover {
            background: #219653;
        }

        .results {
            margin-top: 20px;
            padding: 20px;
            background: #ecf0f1;
            border-radius: 8px;
            border: 1px solid #dfe6e9;
        }

        .results h4 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #3498db;
            font-weight: 500;
        }

        .results p {
            font-size: 15px;
            margin: 8px 0;
            color: #2c3e50;
        }

        .chart {
            display: flex;
            height: 25px;
            margin: 15px 0;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #bdc3c7;
        }

        .principal {
            background: #27ae60;
            color: #ffffff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .interest {
            background: #3498db;
            color: #ffffff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .results button {
            padding: 10px 20px;
            background: #27ae60;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .results button:hover {
            background: #219653;
        }

        .error {
            color: #e74c3c;
            font-size: 15px;
            text-align: center;
            margin: 10px 0;
        }

        .finance-table {
            margin-top: 25px;
            overflow-x: auto;
        }

        .finance-table h4 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #3498db;
            font-weight: 500;
        }

        .finance-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .finance-table th, .finance-table td {
            padding: 12px;
            text-align: right;
            border: 1px solid #dfe6e9;
        }

        .finance-table th {
            background: #3498db;
            color: #ffffff;
            font-weight: 600;
        }

        .finance-table td {
            color: #2c3e50;
        }

        .finance-table tr:nth-child(even) {
            background: #f9fbfc;
        }

        @media (max-width: 768px) {
            .finance-calculator {
                margin: 15px;
                padding: 15px;
            }
            .tabs {
                gap: 8px;
            }
            .tab-btn {
                padding: 8px 15px;
                font-size: 13px;
            }
            .form-table td {
                display: block;
                width: 100%;
                padding: 8px 0;
            }
            .term-input {
                flex-direction: column;
                align-items: flex-start;
            }
            .term-input input {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .finance-calculator h2 {
                font-size: 24px;
            }
            .tab-content h3 {
                font-size: 18px;
            }
            .results h4, .finance-table h4 {
                font-size: 16px;
            }
            .results p, .finance-table table {
                font-size: 13px;
            }
            .calc-form input[type="submit"], .results button {
                font-size: 14px;
                padding: 10px;
            }
        }
    ';
}

// Calculation Functions (unchanged from original)
function calculate_amortized_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $compound = sanitize_text_field($_POST['compound'] ?? 'monthly');
    $payback = sanitize_text_field($_POST['payback'] ?? 'monthly');
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input: Loan amount and term must be positive.");
    }

    $total_months = ($years * 12) + $months;
    $rate_per_period = $compound === 'monthly' ? $interest_rate / 12 : $interest_rate;
    $periods = $payback === 'monthly' ? $total_months : $years;

    if ($periods <= 0) throw new Exception("Loan term must be at least 1 period.");

    if ($rate_per_period == 0) {
        $payment = $loan_amount / $periods;
    } else {
        $payment = $loan_amount * ($rate_per_period * pow(1 + $rate_per_period, $periods)) / (pow(1 + $rate_per_period, $periods) - 1);
    }

    if (!is_finite($payment)) throw new Exception("Calculation error: Check inputs.");

    $total_payments = $payment * $periods;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $periods,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'amortized',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $rate_per_period,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => $payback
    );
}

function calculate_deferred_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $compound = sanitize_text_field($_POST['compound'] ?? 'annually');
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_years = $years + ($months / 12);
    $amount_due = $loan_amount * pow(1 + ($compound === 'annually' ? $interest_rate : $interest_rate / 12), $compound === 'annually' ? $total_years : $total_years * 12);
    $total_interest = $amount_due - $loan_amount;

    return array(
        'amount_due' => $amount_due,
        'total_interest' => $total_interest,
        'principal_percent' => ($loan_amount / $amount_due) * 100,
        'interest_percent' => ($total_interest / $amount_due) * 100,
        'type' => 'deferred',
        'loan_amount' => $loan_amount,
        'total_years' => $total_years,
        'interest_rate' => $interest_rate,
        'compound' => $compound,
        'credit_score' => $credit_score
    );
}

function calculate_bond() {
    $due_amount = floatval(sanitize_text_field($_POST['due_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $compound = sanitize_text_field($_POST['compound'] ?? 'annually');
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($due_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_years = $years + ($months / 12);
    $principal = $due_amount / pow(1 + ($compound === 'annually' ? $interest_rate : $interest_rate / 12), $compound === 'annually' ? $total_years : $total_years * 12);
    $total_interest = $due_amount - $principal;

    return array(
        'principal' => $principal,
        'total_interest' => $total_interest,
        'principal_percent' => ($principal / $due_amount) * 100,
        'interest_percent' => ($total_interest / $due_amount) * 100,
        'type' => 'bond',
        'due_amount' => $due_amount,
        'total_years' => $total_years,
        'interest_rate' => $interest_rate,
        'compound' => $compound,
        'credit_score' => $credit_score
    );
}

function calculate_mortgage_loan() {
    $home_price = floatval(sanitize_text_field($_POST['home_price'] ?? 0));
    $down_payment = floatval(sanitize_text_field($_POST['down_payment'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;
    $loan_amount = $home_price - $down_payment;

    if ($loan_amount <= 0 || $interest_rate < 0 || $years <= 0) {
        throw new Exception("Invalid input values.");
    }

    $total_months = $years * 12;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'mortgage',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_bad_credit_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'poor');

    $credit_adjustment = ['fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_months = ($years * 12) + $months;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'bad_credit',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_auto_loan() {
    $car_price = floatval(sanitize_text_field($_POST['car_price'] ?? 0));
    $down_payment = floatval(sanitize_text_field($_POST['down_payment'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;
    $loan_amount = $car_price - $down_payment;

    if ($loan_amount <= 0 || $interest_rate < 0 || $years <= 0) {
        throw new Exception("Invalid input values.");
    }

    $total_months = $years * 12;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'auto',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_debt_consolidation_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_months = ($years * 12) + $months;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'debt_consolidation',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_personal_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_months = ($years * 12) + $months;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'personal',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_business_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_months = ($years * 12) + $months;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'business',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

function calculate_student_loan() {
    $loan_amount = floatval(sanitize_text_field($_POST['loan_amount'] ?? 0));
    $years = intval(sanitize_text_field($_POST['years'] ?? 0));
    $months = intval(sanitize_text_field($_POST['months'] ?? 0));
    $interest_rate = floatval(sanitize_text_field($_POST['interest_rate'] ?? 0)) / 100;
    $credit_score = sanitize_text_field($_POST['credit_score'] ?? 'good');

    $credit_adjustment = ['excellent' => -0.005, 'good' => 0, 'fair' => 0.015, 'poor' => 0.03];
    $interest_rate += $credit_adjustment[$credit_score] ?? 0;

    if ($loan_amount <= 0 || $interest_rate < 0 || ($years <= 0 && $months <= 0)) {
        throw new Exception("Invalid input values.");
    }

    $total_months = ($years * 12) + $months;
    $monthly_rate = $interest_rate / 12;
    $payment = $loan_amount * ($monthly_rate * pow(1 + $monthly_rate, $total_months)) / (pow(1 + $monthly_rate, $total_months) - 1);
    $total_payments = $payment * $total_months;
    $total_interest = $total_payments - $loan_amount;

    return array(
        'payment' => $payment,
        'total_payments' => $total_payments,
        'total_interest' => $total_interest,
        'total_periods' => $total_months,
        'principal_percent' => ($loan_amount / $total_payments) * 100,
        'interest_percent' => ($total_interest / $total_payments) * 100,
        'type' => 'student',
        'loan_amount' => $loan_amount,
        'rate_per_period' => $monthly_rate,
        'credit_score' => $credit_score,
        'adjusted_rate' => $interest_rate * 100,
        'payback' => 'monthly'
    );
}

// Display Results
function display_results($results, $show_table) {
    if (isset($results['error'])) {
        echo '<div class="results"><p class="error">' . esc_html($results['error']) . '</p></div>';
        return;
    }
    ?>
    <div class="results">
        <h4>Results</h4>
        <?php
        if ($results['type'] === 'amortized') {
            echo '<p>Credit Score: ' . ucfirst(esc_html($results['credit_score'])) . '</p>';
            echo '<p>Adjusted Interest Rate: ' . number_format($results['adjusted_rate'], 2) . '%</p>';
            echo '<p>Payment Every ' . ($results['payback'] === 'monthly' ? 'Month' : 'Year') . ': $' . number_format($results['payment'], 2) . '</p>';
            echo '<p>Total of ' . esc_html($results['total_periods']) . ' Payments: $' . number_format($results['total_payments'], 2) . '</p>';
            echo '<p>Total Interest: $' . number_format($results['total_interest'], 2) . '</p>';
            echo '<div class="chart">';
            echo '<div class="principal" style="width: ' . esc_attr($results['principal_percent']) . '%;">Principal ' . round($results['principal_percent']) . '%</div>';
            echo '<div class="interest" style="width: ' . esc_attr($results['interest_percent']) . '%;">Interest ' . round($results['interest_percent']) . '%</div>';
            echo '</div>';
            ?>
            <form method="post" style="margin-top: 15px;">
                <input type="hidden" name="calculator_type" value="view_table">
                <input type="hidden" name="active_tab" value="<?php echo esc_attr($results['type']); ?>">
                <input type="hidden" name="table_data" value="<?php echo esc_attr(base64_encode(serialize($results))); ?>">
                <button type="submit">Show Amortization Table</button>
            </form>
            <?php
        } elseif ($results['type'] === 'deferred') {
            echo '<p>Credit Score: ' . ucfirst(esc_html($results['credit_score'])) . '</p>';
            echo '<p>Amount Due at Maturity: $' . number_format($results['amount_due'], 2) . '</p>';
            echo '<p>Total Interest: $' . number_format($results['total_interest'], 2) . '</p>';
            echo '<div class="chart">';
            echo '<div class="principal" style="width: ' . esc_attr($results['principal_percent']) . '%;">Principal ' . round($results['principal_percent']) . '%</div>';
            echo '<div class="interest" style="width: ' . esc_attr($results['interest_percent']) . '%;">Interest ' . round($results['interest_percent']) . '%</div>';
            echo '</div>';
            ?>
            <form method="post" style="margin-top: 15px;">
                <input type="hidden" name="calculator_type" value="view_table">
                <input type="hidden" name="active_tab" value="<?php echo esc_attr($results['type']); ?>">
                <input type="hidden" name="table_data" value="<?php echo esc_attr(base64_encode(serialize($results))); ?>">
                <button type="submit">Show Schedule Table</button>
            </form>
            <?php
        } elseif ($results['type'] === 'bond') {
            echo '<p>Credit Score: ' . ucfirst(esc_html($results['credit_score'])) . '</p>';
            echo '<p>Initial Investment Value: $' . number_format($results['principal'], 2) . '</p>';
            echo '<p>Total Interest: $' . number_format($results['total_interest'], 2) . '</p>';
            echo '<div class="chart">';
            echo '<div class="principal" style="width: ' . esc_attr($results['principal_percent']) . '%;">Principal ' . round($results['principal_percent']) . '%</div>';
            echo '<div class="interest" style="width: ' . esc_attr($results['interest_percent']) . '%;">Interest ' . round($results['interest_percent']) . '%</div>';
            echo '</div>';
            ?>
            <form method="post" style="margin-top: 15px;">
                <input type="hidden" name="calculator_type" value="view_table">
                <input type="hidden" name="active_tab" value="<?php echo esc_attr($results['type']); ?>">
                <input type="hidden" name="table_data" value="<?php echo esc_attr(base64_encode(serialize($results))); ?>">
                <button type="submit">Show Bond Schedule</button>
            </form>
            <?php
        } elseif (in_array($results['type'], ['mortgage', 'bad_credit', 'auto', 'debt_consolidation', 'personal', 'business', 'student'])) {
            echo '<p>Credit Score: ' . ucfirst(esc_html($results['credit_score'])) . '</p>';
            echo '<p>Adjusted Interest Rate: ' . number_format($results['adjusted_rate'], 2) . '%</p>';
            echo '<p>Monthly Payment: $' . number_format($results['payment'], 2) . '</p>';
            echo '<p>Total of ' . esc_html($results['total_periods']) . ' Payments: $' . number_format($results['total_payments'], 2) . '</p>';
            echo '<p>Total Interest: $' . number_format($results['total_interest'], 2) . '</p>';
            echo '<div class="chart">';
            echo '<div class="principal" style="width: ' . esc_attr($results['principal_percent']) . '%;">Principal ' . round($results['principal_percent']) . '%</div>';
            echo '<div class="interest" style="width: ' . esc_attr($results['interest_percent']) . '%;">Interest ' . round($results['interest_percent']) . '%</div>';
            echo '</div>';
            ?>
            <form method="post" style="margin-top: 15px;">
                <input type="hidden" name="calculator_type" value="view_table">
                <input type="hidden" name="active_tab" value="<?php echo esc_attr($results['type']); ?>">
                <input type="hidden" name="table_data" value="<?php echo esc_attr(base64_encode(serialize($results))); ?>">
                <button type="submit">Show Amortization Table</button>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}

// Display Amortization Table
function display_amortization_table($results) {
    $balance = $results['loan_amount'];
    $payment = $results['payment'];
    $rate_per_period = $results['rate_per_period'];
    $total_periods = $results['total_periods'];
    $payback = $results['payback'] ?? 'monthly';
    ?>
    <div class="finance-table">
        <h4><?php echo ucfirst(esc_html($results['type'])); ?> Amortization Table</h4>
        <table>
            <thead>
                <tr>
                    <th><?php echo $payback === 'monthly' ? 'Month' : 'Year'; ?></th>
                    <th>Payment</th>
                    <th>Principal</th>
                    <th>Interest</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($period = 1; $period <= $total_periods; $period++) {
                    $interest = $balance * $rate_per_period;
                    $principal = $payment - $interest;
                    $balance -= $principal;
                    if ($balance < 0) $balance = 0;
                    ?>
                    <tr>
                        <td><?php echo esc_html($period); ?></td>
                        <td>$<?php echo number_format($payment, 2); ?></td>
                        <td>$<?php echo number_format($principal, 2); ?></td>
                        <td>$<?php echo number_format($interest, 2); ?></td>
                        <td>$<?php echo number_format($balance, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Display Deferred Payment Schedule Table
function display_deferred_table($results) {
    $loan_amount = $results['loan_amount'];
    $total_years = $results['total_years'];
    $interest_rate = $results['interest_rate'];
    $compound = $results['compound'];
    $periods = $compound === 'annually' ? ceil($total_years) : ceil($total_years * 12);
    $rate_per_period = $compound === 'annually' ? $interest_rate : $interest_rate / 12;
    ?>
    <div class="finance-table">
        <h4>Deferred Payment Schedule</h4>
        <table>
            <thead>
                <tr>
                    <th><?php echo $compound === 'annually' ? 'Year' : 'Month'; ?></th>
                    <th>Balance</th>
                    <th>Interest</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $balance = $loan_amount;
                for ($period = 0; $period <= $periods; $period++) {
                    $interest = $balance * $rate_per_period;
                    $balance += $interest;
                    ?>
                    <tr>
                        <td><?php echo esc_html($period); ?></td>
                        <td>$<?php echo number_format($balance, 2); ?></td>
                        <td>$<?php echo number_format($interest, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Display Bond Schedule Table
function display_bond_table($results) {
    $principal = $results['principal'];
    $total_years = $results['total_years'];
    $interest_rate = $results['interest_rate'];
    $compound = $results['compound'];
    $periods = $compound === 'annually' ? ceil($total_years) : ceil($total_years * 12);
    $rate_per_period = $compound === 'annually' ? $interest_rate : $interest_rate / 12;
    ?>
    <div class="finance-table">
        <h4>Bond Schedule</h4>
        <table>
            <thead>
                <tr>
                    <th><?php echo $compound === 'annually' ? 'Year' : 'Month'; ?></th>
                    <th>Value</th>
                    <th>Interest</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $value = $principal;
                for ($period = 0; $period <= $periods; $period++) {
                    $interest = $value * $rate_per_period;
                    $value += $interest;
                    ?>
                    <tr>
                        <td><?php echo esc_html($period); ?></td>
                        <td>$<?php echo number_format($value, 2); ?></td>
                        <td>$<?php echo number_format($interest, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

add_shortcode('finance_calculator', 'finance_calculator_shortcode');
?>
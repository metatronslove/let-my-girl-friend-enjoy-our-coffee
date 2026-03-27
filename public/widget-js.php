<?php
/**
 * Dynamic JavaScript generator for Coffee Widget – FULLY MOBILE RESPONSIVE
 * Fixed: Button message/description as tooltip, conditional tabs, active tab selection
 */

if ( ! defined( 'ABSPATH' ) ) {
    header( 'Content-Type: text/plain' );
    echo '// Direct access not allowed';
    exit;
}

header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'X-Content-Type-Options: nosniff' );

$coffee_widget_options = get_option( 'coffee_widget_settings', array() );
$coffee_widget_style_options = get_option( 'coffee_widget_style', array() );
$coffee_widget_code_options = get_option( 'coffee_widget_code', array() );

$coffee_widget_button_content = '☕';
switch ( $coffee_widget_options['button_type'] ?? 'emoji' ) {
    case 'emoji':
        $coffee_widget_button_content = ! empty( $coffee_widget_options['button_emoji'] ) ? esc_html( $coffee_widget_options['button_emoji'] ) : '☕';
        break;
    case 'svg':
        $coffee_widget_button_content = ! empty( $coffee_widget_options['button_svg'] ) ? trim( $coffee_widget_options['button_svg'] ) : '☕';
        break;
    case 'png':
        $coffee_widget_button_content = ! empty( $coffee_widget_options['button_png_url'] )
            ? '<img src="' . esc_url( $coffee_widget_options['button_png_url'] ) . '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Donate">'
            : '☕';
        break;
}

$coffee_widget_payment_methods = isset( $coffee_widget_options['payment_methods'] ) && is_array( $coffee_widget_options['payment_methods'] ) 
    ? $coffee_widget_options['payment_methods'] 
    : array( 'crypto' );

$coffee_widget_donation_tiers = isset( $coffee_widget_options['donation_tiers'] ) && is_array( $coffee_widget_options['donation_tiers'] ) 
    ? $coffee_widget_options['donation_tiers'] 
    : array( '5', '10', '20', '50' );
?>

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        if (typeof coffeeWidgetData === 'undefined') {
            console.error('Coffee Widget: No configuration data found');
            createFallbackButton();
            return;
        }
        
        coffeeWidgetData.config['button_content'] = <?php echo wp_json_encode( $coffee_widget_button_content ); ?>;
        var config = coffeeWidgetData.config;
        var i18n = coffeeWidgetData.i18n;
        var coffeeWidgetId = 'cw-' + Math.random().toString(36).substr(2, 8);        
        
        // =========================================================================
        // CREATE WIDGET CONTAINER
        // =========================================================================
        var container = $('<div>', {
            id: coffeeWidgetId,
            class: 'coffee-widget-container'
        }).appendTo('body');
        
        // =========================================================================
        // CREATE BUTTON with tooltip
        // =========================================================================
        var button = $('<div>', {
            class: 'coffee-widget-button',
            html: config.button_content,
            'aria-label': 'Open donation widget',
            'role': 'button',
            'tabindex': '0',
            title: config.message || ''
        }).appendTo(container);
        
        // Add description tooltip (appears on hover)
        if (config.description) {
            var tooltip = $('<div>', {
                class: 'coffee-widget-tooltip',
                text: config.description,
                css: {
                    position: 'absolute',
                    bottom: '70px',
                    [config.position || 'right']: '0',
                    background: '#333',
                    color: '#fff',
                    padding: '8px 12px',
                    borderRadius: '6px',
                    fontSize: '12px',
                    whiteSpace: 'nowrap',
                    zIndex: 10000,
                    display: 'none',
                    boxShadow: '0 2px 8px rgba(0,0,0,0.2)'
                }
            }).appendTo(container);
            
            button.on('mouseenter', function() {
                tooltip.fadeIn(200);
            }).on('mouseleave', function() {
                tooltip.fadeOut(200);
            });
        }
        
        // =========================================================================
        // CREATE MODAL
        // =========================================================================
        var modal = $('<div>', {
            class: 'coffee-widget-modal',
            'role': 'dialog',
            'aria-modal': 'true',
            'aria-label': 'Donation options'
        }).appendTo(container);
        
        var closeBtn = $('<button>', {
            class: 'coffee-widget-close',
            html: '✕',
            'aria-label': 'Close'
        }).appendTo(modal);
        
        // =========================================================================
        // BUILD TABS CONDITIONALLY (only if data exists)
        // =========================================================================
        var tabData = [];
        
        // Crypto
        if (config.payment_methods.includes('crypto') && config.crypto_address) {
            tabData.push({
                id: 'crypto',
                label: '🔗 ' + (i18n.crypto || 'Crypto'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <p>${escapeHtml(i18n.crypto_address_label || 'Send cryptocurrency directly to this address:')}</p>
                        <div class="crypto-address">${escapeHtml(config.crypto_address)}</div>
                        <button class="copy-address">${escapeHtml(i18n.copy_address || 'Copy Address')}</button>
                        <p class="network-note"><strong>${escapeHtml(i18n.crypto_network || 'Network')}:</strong> ${escapeHtml(config.crypto_network)}</p>
                        <p class="info-note">${escapeHtml(i18n.crypto_note || 'Funds go directly to your wallet. No fees.')}</p>
                    `);
                    panel.find('.copy-address').on('click', function() {
                        var btn = $(this);
                        var original = btn.text();
                        navigator.clipboard.writeText(config.crypto_address).then(function() {
                            btn.text(i18n.copied || 'Copied!');
                            setTimeout(function() { btn.text(original); }, 2000);
                        }).catch(function() {
                            alert(i18n.copied || 'Copied to clipboard: ' + config.crypto_address);
                        });
                    });
                }
            });
        }
        
        // NOWPayments
        if (config.payment_methods.includes('nowpayments') && config.nowpayments_api_key) {
            tabData.push({
                id: 'nowpayments',
                label: '⚡ ' + (i18n.nowpayments || 'NOWPayments'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <p>${escapeHtml(i18n.nowpayments_desc || 'Pay with credit card – crypto goes directly to your wallet.')}</p>
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="nowpayments-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <button id="nowpayments-pay" class="payment-button" disabled>${escapeHtml(i18n.pay_with_card || 'Pay with Card')}</button>
                        <div id="nowpayments-loader" class="loader" style="display:none;">⏳ ${escapeHtml(i18n.processing || 'Processing...')}</div>
                    `);
                    var currentAmount = 0;
                    var payBtn = panel.find('#nowpayments-pay');
                    var customInput = panel.find('#nowpayments-custom-amount');
                    var loader = panel.find('#nowpayments-loader');
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        payBtn.prop('disabled', currentAmount <= 0 || isNaN(currentAmount));
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                    
                    payBtn.on('click', function() {
                        if (currentAmount <= 0) return;
                        payBtn.hide();
                        loader.show();
                        $.ajax({
                            url: coffeeWidgetData.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'coffee_widget_create_nowpayments_invoice',
                                amount: currentAmount,
                                currency: 'USD',
                                crypto_currency: config.crypto_network || 'usdt',
                                nonce: coffeeWidgetData.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.open(response.data.invoice_url, '_blank');
                                    alert('Payment window opened. Complete your payment there.');
                                    modal.hide();
                                } else {
                                    alert('Error: ' + response.data);
                                    payBtn.show();
                                }
                                loader.hide();
                            },
                            error: function() {
                                alert('NOWPayments error. Please try again.');
                                payBtn.show();
                                loader.hide();
                            }
                        });
                    });
                }
            });
        }
        
        // CoinGate
        if (config.payment_methods.includes('coingate') && config.coingate_api_key) {
            tabData.push({
                id: 'coingate',
                label: '🪙 ' + (i18n.coingate || 'CoinGate'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <p>${escapeHtml(i18n.coingate_desc || 'Pay with credit card – crypto to your wallet.')}</p>
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="coingate-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <button id="coingate-pay" class="payment-button" disabled>${escapeHtml(i18n.pay_with_card || 'Pay with Card')}</button>
                        <div id="coingate-loader" class="loader" style="display:none;">⏳ ${escapeHtml(i18n.processing || 'Processing...')}</div>
                    `);
                    var currentAmount = 0;
                    var payBtn = panel.find('#coingate-pay');
                    var customInput = panel.find('#coingate-custom-amount');
                    var loader = panel.find('#coingate-loader');
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        payBtn.prop('disabled', currentAmount <= 0 || isNaN(currentAmount));
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                    
                    payBtn.on('click', function() {
                        if (currentAmount <= 0) return;
                        payBtn.hide();
                        loader.show();
                        $.ajax({
                            url: coffeeWidgetData.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'coffee_widget_create_coingate_invoice',
                                amount: currentAmount,
                                nonce: coffeeWidgetData.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.open(response.data.invoice_url, '_blank');
                                    alert('Payment window opened. Complete your payment there.');
                                    modal.hide();
                                } else {
                                    alert('Error: ' + response.data);
                                    payBtn.show();
                                }
                                loader.hide();
                            },
                            error: function() {
                                alert('CoinGate error. Please try again.');
                                payBtn.show();
                                loader.hide();
                            }
                        });
                    });
                }
            });
        }
        
        // BitPay
        if (config.payment_methods.includes('bitpay') && config.bitpay_api_key) {
            tabData.push({
                id: 'bitpay',
                label: '🏦 ' + (i18n.bitpay || 'BitPay'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <p>${escapeHtml(i18n.bitpay_desc || 'Enterprise-grade crypto payments. Best for US businesses.')}</p>
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="bitpay-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <button id="bitpay-pay" class="payment-button" disabled>${escapeHtml(i18n.pay_with_card || 'Pay with Card')}</button>
                        <div id="bitpay-loader" class="loader" style="display:none;">⏳ ${escapeHtml(i18n.processing || 'Processing...')}</div>
                    `);
                    var currentAmount = 0;
                    var payBtn = panel.find('#bitpay-pay');
                    var customInput = panel.find('#bitpay-custom-amount');
                    var loader = panel.find('#bitpay-loader');
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        payBtn.prop('disabled', currentAmount <= 0 || isNaN(currentAmount));
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                    
                    payBtn.on('click', function() {
                        if (currentAmount <= 0) return;
                        payBtn.hide();
                        loader.show();
                        $.ajax({
                            url: coffeeWidgetData.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'coffee_widget_create_bitpay_invoice',
                                amount: currentAmount,
                                nonce: coffeeWidgetData.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.open(response.data.invoice_url, '_blank');
                                    alert('Payment window opened. Complete your payment there.');
                                    modal.hide();
                                } else {
                                    alert('Error: ' + response.data);
                                    payBtn.show();
                                }
                                loader.hide();
                            },
                            error: function() {
                                alert('BitPay error. Please try again.');
                                payBtn.show();
                                loader.hide();
                            }
                        });
                    });
                }
            });
        }
        
        // MoonPay
        if (config.payment_methods.includes('moonpay') && config.moonpay_api_key) {
            tabData.push({
                id: 'moonpay',
                label: '🌙 ' + (i18n.moonpay || 'MoonPay'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <p>${escapeHtml(i18n.moonpay_desc || 'Buy crypto with credit card – then send it to the address below.')}</p>
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="moonpay-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <button id="moonpay-buy" class="payment-button" disabled>${escapeHtml(i18n.buy_crypto || 'Buy Crypto')}</button>
                        <div class="crypto-address" style="margin-top:15px;">
                            <strong>${escapeHtml(i18n.your_crypto_address || 'Your wallet address:')}</strong><br>
                            <code>${escapeHtml(config.crypto_address)}</code>
                            <button class="copy-address-small" style="margin-left:10px;">📋</button>
                        </div>
                        <p class="info-note">${escapeHtml(i18n.moonpay_note || 'After purchasing, send crypto to the address above.')}</p>
                    `);
                    var currentAmount = 0;
                    var buyBtn = panel.find('#moonpay-buy');
                    var customInput = panel.find('#moonpay-custom-amount');
                    var copyBtn = panel.find('.copy-address-small');
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        buyBtn.prop('disabled', currentAmount <= 0 || isNaN(currentAmount));
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                    
                    buyBtn.on('click', function() {
                        if (currentAmount <= 0) return;
                        var moonpayUrl = 'https://buy.moonpay.com?apiKey=' + encodeURIComponent(config.moonpay_api_key) +
                                         '&currencyCode=' + config.crypto_network.toUpperCase() +
                                         '&baseCurrencyCode=USD&baseCurrencyAmount=' + currentAmount;
                        window.open(moonpayUrl, '_blank');
                        alert('Complete purchase on MoonPay, then send crypto to the address above.');
                    });
                    
                    copyBtn.on('click', function() {
                        var btn = $(this);
                        var original = btn.text();
                        navigator.clipboard.writeText(config.crypto_address).then(function() {
                            btn.text('✓');
                            setTimeout(function() { btn.text(original); }, 2000);
                        });
                    });
                }
            });
        }
        
        // Stripe
        if (config.payment_methods.includes('stripe') && config.stripe_publishable_key) {
            tabData.push({
                id: 'stripe',
                label: '💳 ' + (i18n.stripe || 'Stripe'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="stripe-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <div id="stripe-elements"></div>
                        <button id="stripe-pay" class="payment-button" disabled>${escapeHtml(i18n.pay_with_card || 'Pay with Card')}</button>
                        <div id="stripe-loader" class="loader" style="display:none;">⏳ ${escapeHtml(i18n.processing || 'Processing...')}</div>
                    `);
                    var currentAmount = 0;
                    var payBtn = panel.find('#stripe-pay');
                    var customInput = panel.find('#stripe-custom-amount');
                    var loader = panel.find('#stripe-loader');
                    var elementsDiv = panel.find('#stripe-elements');
                    var stripe = null, elements = null, cardElement = null;
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        payBtn.prop('disabled', currentAmount <= 0 || isNaN(currentAmount));
                        if (currentAmount > 0 && !cardElement && typeof Stripe !== 'undefined') {
                            stripe = Stripe(config.stripe_publishable_key);
                            elements = stripe.elements();
                            cardElement = elements.create('card');
                            cardElement.mount(elementsDiv[0]);
                        }
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                    
                    payBtn.on('click', function() {
                        if (currentAmount <= 0 || !stripe || !cardElement) return;
                        payBtn.hide();
                        loader.show();
                        $.ajax({
                            url: coffeeWidgetData.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'coffee_widget_create_payment_intent',
                                amount: currentAmount,
                                nonce: coffeeWidgetData.nonce
                            },
                            success: function(response) {
                                if (!response.success) {
                                    alert(response.data);
                                    payBtn.show();
                                    loader.hide();
                                    return;
                                }
                                stripe.confirmCardPayment(response.data.client_secret, {
                                    payment_method: { card: cardElement }
                                }).then(function(result) {
                                    if (result.error) {
                                        alert(result.error.message);
                                        payBtn.show();
                                        loader.hide();
                                    } else if (result.paymentIntent.status === 'succeeded') {
                                        alert(i18n.payment_success || 'Payment successful!');
                                        modal.hide();
                                    }
                                });
                            },
                            error: function() {
                                alert('Stripe error. Please try again.');
                                payBtn.show();
                                loader.hide();
                            }
                        });
                    });
                }
            });
        }
        
        // PayPal
        if (config.payment_methods.includes('paypal') && config.paypal_client_id) {
            tabData.push({
                id: 'paypal',
                label: '🟡 ' + (i18n.paypal || 'PayPal'),
                hasContent: true,
                build: function(panel) {
                    panel.html(`
                        <div class="donation-tiers">
                            ${<?php echo wp_json_encode( $coffee_widget_donation_tiers ); ?>.map(function(amount) { 
                                return '<button class="tier" data-amount="' + amount + '">$' + amount + '</button>';
                            }).join('')}
                        </div>
                        <div class="custom-amount-container">
                            <input type="number" id="paypal-custom-amount" placeholder="${escapeHtml(i18n.custom_amount || 'Custom amount (USD)')}" step="0.01" min="1">
                        </div>
                        <div id="paypal-button-container"></div>
                    `);
                    var currentAmount = 0;
                    var customInput = panel.find('#paypal-custom-amount');
                    var container = panel.find('#paypal-button-container');
                    
                    function renderPayPalButton(amount) {
                        container.empty();
                        if (typeof paypal === 'undefined') {
                            container.html('<p style="color:red;">PayPal SDK not loaded.</p>');
                            return;
                        }
                        paypal.Buttons({
                            createOrder: function(data, actions) {
                                return fetch(coffeeWidgetData.ajaxurl, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: new URLSearchParams({
                                        action: 'coffee_widget_create_paypal_order',
                                        amount: amount,
                                        nonce: coffeeWidgetData.nonce
                                    })
                                }).then(function(response) {
                                    return response.json();
                                }).then(function(orderData) {
                                    if (!orderData.success) throw new Error(orderData.data);
                                    return orderData.data.order_id;
                                });
                            },
                            onApprove: function(data, actions) {
                                return fetch(coffeeWidgetData.ajaxurl, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: new URLSearchParams({
                                        action: 'coffee_widget_capture_paypal_order',
                                        order_id: data.orderID,
                                        nonce: coffeeWidgetData.nonce
                                    })
                                }).then(function(response) {
                                    return response.json();
                                }).then(function(captureData) {
                                    if (captureData.success) {
                                        alert(i18n.payment_success || 'Payment successful!');
                                        modal.hide();
                                    } else {
                                        alert(i18n.payment_failed || 'Payment failed: ' + captureData.data);
                                    }
                                });
                            },
                            onError: function(err) {
                                alert(i18n.payment_failed || 'PayPal error: ' + err);
                            }
                        }).render('#paypal-button-container');
                    }
                    
                    function setAmount(val) {
                        currentAmount = parseFloat(val);
                        if (currentAmount > 0 && !isNaN(currentAmount)) {
                            renderPayPalButton(currentAmount);
                        } else {
                            container.empty();
                        }
                    }
                    
                    panel.find('.tier').on('click', function() {
                        setAmount($(this).data('amount'));
                        customInput.val($(this).data('amount'));
                    });
                    customInput.on('input', function() { setAmount($(this).val()); });
                }
            });
        }
        
        // =========================================================================
        // BUILD TAB BAR AND PANELS
        // =========================================================================
        if (tabData.length === 0) {
            var emptyPanel = $('<div>', {
                class: 'coffee-widget-tab-panel',
                id: coffeeWidgetId + '-tab-empty',
                'data-tab': 'empty',
                html: '<p style="text-align:center;">Please configure payment methods in admin panel.</p>'
            });
            tabData.push({
                id: 'empty',
                label: 'ℹ️ Info',
                hasContent: true,
                panel: emptyPanel
            });
        }
        
        var tabBar = $('<div>', { class: 'coffee-widget-tabs' }).appendTo(modal);
        var contentDiv = $('<div>', { class: 'coffee-widget-content' }).appendTo(modal);
        var panels = {};
        
        $.each(tabData, function(idx, tab) {
            var tabBtn = $('<button>', {
                class: 'coffee-widget-tab',
                text: tab.label,
                'data-tab': tab.id,
                'role': 'tab'
            }).appendTo(tabBar);
            
            var panel = $('<div>', {
                class: 'coffee-widget-tab-panel',
                id: coffeeWidgetId + '-tab-' + tab.id,
                'data-tab': tab.id,
                'role': 'tabpanel'
            }).appendTo(contentDiv);
            panels[tab.id] = panel;
            if (tab.build) tab.build(panel);
            else if (tab.panel) panel.html(tab.panel.html());
        });
        
        // =========================================================================
        // TAB SWITCHING
        // =========================================================================
        function switchTab(tabId) {
            $('#' + coffeeWidgetId + ' .coffee-widget-tab').removeClass('active').attr('aria-selected', 'false');
            $('#' + coffeeWidgetId + ' .coffee-widget-tab-panel').hide();
            $('#' + coffeeWidgetId + ' .coffee-widget-tab[data-tab="' + tabId + '"]').addClass('active').attr('aria-selected', 'true');
            $('#' + coffeeWidgetId + '-tab-' + tabId).show();
        }
        
        $('#' + coffeeWidgetId + ' .coffee-widget-tab').on('click', function() {
            switchTab($(this).data('tab'));
        });
        
        var firstActive = tabData[0] ? tabData[0].id : null;
        if (firstActive) switchTab(firstActive);
        
        // =========================================================================
        // MODAL TOGGLE
        // =========================================================================
        function openModal() {
            modal.show();
            $(document).on('keydown.coffeeWidget', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        }
        
        function closeModal() {
            modal.hide();
            $(document).off('keydown.coffeeWidget');
        }
        
        button.on('click', function(e) {
            e.stopPropagation();
            if (modal.is(':visible')) closeModal();
            else openModal();
        });
        
        closeBtn.on('click', closeModal);
        $(document).on('click', function(e) {
            if (!container[0].contains(e.target)) closeModal();
        });
        modal.on('click', function(e) { e.stopPropagation(); });
        
        // =========================================================================
        // CSS STYLES
        // =========================================================================
        var style = $('<style>').text(`
            .coffee-widget-container {
                position: fixed;
                bottom: ${config.margin_y}px;
                ${config.position}: ${config.margin_x}px;
                z-index: 9998;
            }
            .coffee-widget-button {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: ${config.color};
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                transition: transform 0.2s;
            }
            .coffee-widget-button:hover {
                transform: scale(1.05);
            }
            .coffee-widget-tooltip {
                position: absolute;
                bottom: 70px;
                ${config.position}: 0;
                background: #333;
                color: #fff;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 10000;
                display: none;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            .coffee-widget-modal {
                position: absolute;
                bottom: 80px;
                ${config.position}: 0;
                width: 380px;
                max-width: calc(100vw - 30px);
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                display: none;
                flex-direction: column;
                overflow: hidden;
                z-index: 9999;
            }
            .coffee-widget-close {
                position: absolute;
                top: 12px;
                right: 12px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #f0f0f0;
                border: none;
                cursor: pointer;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
            }
            .coffee-widget-tabs {
                display: flex;
                flex-wrap: wrap;
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
                padding: 12px 12px 0 12px;
                gap: 4px;
            }
            .coffee-widget-tab {
                padding: 10px 14px;
                cursor: pointer;
                font-weight: 500;
                font-size: 13px;
                color: #6c757d;
                border-radius: 20px 20px 0 0;
                background: none;
                border: none;
            }
            .coffee-widget-tab.active {
                color: #2c3e50;
                background: white;
                border-bottom: 2px solid ${config.color};
            }
            .coffee-widget-content {
                padding: 20px;
                max-height: 65vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            .coffee-widget-tab-panel {
                display: none;
            }
            .donation-tiers {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin: 15px 0;
            }
            .tier {
                background: #f1f3f5;
                border: none;
                padding: 12px 20px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 500;
                flex: 1 0 auto;
                min-width: 70px;
            }
            .custom-amount-container input {
                width: 100%;
                padding: 14px;
                font-size: 16px;
                border: 1px solid #dee2e6;
                border-radius: 40px;
                text-align: center;
                box-sizing: border-box;
            }
            .crypto-address {
                background: #f8f9fa;
                padding: 14px;
                border-radius: 12px;
                word-break: break-all;
                font-family: monospace;
                margin: 12px 0;
                border: 1px solid #e9ecef;
            }
            .copy-address, .copy-address-small {
                background: ${config.color};
                border: none;
                padding: 10px 16px;
                border-radius: 40px;
                cursor: pointer;
                font-weight: 600;
                width: 100%;
                margin-top: 8px;
            }
            .copy-address-small {
                width: auto;
                margin-top: 0;
                margin-left: 8px;
            }
            .payment-button {
                background: ${config.color};
                border: none;
                padding: 14px 20px;
                border-radius: 40px;
                font-weight: 600;
                font-size: 16px;
                cursor: pointer;
                width: 100%;
                margin-top: 12px;
            }
            .payment-button:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            .loader {
                text-align: center;
                padding: 20px;
                color: #6c757d;
            }
            .network-note, .info-note {
                font-size: 12px;
                color: #6c757d;
                margin: 8px 0;
            }
            @media (max-width: 600px) {
                .coffee-widget-modal {
                    position: fixed;
                    top: auto;
                    bottom: 90px;
                    left: 50%;
                    right: auto;
                    transform: translateX(-50%);
                    width: calc(100vw - 20px);
                    max-width: 400px;
                    max-height: 80vh;
                }
                .coffee-widget-button {
                    width: 52px;
                    height: 52px;
                    font-size: 22px;
                }
                .coffee-widget-tab {
                    padding: 8px 12px;
                    font-size: 12px;
                }
                .tier {
                    padding: 10px 16px;
                    font-size: 14px;
                }
                .payment-button {
                    padding: 12px 16px;
                    font-size: 14px;
                }
            }
        `);
        $('head').append(style);
        
        <?php if ( ! empty( $coffee_widget_style_options['custom_css'] ) ) : ?>
            var customStyle = $('<style>').text(<?php echo wp_json_encode( $coffee_widget_style_options['custom_css'] ); ?>);
            $('head').append(customStyle);
        <?php endif; ?>
        
        <?php if ( ! empty( $coffee_widget_code_options['custom_js'] ) ) : ?>
            // Custom JS from settings
            try {
                eval(<?php echo wp_json_encode( $coffee_widget_code_options['custom_js'] ); ?>);
            } catch(e) {
                console.error('Coffee Widget: Custom JS error', e);
            }
        <?php endif; ?>
        
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function createFallbackButton() {
            var fallbackBtn = $('<div>', {
                class: 'coffee-widget-fallback',
                html: '☕',
                css: {
                    position: 'fixed',
                    bottom: '20px',
                    right: '20px',
                    width: '56px',
                    height: '56px',
                    borderRadius: '50%',
                    background: '#FFDD00',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '24px',
                    cursor: 'pointer',
                    zIndex: 999999,
                    boxShadow: '0 2px 10px rgba(0,0,0,0.2)'
                }
            }).appendTo('body');
            fallbackBtn.on('click', function() {
                alert('Coffee Widget: Please configure payment methods in WordPress admin panel.');
            });
        }
        
        console.log('Coffee Widget: Initialized successfully');
    });
})(jQuery);

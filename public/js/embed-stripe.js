jQuery(document).ready(function($) {
    var stripe = Stripe(config.stripe_publishable_key);
    var stripeModal = document.querySelector('.coffee-widget-tab-panel[data-tab="stripe"]');
    if (!stripeModal) return;

    var amount = 0;
    var customInput = stripeModal.querySelector('#custom-amount');
    var tierButtons = stripeModal.querySelectorAll('.tier');
    var payButton = stripeModal.querySelector('#stripe-pay');

    function setAmount(val) {
        amount = parseFloat(val);
        payButton.disabled = amount <= 0;
    }

    tierButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            setAmount(this.dataset.amount);
            customInput.value = this.dataset.amount;
        });
    });
    customInput.addEventListener('input', function() {
        setAmount(this.value);
    });

    payButton.addEventListener('click', function(e) {
        if (amount <= 0) return;
        // Create a checkout session via AJAX (needs server endpoint)
        // For simplicity, we can redirect to a Stripe Payment Link generated from admin.
        // Alternatively, implement a server-side endpoint using the secret key.
        alert('Stripe integration requires server-side endpoint. For demo, we will use a redirect to a Stripe Payment Link.');
        // In production, you would create a session and redirect to checkout.
    });
});

# Let My Girl Friend Enjoy Our Coffee

**A free, self-hosted donation and payment widget for WordPress.**  
Accept one-time donations, recurring memberships, and cryptocurrency (Quai) without any monthly fees. Stripe and PayPal integrations are built-in and work **inline** – your visitors never leave your site.

## Features

- 🧾 **Inline payments** – Credit card via Stripe Elements, PayPal Smart Buttons, and crypto address all inside a modal.
- 💰 **Custom donation tiers** – Set your own amounts or allow custom input.
- 🔗 **Crypto support** – Quai Network (free) and other networks; wallet address displayed with copy button.
- 💳 **Stripe integration** – Use your free Stripe account; card details entered directly in the widget.
- 🟡 **PayPal integration** – Embedded Smart Buttons, no redirect.
- 🎨 **Fully customizable** – Button color, icon, position, margins, and custom CSS/JS.
- 🌍 **Multi-language** – Includes English and Turkish translations; easily extensible.
- 📱 **Mobile‑friendly** – Responsive modal works on all devices.
- 🆓 **100% free** – No monthly subscriptions, no hidden costs. You only pay transaction fees to Stripe/PayPal.

## Installation

1. Upload the `let-my-girl-friend-enjoy-our-coffee` folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings → Coffee Widget** to configure the button appearance.
4. Go to **Coffee Widget → Payment Methods** and enter your crypto wallet address, Stripe keys, and/or PayPal credentials.
5. The widget will automatically appear on your site as a floating button.

## Requirements

- WordPress 5.0+
- PHP 7.2+
- For Stripe: you need the Stripe PHP library (install via Composer). See instructions in the plugin's Help tab.
- For PayPal: no extra library required.

## How to Get Free Services

- **Free hosting**: [InfinityFree](https://infinityfree.net) gives you a free domain and hosting for WordPress.
- **Free crypto wallet**: [Quai Network](https://quai.network) offers a free, decentralized wallet.
- **Free Stripe account**: [Stripe](https://stripe.com) – pay only per transaction.
- **Free PayPal business account**: [PayPal](https://paypal.com) – standard transaction fees apply.

## Development

### Local Setup
```bash
git clone https://github.com/metatronslove/coffee-widget.git
cd coffee-widget
# Symlink to your WordPress plugins directory
ln -s $(pwd) /path/to/wp-content/plugins/let-my-girl-friend-enjoy-our-coffee
```

### Translations
```bash
# Generate .pot file
wp i18n make-pot . languages/coffee-widget.pot
# Update .po files
msgmerge -U languages/tr_TR.po languages/coffee-widget.pot
# Compile .mo
msgfmt languages/tr_TR.po -o languages/tr_TR.mo
```

## Support & Sponsorship

If you like this plugin, please consider supporting the developer via:

- **Crypto (Quai)**: `0x00385405687ddb205440DABCbaC30D56b63D6F2B`
- **Buy Me a Coffee**: [https://buymeacoffee.com/metatronslove](https://buymeacoffee.com/metatronslove)

You can also use the generated **GitHub support page** at `/wp-content/plugins/let-my-girl-friend-enjoy-our-coffee/public/github-support.php` in your `FUNDING.yml`.

## License

GPL-2.0+ – See [LICENSE](LICENSE) file.

## Changelog

### 1.0.0
- Initial release.
- Inline payments via Stripe and PayPal.
- Crypto wallet support (Quai).
- Responsive modal.
- Customizable button and donation tiers.

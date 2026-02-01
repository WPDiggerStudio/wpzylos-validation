# WPZylos Validation

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-WPDiggerStudio-181717?logo=github)](https://github.com/WPDiggerStudio/wpzylos-validation)

Minimal validation with localized messages for WPZylos framework.

📖 **[Full Documentation](https://wpzylos.com)** | 🐛 **[Report Issues](https://github.com/WPDiggerStudio/wpzylos-validation/issues)**

---

## ✨ Features

- **Validation Rules** — Required, email, min, max, and more
- **Custom Rules** — Create your own validation rules
- **Localized Messages** — Translatable error messages
- **FormRequest** — Laravel-style form validation
- **Array Validation** — Validate nested data

---

## 📋 Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | ^8.0    |

---

## 🚀 Installation

```bash
composer require wpdiggerstudio/wpzylos-validation
```

---

## 📖 Quick Start

```php
use WPZylos\Framework\Validation\Validator;

$validator = new Validator($data, [
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email'],
    'age' => ['required', 'integer', 'min:18'],
]);

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

---

## 🏗️ Core Features

### Available Rules

```php
$rules = [
    'name' => ['required', 'string', 'min:2', 'max:100'],
    'email' => ['required', 'email'],
    'age' => ['required', 'integer', 'min:18', 'max:120'],
    'website' => ['nullable', 'url'],
    'password' => ['required', 'min:8', 'confirmed'],
    'terms' => ['accepted'],
    'category' => ['required', 'in:tech,news,sports'],
];
```

### Custom Rules

```php
Validator::extend('phone', function ($attribute, $value) {
    return preg_match('/^\+?[1-9]\d{1,14}$/', $value);
}, 'The :attribute must be a valid phone number.');

// Usage
$rules = ['phone' => ['required', 'phone']];
```

### Error Messages

```php
if ($validator->fails()) {
    foreach ($validator->errors()->all() as $error) {
        echo $error;
    }

    // Get errors for a specific field
    $emailErrors = $validator->errors()->get('email');
}
```

### Custom Error Messages

```php
$validator = new Validator($data, $rules, [
    'email.required' => 'We need your email address.',
    'email.email' => 'Please enter a valid email.',
]);
```

---

## 📦 Related Packages

| Package                                                                | Description            |
| ---------------------------------------------------------------------- | ---------------------- |
| [wpzylos-core](https://github.com/WPDiggerStudio/wpzylos-core)         | Application foundation |
| [wpzylos-http](https://github.com/WPDiggerStudio/wpzylos-http)         | HTTP handling          |
| [wpzylos-scaffold](https://github.com/WPDiggerStudio/wpzylos-scaffold) | Plugin template        |

---

## 📖 Documentation

For comprehensive documentation, tutorials, and API reference, visit **[wpzylos.com](https://wpzylos.com)**.

---

## ☕ Support the Project

If you find this package helpful, consider buying me a coffee! Your support helps maintain and improve the WPZylos ecosystem.

<a href="https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC" target="_blank">
  <img src="https://img.shields.io/badge/Donate-PayPal-blue.svg?style=for-the-badge&logo=paypal" alt="Donate with PayPal" />
</a>

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [WPDiggerStudio](https://github.com/WPDiggerStudio)**

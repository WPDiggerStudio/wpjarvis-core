
# 🧠 WPJarvis Core

**A Laravel-inspired modular framework for modern WordPress plugin development.**  
Built for developers who want structure, speed, and simplicity when building scalable WordPress plugins.

---

## 📦 Installation

> Requires: PHP 8.0+, WordPress 6.0+, Composer

```bash
composer require wpjarvis/core
```

---

## ⚙️ What is WPJarvis?

WPJarvis Core is the heart of a Laravel-style plugin development experience for WordPress. It brings modern design patterns — like service providers, facades, dependency injection, event-driven architecture, and Eloquent ORM — to WordPress plugin development.

This package is **not a plugin by itself**, but the **framework core** that powers plugins built using the [WPJarvis scaffold](https://github.com/WPDiggerStudio/WPJarvis).

---

## 🗂 Folder Structure Overview

```
wpjarvis-core/
├── src/
│   ├── Config/            # Framework & user-defined configuration
│   ├── Console/           # CLI Commands (via WP-CLI)
│   ├── Container/         # IoC Container & Service Providers
│   ├── Database/          # Migrations, Query Builder, Models
│   ├── Events/            # Event Dispatcher
│   ├── Exceptions/        # Custom exception handling
│   ├── Foundation/        # Core bootstrap and app logic
│   ├── Http/              # Middleware, Controllers, Requests
│   ├── Logging/           # Logging abstraction
│   ├── Queue/             # Jobs and Queue Dispatcher
│   ├── Routing/           # Router and Route definitions
│   ├── Scheduling/        # Cron-based scheduling system
│   ├── Support/           # Facades, traits, and utilities
│   ├── Validation/        # Validation rules and service
│   ├── View/              # Blade templating engine
│   ├── WordPress/         # WP-specific features (shortcodes, metaboxes, etc.)
│   └── helpers.php        # Global helper functions
└── tests/                 # PHPUnit & WordPress integration tests
```

---

## 💡 Features

- 🔧 Laravel-style Service Providers & Dependency Injection
- 🧱 Modular and Extensible Architecture
- 📃 Blade-based View Rendering
- 🗃 Database Migrations + Eloquent ORM
- 🌀 Event System
- 🧪 Built-in Testing Setup (PHPUnit + Brain Monkey)
- 🧩 WordPress-specific Hooks (Shortcodes, Menus, Widgets, Blocks)
- ⚙️ Config Cache & Environment Loading
- 🛡 Middleware Support
- 🧾 CLI Integration with WP-CLI

---

## 🧰 Development Requirements

| Tool             | Version         |
|------------------|-----------------|
| PHP              | ^8.0            |
| Composer         | Latest          |
| WordPress        | 6.0+            |
| WP-CLI (optional)| ^2.11.0         |

---

## 🧪 Run Tests

```bash
composer test
```

---

## 🧑‍💻 Contributing

We welcome contributions! Please fork the repo and submit a pull request.

- Report bugs or request features via [GitHub Issues](https://github.com/WPDiggerStudio/wpjarvis-core/issues)
- Follow PSR-4 and Laravel-style conventions
- Run tests before submitting a PR

---

## 📜 License

MIT © [UPDigger & UPRootApps Team](https://uprootapps.com)

---

## 🧭 Next Steps

📖 Check out the **[Documentation »](https://github.com/wpjarvis/core/wiki)**  
To get started building your plugin, install the scaffold tool: [WPJarvis](https://github.com/WPDiggerStudio/WPJarvis)


# 🧠 WPJarvis Core

**A Laravel-inspired modular framework for modern WordPress plugin development.**  
Built for developers who want structure, speed, and simplicity when building scalable WordPress plugins.

---

## 📦 Installation

> Requires: PHP 8.0+, WordPress 6.0+, Composer

Install via Composer:

```bash
composer require wpjarvis/core
```

Ensure you have Composer installed and available in your PATH. If you're setting up a project using the WPJarvis scaffold, you'll also need to install the [wpjarvis](https://github.com/WPDiggerStudio/WPJarvis) CLI tool.

---

## ⚙️ What is WPJarvis?

WPJarvis Core is a powerful engine designed to modernize WordPress plugin development by offering:

- Laravel-style Service Providers
- Eloquent ORM support
- Middleware and HTTP abstraction
- Dependency Injection via a robust IoC Container
- Blade templating
- Event dispatching and queue handling
- WP-specific tools for menus, shortcodes, widgets, and more

It is the **foundation package**, meant to be extended by plugins scaffolded through the WPJarvis CLI tool, making plugin development not just easier but elegant and scalable.

---

## 🗂 Folder Structure Overview

```
wpjarvis-core/
├── src/
│   ├── Config/            # Configuration loading and caching
│   ├── Console/           # WP-CLI commands & scaffolding tools
│   ├── Container/         # IoC container bindings & service providers
│   ├── Database/          # Models, migrations, schema builder
│   ├── Events/            # Event dispatcher and listeners
│   ├── Exceptions/        # Custom exception classes and global handler
│   ├── Foundation/        # Bootstrapping logic & application lifecycle
│   ├── Http/              # Request, response, middleware, controller logic
│   ├── Logging/           # Logging abstraction via Monolog
│   ├── Queue/             # Job dispatching and queue interface
│   ├── Routing/           # Route registration and resolution
│   ├── Scheduling/        # Scheduled jobs and cron support
│   ├── Support/           # Facades, traits, and utility helpers
│   ├── Validation/        # Validator integration via Illuminate
│   ├── View/              # Blade engine setup and rendering support
│   ├── WordPress/         # WP integrations: menus, metaboxes, blocks, etc.
│   └── helpers.php        # Global helper functions
└── tests/                 # PHPUnit + Brain Monkey integration tests
```

---

## 💡 Features

- **🔧 Service Providers** — Modular application architecture via registration-based loading
- **🧱 Dependency Injection** — Power from Laravel's IoC container using `illuminate/container`
- **📃 Views** — Blade templating engine with asset injection and layout support
- **🗃 ORM & DB** — Eloquent ORM, schema builder, and Laravel-style migrations
- **🌀 Events** — Event broadcasting and listener-based architecture
- **🧪 Testing Ready** — Comes with PHPUnit, Mockery, and Brain Monkey for WP mocking
- **🧩 WP Integration** — Simplifies WP menus, widgets, shortcodes, blocks, and more
- **⚙️ Config System** — Environment loading, caching, and configuration abstraction
- **🛡 Middleware** — Easily assign request-level filters and guards
- **🧾 CLI** — Extendable WP-CLI-based command suite for automation and scaffolding

---

## 🧰 Development Requirements

| Tool             | Version         |
|------------------|-----------------|
| PHP              | ^8.0            |
| Composer         | Latest          |
| WordPress        | 6.0+            |
| WP-CLI (optional)| ^2.11.0         |

Also recommended:
- MySQL 5.7+/MariaDB 10.3+ for Eloquent compatibility
- Laravel Valet or LocalWP for fast local development

---

## 🧪 Run Tests

To run the unit and integration tests:

```bash
composer test
```

Tests use:
- [PHPUnit](https://phpunit.de/)
- [Brain Monkey](https://brain-wp.github.io/BrainMonkey/)
- [Mockery](https://github.com/mockery/mockery)

---

## 🧑‍💻 Contributing

We welcome contributions from developers of all skill levels! Here's how to get involved:

1. Fork the repo
2. Create a feature branch (`git checkout -b my-feature`)
3. Make your changes and add tests
4. Run tests locally
5. Commit and push your code
6. Open a pull request with a description of your changes

Report bugs or request features via [GitHub Issues](https://github.com/WPDiggerStudio/wpjarvis-core/issues)

Style guide:
- Follow PSR-4 autoloading
- Stick to Laravel-style service & folder conventions
- Document public methods with PHPDoc

---

## 📜 License

Released under the MIT License.  
MIT © [UPDigger & UPRootApps Team](https://uprootapps.com)

---

## 🧭 Next Steps

Ready to build your first plugin?

- 🚀 Install the WPJarvis scaffold tool: [WPDiggerStudio/WPJarvis](https://github.com/WPDiggerStudio/WPJarvis)
- 📚 Explore the [Documentation Wiki](https://github.com/WPDiggerStudio/wpjarvis-core/wiki)
- 🧩 Try generating a plugin skeleton using `wp jarvis:make-plugin`

Happy coding!


---

## 🙏 Support & Donations

If this framework helps speed up your workflow or adds value to your project, consider supporting continued development.

💖 You can donate via **PayPal**: [diggerwp@gmail.com](mailto:diggerwp@gmail.com)  
Every bit helps us keep improving the project and delivering updates faster. Thank you!


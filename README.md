# Spindle CMS

[![License: GPLv3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0.2-8892bf.svg?logo=php)](https://www.php.net/releases/8.0/en.php)
[![Build Status](https://img.shields.io/badge/build-manual-lightgrey.svg)](https://github.com/RandomCoderTinker/Spindle)
[![Project Status: WIP](https://img.shields.io/badge/status-WIP-orange.svg)](https://github.com/RandomCoderTinker/Spindle)
[![Open Source Love](https://img.shields.io/badge/Open%20Source-%E2%9D%A4-red.svg)](https://github.com/RandomCoderTinker/Spindle)

<p align="center">
  <img src="https://raw.githubusercontent.com/RandomCoderTinker/Spindle/refs/heads/master/public_html/cdn/images/logo_spindle.webp" alt="Spindle CMS Logo" width="180" />
</p>

**Spindle** is a clean, developer-first CMS framework rebuilt from the architectural bones of OpenCart — but with the
ecommerce layer surgically removed.  
It’s what OpenCart might have become if it had focused on modular content management instead of checkout flows and
product SKUs.

> ⌛ *Lightweight. Familiar. No cart required.*

---

## What Makes Spindle Different?

- **No eCommerce bloat** — no carts, no payments, no inventory logic
- **Dynamic subdomain routing** — serve multi-site content from folders
- **Shared folders and models** — Use a shared folder for subdomains and other content
- **Familiar MVC structure** — but focused purely on content-first deployments
- **No Composer required** — works out-of-the-box without external dependencies
- **Full codebase access** — nothing obfuscated, everything hackable

---

## ✨ Features

- Lightweight, override-friendly MVC architecture
- Secure directory separation (storage outside webroot)
- Modular extensions (`/extensions/`) without hard bindings
- Dynamic subdomain mapping (e.g. blog.example.com → `/subDomains/blog/`)
- Optional Composer support, bundled in `storage/vendor/`
- No JS frameworks, no React, no headless nonsense

---

## Installation

```bash
git clone https://github.com/RandomCoderTinker/Spindle.git
````

Set your document root to:

```
Spindle/public_html/
```

Ensure PHP 8.0+ is installed, and set write permissions on:

```
/storage/
/storage/logs/
/storage/cache/
/cdn/images/
```

You may optionally run:

```bash
composer install
```

…but it’s not required. All needed libraries are pre-bundled.

---

## Directory Layout

* `public_html/` – public web root
* `public_html/app` – Main website
* `public_html/cdn` – CDN folder for faster load speeds (Set up with VHOST or cloudflare caching)
* `public_html/data` – Manifest data and images (if required)
* `public_html/subDomains` – folder-based routing for subdomains
* `public_html/shared` – Shared utilities (Models, Views, Controllers)
* `public_html/system` – All system components
* `storage/` – configs, logs, cache (outside web root)

---

## Why Spindle?

> "OpenCart Without the Cart."

Spindle was designed for devs who liked the *simplicity* of OpenCart's architecture… but hated all the ecommerce
baggage. This is a toolkit for making dashboards, wikis, documentation hubs, and admin UIs — not stores.

---

## Roadmap

* [x] Rewrite base routing and MVC to remove cart logic
* [x] Subdomain-to-folder routing via `.htaccess`
* [x] Directory security separation
* [ ] Everything else
* [ ] Example usages
* [ ] Future updates

---

## License

GPLv3 — free to use, fork, modify, and redistribute, would be MIT, but have to stick with opencart's license.

> Spindle is yours now. Hack it.

---

## Credits

Originally based on [OpenCart](https://github.com/opencart/opencart)
Refactored, rebuilt, and unshackled by [@RandomCoderTinker](https://github.com/RandomCoderTinker)

---
Built with ❤️ using PHP 8.1+
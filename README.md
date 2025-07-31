# Spindle CMS

[![License: GPLv3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0.2-8892bf.svg?logo=php)](https://www.php.net/releases/8.0/en.php)
[![Build Status](https://img.shields.io/badge/build-manual-lightgrey.svg)](https://github.com/RandomCoderTinker/Spindle)
[![Open Source Love](https://img.shields.io/badge/Open%20Source-%E2%9D%A4-red.svg)](https://github.com/RandomCoderTinker/Spindle)

**Spindle** is a lightweight, developer-friendly CMS framework derived from the core of OpenCart — redesigned for structured content management instead of e-commerce.  

It preserves the simplicity of OpenCart’s MVC architecture, but removes e-commerce logic entirely, focusing instead on speed, clarity, and developer-first modularity.

---

## Features

- **Lightweight MVC core** — no Composer installation required to get started  
- **Secure directory separation** — config, logs, and cache stored outside the webroot  
- **Modular and override-ready** — drop-in logic, admin modules, and extensions  
- **Developer-first** — clean structure, no abstract wrappers  
- **Composer-supported** — bundled, but not required  
- **No commerce** — no cart, checkout, product logic  
- **Open Source (GPLv3)** — modify, extend, redistribute freely

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/RandomCoderTinker/Spindle.git
   ```

2. Set your **webserver document root** to:

   ```
   Spindle/public_html/
   ```

3. Ensure PHP 8.0+ and required extensions are installed.

4. Set writable permissions on:

   * `storage/`
   * `storage/logs/`
   * `storage/cache/`
   * `cdn/images/`
   
5. (Optional) Run:

   ```bash
   composer install
   ```
   to install or update third-party libraries. All required libraries are already bundled in `storage/vendor/`.

---

## Requirements

* PHP 8.0.2 or higher
* Apache or Nginx (with rewrite support)
* MySQL or MariaDB
* Composer (optional)

---

## Documentation

See the [Wiki](https://github.com/RandomCoderTinker/Spindle/wiki) for:

* Folder structure & autoloading
* Module development & routing
* Theme override & template structure
* Admin extensions & permissions
* Deployment tips

---

## Philosophy

> **"Build on something clear. Deploy something lean."**

Spindle is for developers who want:

* Full control over the stack
* Simple override logic
* A CMS with *no* plugin lock-in or over-engineering
* Source-readable PHP, not abstracted SDKs

---

## Roadmap

* [x] Core rewrite of OpenCart for content-first use
* [x] Directory security separation (`storage/` outside webroot)
* [ ] CLI tooling for module & controller generation
* [ ] Starter site template packs
* [ ] Optional flat-file driver for no-DB deployments
* [ ] JSON-based API layer (experimental)

---

## Credits

Originally based on [OpenCart](https://github.com/opencart/opencart)
Major refactor and CMS transition by [@RandomCoderTinker](https://github.com/RandomCoderTinker)

---

## License

Licensed under [GPLv3](https://www.gnu.org/licenses/gpl-3.0.html)

> You are free to use, modify, and redistribute this software under the terms of the GNU General Public License v3.

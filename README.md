# OmniPrice - Pakistan's Unified Price Comparison Platform

**PriceOye · Daraz · Naheed**

A real-time price comparison web application that searches **PriceOye, Daraz, and Naheed** simultaneously and shows the best prices in one place.

![Laravel](https://img.shields.io/badge/Laravel-11-red) 
![PHP](https://img.shields.io/badge/PHP-8.2-blue) 
![Node.js](https://img.shields.io/badge/Node.js-18-green) 
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue)

## Project Overview

OmniPrice is a **Web Technologies Final Year Project** that solves the problem of manually checking prices across multiple Pakistani e-commerce websites. Users can search once and get unified results from **PriceOye, Daraz, and Naheed**, sorted from lowest to highest price.

## Key Features

- Real-time unified search across 3 major stores
- Results sorted by price (cheapest first)
- Store-wise filtering
- Discount percentage calculation
- Category browsing (Mobiles, Laptops, Earbuds, Perfumes, etc.)
- Persistent product storage in MySQL
- Responsive dark-themed UI with Bootstrap 5
- Static pages (About, Contact, Privacy)

## Technology Stack

| Layer              | Technology                          | Purpose |
|--------------------|-------------------------------------|--------|
| Backend            | Laravel 11 (PHP)                    | MVC Framework, Routing, Eloquent |
| PriceOye Scraper   | PHP + Guzzle                        | JSON API Integration |
| Daraz Scraper      | Node.js + Axios                     | AJAX Catalog API |
| Naheed Scraper     | PHP + Symfony DomCrawler            | HTML Parsing |
| Database           | MySQL + Eloquent ORM                | Persistent Storage |
| Frontend           | Blade + Bootstrap 5                 | Responsive UI |

## Folder Structure (Important)

- `app/Http/Controllers/` → `SearchController`, `DarazController`, `NaheedController`, etc.
- `scraper/daraz_scraper.mjs` → Daraz scraping script
- `resources/views/` → All Blade templates
- `routes/web.php` & `routes/api.php` → Application routes

## How to Run Locally

```bash
# 1. Go to project folder
cd omni-price-tracker

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env file
# 5. Run migrations
php artisan migrate

# 6. Start server
php artisan serve

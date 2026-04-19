# 🍽️ Restaurant Reservation System (Laravel + Livewire + Multi-DB)

---

# 📌 Project Setup

## Tech Stack

* Laravel 12
* Tailwind CSS
* Livewire
* MySQL (Multi Database)
* Laravel Breeze (Auth)
* QR Code Package

---

# 🗄️ Database Architecture (IMPORTANT)

## 🔹 Database 1: Core System

`restaurant_core_db`

* users (admin + owner)
* restaurants
* menus (manual only)
* slots

## 🔹 Database 2: Customer System

`restaurant_customers_db`

* customers
* bookings

---

# ⚙️ Multi-DB Configuration

## .env

DB_DATABASE=restaurant_core_db

DB_CUSTOMER_DATABASE=customer_db

---

## database.php

Add connection:

```php
'customer_mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST'),
    'database' => env('DB_CUSTOMER_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
],
```

---

## Models

```php
class Customer extends Model {
    protected $connection = 'customer_mysql';
}

class Booking extends Model {
    protected $connection = 'customer_mysql';
}
```

---

# 🔁 COMPLETE SYSTEM FLOW

---

## 🏪 Restaurant Onboarding

1. Owner registers
2. Creates restaurant
3. Adds menu (manual)
4. Creates slots
5. Status = pending
6. Admin approves
7. Visible on frontend

---

## 🍴 Customer Booking Flow

1. Browse restaurants
2. View details + menu
3. Click Book Now

### Multi-Step Booking

* Select Date
* Select Slot
* Guests
* Customer Info

---

## Validation

```
if (total_guests < slot_capacity)
```

---

## Booking Creation

* Save in `customer_db.bookings`
* Store:

  * restaurant_id
  * slot_id

---

## QR + Email

* Generate QR (booking_id)
* Send email

---

## 🏪 Owner Booking Management

* View bookings
* Confirm / Reject
* Assign table

---

## 👤 Customer Dashboard

* View bookings
* Cancel booking

---

# 🧱 MODULES

## Core DB

* Users
* Restaurants
* Menus
* Slots

## Customer DB

* Customers
* Bookings

---

# ⚠️ Cross-DB Rule

❌ No direct relationships
✔ Use IDs manually

---

# 🚀 PHASE-WISE DEVELOPMENT

---

# 🧩 PHASE 0: INSTALLATION + MULTI-DB SETUP

## Prompt:

```
Setup Laravel 12 project with MySQL.

Tasks:
- Create 2 databases:
  1. restaurant_core_db
  2. restaurant_customers_db

- Configure multi-database connection in Laravel
- Update config/database.php
- Test both connections

- Install:
  Laravel Breeze (Livewire)
  Tailwind CSS

Ensure project runs successfully.
```

---

# 🧩 PHASE 1: AUTH + ROLES (CORE DB)

## Prompt:

```
Implement authentication using Laravel Breeze (Livewire).

Add roles:
- admin
- owner

Tasks:
- Add role column in users table
- Middleware for roles
- Redirect based on role

Ensure clean structure.
```

---

# 🧩 PHASE 2: RESTAURANT MODULE

## Prompt:

```
Build restaurant module.

Restaurant Owner can:
- sign up and login
- Create restaurant
-- multiple sections in this create restaurant form
- restaurant owner info
- restaurant info (name, status, category , description , )
- restaurant contacts section info (restaurant email , contact, social medial account links , etc any other needed field as per you)
- Restaurant hours info (day wise start time and end time fields)
- Restaurant Address info
- Restaurant images (logo image, cover image , certificates etc, gallery)
- add area (areaname (like rooftop , indoor, garden etc ), status )
- add tables (area, table code, seating capacity , etc..)


Default:
- status = pending


Use Livewire form with validation.
```

---

# 🧩 PHASE 3: ADMIN APPROVAL

## Prompt:

```
Create admin panel.

Features:
- View pending restaurants
- Approve / Reject


Update status in database.

Restrict access to admin only.
```

---

# 🧩 PHASE 4: MENU + SLOTS

## Prompt:

```
Build menu and slot system.

Menu:
- Manual only (no images)
- section, item_name, price, description

Slots:
- type (breakfast/lunch/dinner)
- start_time, end_time
- max_capacity

Owner can manage both.

Use Livewire components.
```

---

# 🧩 PHASE 5: CUSTOMER MODULE (SECOND DB)

## Prompt:

```
Create customer system using second database.

Tasks:
- Create customers table in customer_db
- Model with connection = customer_mysql
- Basic customer registration (separate from users)

Ensure data saves in customer_db.
```

---

# 🧩 PHASE 6: BOOKING SYSTEM

## Prompt:

```
Build booking system.

Requirements:
- Multi-step booking form (Livewire)
- Save bookings in customer_db

Fields:
- customer_id
- restaurant_id
- slot_id
- guests
- booking_date

Implement slot capacity validation.
```

---

# 🧩 PHASE 7: QR + EMAIL

## Prompt:

```
Integrate QR code and email.

Tasks:
- Install QR package
- Generate QR from booking_id
- Send email with booking details

Trigger after booking creation.
```

---

# 🧩 PHASE 8: OWNER BOOKING MANAGEMENT

## Prompt:

```
Build owner booking dashboard.

Features:
- View bookings
- Confirm / Reject

Fetch:
- Booking from customer_db
- Restaurant from core_db

Handle data manually (no relationships).
```

---

# 🧩 PHASE 9: CUSTOMER DASHBOARD

## Prompt:

```
Build customer dashboard.

Features:
- View bookings
- Cancel booking

Fetch bookings from customer_db.
```

---

# 🧪 TESTING FLOW

After each phase:

* Test database entries
* Test UI
* Test booking flow

---

# 🎯 FINAL MVP

✔ Multi-DB working
✔ Owner onboarding
✔ Admin approval
✔ Booking system
✔ QR email
✔ Owner booking management

---

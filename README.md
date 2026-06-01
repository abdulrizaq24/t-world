# T-World

An e-commerce website for selling T-shirts.

## Stack

- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Laragon Setup

1. Start Laragon.
2. Start Apache and MySQL from Laragon.
3. Put this project in Laragon's web root, usually:

```text
C:\laragon\www\t-world
```

4. Import the database using Laragon Terminal:

```sql
mysql -uroot < database/schema.sql
```

Or run:

```text
database\import-laragon.bat
```

5. Check your database credentials in:

```text
config/database.php
```

Default settings:

```text
Database: t_world
User: root
Password: empty
Host: 127.0.0.1
```

6. Open the PHP app:

```text
http://localhost/t-world/index.php
```

If you use Laragon auto virtual hosts, it may also be:

```text
http://t-world.test/index.php
```

## Demo Admin

The schema creates a demo admin account:

```text
Email: admin@t-world.test
Password: password
```

## Main PHP Pages

```text
index.php
pages/shop.php
pages/product_details.php
pages/cart.php
pages/checkout.php
auth/login.php
auth/register.php
admin/dashboard.php
admin/product_form.php
```

The older `.html` files are static prototypes from the first build phase. Use the `.php` pages for the database-backed version.

## Backend Features

- Product catalog from MySQL
- Product details from MySQL
- Session cart
- Checkout creates orders and order items
- Stock reduces after checkout
- Customer registration and login
- Admin dashboard
- Admin create/edit/hide products

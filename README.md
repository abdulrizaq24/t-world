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
Password: Malicha@123
```

## Main PHP Pages

```text
index.php
pages/shop.php
pages/product_details.php
pages/cart.php
pages/checkout.php
pages/contact.php
pages/shipping.php
pages/returns.php
pages/size_guide.php
auth/login.php
auth/register.php
account/profile.php
account/orders.php
account/order_details.php
admin/dashboard.php
admin/product_form.php
admin/orders.php
admin/order_details.php
admin/returns.php
admin/return_details.php
```

The older `.html` files are static prototypes from the first build phase. Use the `.php` pages for the database-backed version.

## Backend Features

- Product catalog from MySQL
- Product details from MySQL
- Support pages for contact, shipping, returns, and size guide
- Session cart
- Checkout creates orders and order items
- Checkout pre-fills saved customer details
- Stock reduces after checkout
- Customer registration, login, profile editing, saved shipping details, and stronger form validation
- Customer order history, order details, shipping progress, return requests, and status summaries
- Admin dashboard with product search/category/status/low-stock filters
- Admin create/edit/hide/delete products with low-stock warnings and order-history protection
- Admin view/update order statuses with order search/status filters and pagination
- Admin view/update return requests with internal notes
- CSRF protection for admin, customer, cart, checkout, login, and registration POST forms
- Admin customer list and detail pages with search, pagination, order counts, and total spent


## Product Image Uploads

Admin product images are uploaded to:

```text
uploads/products
```
Allowed image types: JPG, PNG, WEBP. Maximum size: 2MB.

When an uploaded product image is replaced or a safe-deleted product is removed, old files inside uploads/products are cleaned up automatically. Seeded images inside images are not deleted.


## Image Mapping

Current site images are mapped like this:

```text
images/hero-shirt-placeholder.jpg  <- heroimage.jpg
images/category-plain.jpg          <- plain t-shirts brown.jpg
images/category-oversized.jpg      <- oversized2.jpg
images/category-graphic.jpg        <- graphic t-shirts.jpg
images/category-new.jpg            <- newarrivals1.jpg
images/product-1.jpg               <- productimg1.jpg
images/product-2.jpg               <- productimg2.jpg
images/product-3.jpg               <- productimg3.jpg
images/product-4.jpg               <- productimg4.jpg
images/product-5.jpg               <- productimg5.jpg
images/product-6.jpg               <- productimg6.jpg
```

The original uploaded files are kept in `images` as backups.

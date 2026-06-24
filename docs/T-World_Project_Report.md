# T-World E-Commerce Website Project Report

## Title Page

Project Title: T-World E-Commerce Website

Project Type: Database-backed e-commerce website for selling T-shirts

Technologies Used: HTML, CSS, JavaScript, PHP, MySQL, Laragon

Backend Type: PHP server-side application

Database: MySQL database named t_world

Prepared For: Project documentation and presentation

Prepared By: Abdul Rizaq

Date: June 2026

## Abstract

T-World is a web-based e-commerce platform designed for selling T-shirts online. The project provides a complete shopping experience where customers can browse products, view product details, add products to a cart, checkout, create accounts, view order history, track shipping progress, and request returns. The system also includes an admin dashboard where the store owner can manage products, orders, customers, and return requests.

The project was developed using HTML, CSS, JavaScript, PHP, and MySQL. PHP handles backend logic such as authentication, cart actions, checkout, order processing, product management, and admin actions. MySQL stores persistent data including users, products, orders, order items, and return requests. Laragon was used as the local development environment for running Apache, PHP, and MySQL.

The main goal of T-World is to demonstrate how a practical e-commerce website works from frontend design to backend processing and database storage. The project also includes security features such as password hashing, session-based login, role-based access control, CSRF protection, database privacy cleanup for GitHub, and restricted admin pages.

## 1. Introduction

E-commerce websites are important because they allow businesses to sell products online and allow customers to shop without visiting a physical store. T-World is an e-commerce website focused on T-shirts. The project was created to understand how online stores are built and how different parts of a web application work together.

The website is not only a static design. It has a working backend and database. Customers can register, log in, add products to a cart, place orders, and track their orders. The admin can manage the store by adding products, uploading images, updating order statuses, viewing customers, deleting customers, and processing returns.

The project was built step by step, starting from the home page and navigation, then moving into product pages, cart, checkout, authentication, admin features, database integration, and final improvements such as password visibility toggles, shareable tunnel links, and project diagrams.

## 2. Project Objectives

The main objectives of the T-World project are:

- To build a complete T-shirt e-commerce website.
- To create a clean frontend using HTML, CSS, and JavaScript.
- To use PHP as the backend language.
- To use MySQL for storing product, customer, order, and return data.
- To allow customers to register and log in.
- To allow customers to add products to a cart and place orders.
- To allow customers to view order status and request returns.
- To allow admins to manage products, orders, customers, and returns.
- To support product image uploads.
- To protect important forms with CSRF tokens.
- To hide database and customer-sensitive files from GitHub.
- To provide a project that can be tested locally using Laragon.

## 3. Project Scope

The scope of the project includes the main features expected in a small online store. Customers can browse products, add products to the cart, checkout, and view account information. Admins can manage the data behind the store.

The project includes customer-facing pages such as the home page, shop page, product details page, cart page, checkout page, support pages, login page, register page, profile page, orders page, and order details page.

The project also includes admin-facing pages such as the dashboard, product form, orders list, order details, customers list, customer details, returns list, and return details.

The project does not include real online payment processing yet. Checkout creates orders in the database, but it does not charge a card or connect to a payment gateway. This can be added in future work using services like Stripe or PayPal.

## 4. Technology Stack

### Frontend

The frontend is built using HTML, CSS, and JavaScript. HTML provides the structure of each page. CSS controls the visual design, layout, responsiveness, spacing, typography, buttons, forms, cards, tables, and admin dashboard style. JavaScript is used for small interactive features such as toggling password visibility.

### Backend

The backend is built with PHP. PHP processes forms, connects to the database, controls sessions, checks login status, protects admin pages, handles cart actions, creates orders, updates product stock, manages products, processes returns, and handles customer deletion.

### Database

The database is MySQL. It stores data in structured tables such as users, products, orders, order_items, and return_requests. MySQL allows the website to keep data even after the browser is closed.

### Local Development Environment

Laragon is used to run the project locally. Laragon provides Apache, PHP, and MySQL in one local environment. The project runs from the Laragon web root, usually C:\laragon\www\t-world.

## 5. System Architecture

The system follows a traditional server-rendered web application structure. The user interacts with PHP pages in the browser. When the user submits a form, the request is sent to a PHP action file. The PHP file validates the request, communicates with MySQL, updates the session if needed, and redirects the user to the correct page.

The main flow is:

Browser -> PHP Page -> PHP Action -> MySQL Database -> Redirect/Render Page -> Browser

For example, when a customer adds a T-shirt to the cart, the product page submits a form to actions/add_to_cart.php. That action checks the CSRF token, validates the product, stores the cart item in the PHP session, and redirects the user to the cart page.

When the customer places an order, actions/place_order.php validates checkout details, checks stock, creates an order in the orders table, creates rows in the order_items table, reduces product stock, clears the session cart, and redirects to the checkout success page.

## 6. Database Design

The database is named t_world. It contains the following main tables:

### users

The users table stores registered customers and admins. Important fields include id, name, email, password_hash, role, phone, address, city, postal_code, and created_at. Passwords are not stored as plain text. They are stored as hashed passwords using PHP password hashing.

### products

The products table stores T-shirt product information. Important fields include id, name, category, description, price, stock, image_url, is_active, and created_at. The stock field is reduced when customers place orders.

### orders

The orders table stores order-level information. Important fields include id, user_id, customer_name, email, phone, address, city, postal_code, subtotal, shipping, total, status, and created_at. The order status can be pending, processing, shipped, delivered, or cancelled.

### order_items

The order_items table stores the individual products inside each order. It includes order_id, product_id, product_name, size, quantity, and price. This separates order information from item information and allows one order to contain multiple products.

### return_requests

The return_requests table stores customer return requests. It includes order_id, user_id, reason, status, admin_note, created_at, and updated_at. Return statuses include requested, approved, rejected, received, and refunded.

## 7. Frontend Design

The frontend design is clean and simple. The navigation bar includes the brand name, Home, Shop, Categories, search icon, cart icon, and login/account icon. The home page includes a hero section, featured products, product categories, and a promotional banner.

The shop page contains filters, search, product cards, and add-to-cart controls. Product detail pages include product images, description, price, size options, quantity input, stock information, and add-to-cart button.

The cart page displays selected items, quantities, prices, remove buttons, and an order summary. The checkout page collects contact information, shipping address, and payment method selection.

The account pages allow customers to edit profile information, view orders, check shipping progress, and request returns. Admin pages use tables, filters, forms, status badges, and dashboard cards to make store management easier.

The website uses responsive CSS so it can work on desktop and mobile screens. Mobile-friendly improvements include flexible grids, stacked layouts, adjusted spacing, and input controls designed for smaller screens.

## 8. Backend Functionality

The backend is divided into page files and action files. Page files display forms and data. Action files process POST requests. This separation makes the project easier to understand and maintain.

Important backend features include:

- Product loading from MySQL.
- Session-based cart.
- Cart add, update, and remove actions.
- Checkout processing.
- Stock reduction after checkout.
- Customer registration and login.
- Password hashing and password verification.
- Admin-only access control.
- Product create, edit, hide, and delete.
- Product image upload.
- Order status updates.
- Customer list with registered and guest customers.
- Customer deletion.
- Return request creation.
- Return status updates.
- CSRF protection for POST forms.

## 9. Customer Features

Customers can browse the shop, view product details, add products to the cart, and checkout. Customers can also create accounts and log in. Logged-in customers can view their profile and order history.

The order details page shows shipping information, order status, ordered items, and a shipping progress tracker. Once an order has shipped or been delivered, the customer can submit a return request with a reason.

The password fields on login and registration pages include an eye icon that allows users to show or hide the password they are typing.

## 10. Admin Features

The admin dashboard allows the store owner to control the website. Admins can view summary statistics, manage products, manage orders, manage customers, and manage returns.

Product management includes creating products, editing product details, uploading product images, hiding inactive products, and deleting products when safe. Order management includes viewing all orders, filtering by status, searching orders, and updating order status directly from the orders list.

Customer management includes registered customers and guest checkout customers. Admins can view customer details, view customer orders, and delete customers. Return management allows admins to view return requests, update return status, and add internal notes.

## 11. Security Features

The project includes several security features. Passwords are stored as hashes instead of plain text. Sessions are used to manage logged-in users. Admin pages are protected using require_admin(), while customer pages are protected using require_customer().

CSRF tokens are used to protect important POST forms such as cart actions, checkout, product management, order status updates, customer deletion, return requests, and return status updates.

The project also includes Git privacy cleanup. Database credentials, database exports, uploaded product files, logs, and local tunnel tools are ignored using .gitignore so private data is not uploaded to GitHub.

## 12. Testing

The project was tested locally using Laragon. PHP syntax checks were run using php -l to confirm that PHP files did not contain syntax errors. Important flows were tested using local HTTP requests, including login, registration, add to cart, checkout page access, admin order status updates, customer listing, customer deletion, and Cloudflare Tunnel public access.

The website was also tested using temporary Cloudflare Tunnel links so it could be opened from other devices. This confirmed that the site could be shared temporarily, although Cloudflare quick tunnel links are not permanent.

## 13. Deployment and Sharing

The project currently runs locally using Laragon. It can be shared temporarily using Cloudflare Tunnel. A tunnel creates a temporary public URL that forwards traffic to the local Laragon site. This is useful for demonstrations, but it is not a permanent hosting solution.

For a real public website, the project should be deployed to PHP/MySQL hosting. Suitable hosting options include cPanel hosting, InfinityFree for testing, Hostinger, Namecheap hosting, or a VPS. The database should be imported into the hosting MySQL server, and config/database.php should be updated with the live database credentials.

GitHub can be used to store the source code, but GitHub Pages cannot run PHP or MySQL. Therefore, GitHub Pages is not suitable for the full working version of this website.

## 14. Limitations

The project does not yet include real payment integration. The checkout page creates an order but does not process card payments. The project also does not send email notifications to customers or admins. Another limitation is that Cloudflare Tunnel links are temporary and can change when the tunnel restarts.

The admin dashboard has useful management features, but it could be improved with more analytics such as revenue charts, best-selling products, low-stock reports, and monthly sales summaries.

## 15. Future Improvements

Future improvements could include:

- Stripe or PayPal payment integration.
- Email confirmation after checkout.
- Admin email alerts for new orders.
- Better sales reports and charts.
- Product reviews and ratings.
- Wishlist feature.
- Discount codes and coupons.
- More advanced shipping tracking.
- Better return workflow with uploaded return photos.
- Permanent deployment to a real hosting provider.
- More secure production configuration.

## 16. Conclusion

T-World is a complete PHP and MySQL e-commerce website for selling T-shirts. It includes customer-facing features, admin management features, database integration, image uploads, order tracking, return requests, and security protections. The project demonstrates how a real online store works from frontend layout to backend processing and database storage.

The project is suitable as a learning and demonstration project because it covers many important web development concepts, including forms, sessions, authentication, database relationships, CRUD operations, admin dashboards, file uploads, and deployment preparation.

Overall, T-World successfully meets its goal of providing a functional e-commerce platform while also serving as a practical example of full-stack web development using HTML, CSS, JavaScript, PHP, and MySQL.

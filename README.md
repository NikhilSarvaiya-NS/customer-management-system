CUSTOMER MANAGEMENT SYSTEM


HOW TO RUN
===========
1. Install XAMPP → start Apache + MySQL
2. Copy "customer_management" folder to: C:\xampp\htdocs\
3. Open phpMyAdmin → SQL tab → paste database.sql → Click Go
4. Open browser: http://localhost/customer_management/

LOGIN CREDENTIALS
==================
Admin : username: admin    | password: admin123
User  : username: nikhil   | password: nikhil123

PROJECT FILES
==============
login.php          → Login page
logout.php         → Logout (destroys session)
dashboard.php      → Dashboard (counts, chart, recent data)
index.php          → Customer Management (Add/Edit/Delete)
quotation.php      → Create Quotation
quotation_list.php → Quotation History
get_customer.php   → AJAX customer fetch
pdf_quotation.php  → PDF/Print Quotation
database.sql       → Run this in phpMyAdmin
includes/db.php    → DB connection + session + auth
includes/navbar.php→ Navigation bar
css/style.css      → Styles
js/quotation.js    → Quotation JS

FEATURES
=========
✅ Login System (username + password)
✅ Show/Hide password toggle
✅ Session management (auto redirect if not logged in)
✅ Dashboard with 4 stat cards
✅ Monthly quotations bar chart
✅ Recent customers table on dashboard
✅ Recent quotations table on dashboard
✅ Quick action buttons on dashboard
✅ Customer Add / Edit / Delete
✅ Photo upload for contact person
✅ Customer Quotation with auto-fetch
✅ Dynamic product rows with auto total
✅ PDF/Print quotation
✅ Quotation History page
✅ Admin can delete quotations
✅ Logout button
================================================

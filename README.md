Fusion Syndicate - Installation Manual
=======================================

Welcome to Fusion Syndicate, your online food ordering system!

This document will guide you through the installation process step-by-step.

Requirements
------------
- Web Server (e.g., XAMPP, WAMP, LAMP)
- PHP 7.0 or higher
- MySQL Database
- Web Browser (Google Chrome, Mozilla Firefox, etc.)

Installation Steps
-------------------

1. Install a Local Server:
   - Download and install XAMPP from this link: 
     https://www.apachefriends.org/index.html

2. Setup the Project Files:
   - Copy the 'FusionSyndicate' project folder into the `htdocs` directory (for XAMPP users).

3. Configure the Database:
   - Start Apache and MySQL from the XAMPP control panel.
   - Open your browser and go to: `http://localhost/phpmyadmin`
   - Create a new database named: `fusion_syndicate`
   - Import the provided SQL file (`fusion_syndicate.sql`) into the new database.

4. Update Database Configuration:
   - Open the project folder.
   - Locate the database connection file (example: `db_connect.php`).
   - Make sure the database settings are correct:
     - Host: `localhost`
     - User: `root`
     - Password: *(leave empty if using XAMPP default settings)*
     - Database Name: `fusion_syndicate`

5. Launch the Application:
   - In your browser, go to: `http://localhost/FusionSyndicate`
   - The Fusion Syndicate homepage should appear.

Troubleshooting
---------------
- Make sure Apache and MySQL services are running.
- Check that the database name and credentials are correct.
- Clear browser cache if changes do not appear.
- Check PHP error logs if you see a blank page.

Credits
-------
Developed by: Amirul,Magdalene,Adden

Thank you for using Fusion Syndicate!
Enjoy a smooth food ordering experience.

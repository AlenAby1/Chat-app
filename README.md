# ChatApp (PHP + MySQL)

Features: register / login, public group chat, private one-to-one chat, online dots,
messages refresh every 2 seconds, mobile friendly.

## Run it with XAMPP
1. Copy this folder into `C:\xampp\htdocs\` and name it `chatapp`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open http://localhost/phpmyadmin, click **Import**, choose `schema.sql`, click **Go**.
4. If your MySQL has a password, edit `config.php`.
5. Open http://localhost/chatapp/ and register two users in two different browsers to try it.

## Files
- `schema.sql` database and tables
- `config.php` database settings
- `db.php` connection, sessions, CSRF, helpers
- `register.php`, `login.php`, `logout.php`, `index.php` pages
- `api/send.php`, `api/fetch.php`, `api/users.php` JSON endpoints
- `assets/chat.js`, `assets/style.css` front end

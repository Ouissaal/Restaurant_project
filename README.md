# Restaurant Web Application

A user-friendly online platform to browse meals, manage orders, and streamline restaurant interactions — built with security, responsiveness, and user experience in mind.

---

## Tech Stack

- **PHP** (using PDO for secure database interactions)  
- **SQL** (or compatible relational database)  
- **HTML5 + Bootstrap 5** for responsive UI design  
- **Sessions and Cookies** for user state management and personalized experience  

---

## Features

- User registration and login system (`create_account.php`, `connexion.php`)  
- Secure session management (`connexiondata.php`, `déconnexion.php`)  
- Browse meals and view details (`accueil.php`, `view_details.php`)  
- Manage food orders (add to cart, update, remove items) (`panier.php`, `mettre_a_jour_panier.php`, `supprimer_du_panier.php`, `vider_panier.php`)  
- Place and track orders (`passer_commande.php`, `commande_success.php`, `mes_commandes.php`)  
- User profile management (`mon_compte.php`, `modification_profil.php`)  
- Contact form for customer support or inquiries (`contact.php`)  
- Responsive design with Bootstrap 5, including light and dark modes  

---

##  Security
- Uses **PDO** with prepared statements to prevent SQL injection  
- User passwords are securely hashed before storage  
- Session management to ensure secure login states  
- Proper input validation on all forms  



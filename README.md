*Create the database:
Ensure your database is set up and configured.

*Create the .env file:


Copy the .env.example file and rename it to .env.

APP_USER_EMAIL="admin@ccs.com"
APP_USER_PASSWORD="password"
APP_SUPERADMIN_ROLE="Super Admin"
Update the database and other environment configurations in the .env file.

*Install Composer dependencies:
composer install
--php artisan key:generate
*Install NPM dependencies:
npm install
*Run database migrations:
php artisan migrate:fresh --seed
*Ziggy Routes If you create or modify a route, run the following command to update Ziggy routes:
php artisan ziggy:generate
*Start the development server:
php artisan serve
npm run dev
composer dump-autoload
Component Naming Convention
Component Names: Should be written in PascalCase (example : DetailContainer.vue).
Folder Names: Should be in lowercase (example : home). (Example:./container/Container.vue)
php artisan app:courses:unlock 
php artisan app:update-course-progress
php artisan schedule:work

## 🛠️ Installation Initiale

Pour installer toutes les dépendances et configurer le projet (première utilisation) :

### Sur Windows
Exécutez :
- **`setup.bat`**

### Via Terminal (Git Bash / WSL)
Exécutez :
```bash
./setup.sh
```

## 🚀 Démarrage Rapide du Développement

Pour lancer simultanément le backend, le frontend et la queue worker :

### Sur Windows (Recommandé)
Double-cliquez sur :
- **`dev.bat`**

### Via Terminal (Git Bash / WSL / Linux)
Exécutez :
```bash
./start.sh
```
*Note : Utilisez `Ctrl+C` pour arrêter tous les processus.*

const transporter = nodemailer.createTransport({
    host: 'smtp.ethereal.email',
    port: 587,
    auth: {
        user: 'marlene80@ethereal.email',
        pass: 'uYA8dZNxzVWrmXUXtz'
    }
});
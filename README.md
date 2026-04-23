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

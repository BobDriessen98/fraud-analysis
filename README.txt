STEPS TO INSTALL

1. Install docker desktop
2. Install WSL
3. Enter the WSL CLI
4. Run >docker pull vzdeveloper/customers-api
5. Run >git clone https://github.com/BobDriessen98/fraud-analysis.git
6. In project root, rename .env.example to .env
7. Run the following commands:
	>cd fraud-analysis
	>docker compose up -d --build
	>docker compose exec app composer install
	>docker compose exec app php artisan key:generate
	>docker compose exec app php artisan migrate
		Select "yes" when asked to create a sqlite database
	>sudo chmod -R 777 .
	>docker compose exec app php artisan db:seed
8. Start the external API docker application by running >docker run -p 8080:80 vzdeveloper/customers-api:latest
9. Navigate to localhost/scans/index, application should show the index page.

AVAILABLE ROUTES

Web routes:
http://localhost/scans/index > lists all scans
http://localhost/scans/latest > shows latest scan from cache
http://localhost/scans/:id > fetch a specific scan and shows this scan

API routes:
http://localhost/api/scans/index > shows a list of all scans, with all customers belonging to that scan and the fraud reasons for each customer (if they have any)
http://localhost/scans/:id > shows a specific scan

If using PHPStorm and you want to run feature test:
1. Go to file>settings>PHP
2. Click the dots next to CLI interpreter
3. Click the + and then "from docker..."
4. Click "docker compose" and in service select "app"
5. Click "apply"
6. Back in the settings menu, under PHP, click "test frameworks"
7. Click + and then "pest by remote interpreter"
8. Path to executable should be: /var/www/html/vendor/pestphp/pest/bin/pest
9. Click apply
10. You should be able to run feature tests now.

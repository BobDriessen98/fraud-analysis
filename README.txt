STEPS TO INSTALL

1. Install docker desktop
2. Install WSL
3. Enter the WSL CLI
4. Run >docker pull vzdeveloper/customers-api
5. Run >git clone https://github.com/BobDriessen98/fraud-analysis.git
6. In fraud-analyis project root, rename .env.example to .env
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
http://localhost/api/scans/:id > shows a specific scan

If using PHPStorm and you want to run feature tests:
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


-------------------------------------------
What did I implement?
-------------------------------------------
In this application, I implemented two pages; one that shows a list of fraud scans that have been completed, and another page that shows the details of each scan. When the user runs a fraud scan, it fetches a list of customers from an external API, stores these customers in the database by creating new CustomerModels (or updates existing CustomerModels if the customer ID already exists in the DB). It also creates a new Scan model, and sets the "occurred_at" column for that scan to the current time. It then runs several functions that check for fraudulent activity. If fraud is found, it attaches a FraudReason model to this customer (many-to-many relationship). This relationship has a pivot column called "context" where more details are given about why the customer is fraudulent. All customers that are imported through a scan are attached to the scan through a many to many relation. Through the interface the user can select a specific scan (this will fetch a scan by ID) or select the latest scan (this will fetch the latest scan from the cache). In case the external API request fails, an error message is shown saying that an error occurred.
The scan details page shows a list of all customers that were imported in that scan, highlights fraudulent customers and shows one or more fraud reasons if the customer is fraudulent. I created a feature tests that tests the most important flow of the application (fetching customers from the API, creating scan and customer models, running the fraud scan over these customers, storing scan in cache and returning the scan).


-------------------------------------------
Why did I implement or skip certain things?
-------------------------------------------

I chose to keep logic divided in the correct layers as much as possible. Database manipulations/fetch actions are located in repository classes. API requests to the external application are done in a specific external service. Regular business logic is located in an internal service. Controllers are kept thin; they only receive a request and call either an internal service or a repository to fetch data. 

I created a many-to-many relation for customer_scans because I saw that the API already provides a customer ID. In the case that a customer already exists with a specific ID, I don't want that ID to become duplicate in my database by having two customers with that ID. I chose to implement "updateOrCreate" to store the customers for this reason.

I also created a many-to-many relation for customer_fraud_reasons so I could attach multiple fraud reasons to customers and keep track of the reason why a customer was fraudulent in a clean way.

To keep the controllers organized, I made one controller that handles showing views, and another that handles API requests.

API requests return resource classes so the data can get formatted nicely and relations can get fetched for each model in a clean way

I chose to containerize the application and use SQLite to make it easy to set up and use the application.

-------------------------------------------
What trade-offs did I make?
-------------------------------------------
The way I currently keep track of why a customer was fraudulent is not the best solution; I store the reason in a specific column "context" in the pivot table. Essentially this is duplicate/unnecessary data since what I store in this column can also be fetched through a query. If data gets updated later on the data in this column becomes outdated. Ideally I'd create a function that determines why a customer was fraudulent when fetching the customer from the database.

Right now I only compare the customers that are fetched in the same API call for fraudulent activity. Ideally you'd also compare these to the existing customers in the database. I chose not to do this to keep the data in the tables on the short side; each request fetches 100 customeres and the tables would get large very quickly if I also had to show old customers that became fraudulent again in this scan. 

The API routes reside in the web.php file. Normally I'd put these in the api.php file, but for some reason the routes could not be found when they were defined in this file. I did not want to waste too much time on this so for this demo I just put the routes in the web.php file, which also works fine.

I only created one feature test without too many edge cases. If I had more time I'd test more features and would also write some unit tests since the logic is separated nicely which makes the code good for unit testing. 

When storing customers using the updateOrCreate function, I manually map keys from the external API array to a database column. This is not really maintainable. I did this because the data from the API comes in as camelCase while the DB expects snake_case. Otherwise I could just pass in the data from the validator object to store the data. I chose to keep it like this because it works fine and did not want to waste time on creating a conversion method to make the imported data snake_case.

------------------------------------------------
If I had more time, what would I improve or add?
------------------------------------------------
Improve the way the reason why a customer is fraudulent is fetched. I'd not store the data in the context column like I do now but create a function/relationship query that determines the reason when fetching the customer.

Create a view that shows all customers, with a filter function to only show the fraudulent ones. I' also add filter options to only see customers with (a) specific fraud reason(s)

I'd expand the feature/unit tests so I also test the controllers/repositories.

I'd create an end-to-end test so the UI is also tested.

As said above, improve the way I use the updateOrCreate function by creating a middleware function that converts camelCase request arrays to snake_case

Improve error handling by creating an error page that shows more detail about an error if the application crashes

Implement basic auth features 


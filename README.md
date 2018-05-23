# iBus Job Scraper

## Objective

Write PHP scraper which:
- parses iBus job offers [page](http://www.ibusmedia.com/career.htm);
- stores parsed data in JSON format valid with `schema.json`;
- has an API endpoint which returns scraped data in JSON;
- has a CLI task which validates JSON with `schema.json`;

## Installation / Running

### Installation

- Clone git repository 

   `git clone https://github.com/appstu/ibusjobscraper`
   
- Make ./var sub-directories writable to your user and web user (and cron user if necessary) 
   
- Run composer to install/update vendor packages in the root direcotry of the project
   
   `composer update`

- Test installation by running app console in the root folder

   `php bin/console`
   
   You should see the following two commands in the list:
   
   ```
   app
        app:scrape-jobs                         Scrapes jobs.
        app:validate-json                       Validates scraped JSON.
   ```

- If you need to change the job URL or the storage for scraped data - set it as parameters in config.yml or params.yml
```
parameters:
    jobs_url: http://www.ibusmedia.com/career.htm
    jobs_storage: '%kernel.project_dir%/var/storage'
```

- For API endpoint to work you need to configure the site on your web server of choice
   & set the document root to %kernel.project_dir%/web/


### Running 

Scraper can be run as 
- standalone
    `php bin/console app:scrape-jobs`
- or by adding the same command line to cron jobs
 
Validator can be run by
    `php bin/console app:validate-json`
    
### API     

If set up correctly the API endpoint returning the full scraped data
is available:

    `%base_url%/api/jobs/list`
    
It returns JSON array of objects:

     ```
     [
        {
            "title":"Business Development Manager",
            "location":"Barcelona, Spain",
            "apply_link":"https:\/\/ibusmedialimited.peoplehr.net\/Pages\/JobBoard\/Opening.aspx?v=e30da088-71b9-4598-9680-4644f6abf664",
            "date":"20 February 2018",
            "description":"We are looking for ... Competitive salary."
        },
     ...
     ]
     ```

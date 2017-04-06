# Klandr-o-mat
A website for remembering impudence.

Currently hosted on `http://holdetstime.com`

## Install
Install comes in two categories `Deployment` if you are hosting the klandr-o-mat yourself, and development if you are thinking of contributing changes.
### Deployment
1. fill out `config.personal-blank.php` and rename it to `config.personal.php`
2. Create a MySQL database and run `schema.sql` on it.
3. Fill out the database with the appropiate data.
4. Copy contents of source to the desired webserver.

### Development
The following steps are really just a personal prefernce, you might want to user XAMP or some other commands.

1. fill out `config.personal-blank.php` and rename it to `config.personal.php`
    - set MYSQL_PROVIDER to `localhost:3306`, `MYSQL_USER` to `root` or what over setup you are going with.
2. Run MySQL server by running `>mysqld --console`. If mysqld is not initialized yet follow [this](https://dev.mysql.com/doc/refman/5.7/en/data-directory-initialization-mysqld.html).
3. Create a MySQL database and run `schema.sql` on it (from the MySQL cli: `"\. schema.sql"`).
4. Run `test-data.sql` on the database (Not made yet but will be)
5. Run `>mysql -u root` then `\u <database name>` then the following:
```sql 
INSERT INTO student (auid, name) VALUES ('<YOUR AUID>', '<YOUR NAME HERE>');
```
6. Run PHP 
    - Windows: Use CMD `>php.exe -S localhost:1337 -t source` (we are running 5.6.30, but you might want to try a newer version like 7)
7. Open browser and go to `http://localhost:1337/`

Second time around, you need only do step 2, 6, and 7. (You could create a .bat/sh file that does this)

## Contributing
Any contribution is greatly appricated, be it issue or pull request.

See [ROADMAP](ROADMAP.md) for inspiration.

____________________________________________________________
License: [AGPL](LICENSE)
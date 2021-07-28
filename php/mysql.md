# Mysql

## Check new version
https://www.apachefriends.org/download.html

For multilanguage set `utf8_unicode_ci`

## Monitor
> SHOW PROCESSLIST

## Explain query
> EXPLAIN select * from users

- mysql automatically create index for foreign keys

## comma seperated column values
> SELECT GROUP_CONCAT(id)  FROM table_level where parent_id=4 group by parent_id;

## Star rating
> SELECT SUM(rating = 1) AS one_s, SUM(rating = 2) AS two_s FROM star_ratings

```php
// single DB query to get conditional counts
select
  count(*) as total,
  count(case when status = 'confirmed' then 1 end) as confirmed,
  count(case when status = 'unconfirmed' then 1 end) as unconfirmed,
from subscribers

$totals = DB::table('subscribers')
    ->selectRaw('count(*) as total')
    ->selectRaw("count(case when status = 'confirmed' then 1 end) as confirmed")
    ->selectRaw("count(case when status = 'unconfirmed' then 1 end) as unconfirmed")
    ->first();

// boolean column
$totals = DB::table('subscribers')
    ->selectRaw('count(*) as total')
    ->selectRaw('count(is_admin or null) as admins')
    ->first();

// PostgreSQL
$totals = DB::table('subscribers')
    ->selectRaw('count(*) as total')
    ->selectRaw('count(*) filter (where is_admin) as admins')
    ->first();
```

## foreign key
> alter table comments add foreign key (post_id) references posts(id)
  - on delete cascade
  - on delete restrict
  - on delete no action
  - on update cascade
  - set null
> alter table courses drop foreign key courses_sector_id_foreign

## Add/drop index
> ALTER TABLE courses ADD INDEX (user_id)
> ALTER TABLE courses DROP INDEX (user_id)

## primary/unique/foreign key

```php
ALTER TABLE `redirects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `redirects_old_url_unique` (`old_url`),
  ADD KEY `redirects_user_id_foreign` (`user_id`);

ALTER TABLE `redirects`
  ADD CONSTRAINT `redirects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;
```

## length
> CHAR_LENGTH('column')

## import to windows
C:/xampp/mysql/bin > mysql.exe -u root -p `database_name` < `path/file.sql`
example:
C:\xampp\mysql\bin>mysql.exe -u root -p stan < D:\Downloads\dev\guidely\candidates.sql

## change htdocs path
- location: `C:\xampp\apache\conf\httpd`
- changes: `DocumentRoot "D:/dev"` , `<Directory "D:/dev">`

## [Check Stable versions](https://www.php.net/downloads.php)
check by `php -v`

```php
PHP 7.4.5
PHP 7.3.17
PHP 7.2.30
```

## Storage Engines
- MySQL 5.5 => default is MyISAM
- after mysql 5.5 => default is InnoDB

1. InnoDB - Supports transactions, row-level locking, and foreign keys , full-text search
2. MyISAM - table level locking ,  full-text search
3. CSV
4. Blackhole

## Use older PHP versions
- go to this [link](https://sourceforge.net/projects/xampp/files/)
- choose xampp windows & choose required version: php v5.6
- download .zip file & extract
- copy new php folder & rename previous one & paste new php folder
- edit c:\xampp\php\php.ini & replace all \xampp\ to C:\xampp\
- copy new apache folder & rename previous one & paste new apache folder
- restart xampp

## Use multiple xampp
- go to this [link](https://sourceforge.net/projects/xampp/files/)
- choose xampp windows & choose required version: php v5.6
- download .exe file & install
- [change Apache port](#id)

## PHP Modules
Check available php extension modules by `php -m`

```php
[PHP Modules]
bcmath, bz2, calendar, Core, ctype, curl, date, dom, exif, fileinfo, filter,
ftp, gd, gettext, hash, iconv, json, libxml, mbstring, mysqli, mysqlnd, openssl,
pcre, PDO, pdo_mysql, pdo_sqlite, Phar, readline, Reflection, session, SimpleXML,
SPL, standard, tokenizer, xml, xmlreader, xmlwriter, zip, zlib
[Zend Modules]
```

## Install PHP extension at windows
Location: `C:\xampp\php\php.ini`
[mcrypt](https://sourceforge.net/projects/mcrypt/)

```php
;extension=php_mcrypt.dll
```

## Install PHP extension at ubuntu

```php
sudo apt-get update

apt-get install php-mcrypt
apt-get install php5-mcrypt
apt-get install php7.0-mcrypt

sudo apt-get upgrade
```

# Mysql

## Host
Location: `C:\Windows\System32\drivers\etc\hosts`

```php
127.0.0.1 			dev.doccure.test
```

## vHost
Location: C:/xampp/apache/conf/extra/httpd-vhosts.conf

```php
<VirtualHost dev.guidely.test:80>
    DocumentRoot "D:/dev/guidely/public"
    ServerName dev.guidely.test
     <Directory "D:/dev/guidely/public">
       DirectoryIndex index.php
       AllowOverride All
       Order allow,deny
       Allow from all
       // Require all granted - uncommand this line
   </Directory>
</VirtualHost>
```

# Xampp

## change Apache port {#apache}
After Xampp -> config -> Service & Port settings -> set port -> save
Restart xampp
visit localhost:8080

```php
Location: C:\xampp\apache\conf\httpd\httpd.conf
# Listen 80
Listen 8080
# ServerName localhost:80
ServerName localhost:8080

Location: C:\xampp\apache\conf\httpd\extra\httpd-ssl.conf
# Listen 443
Listen 444
# <VirtualHost _default_:443>
<VirtualHost _default_:444>
# ServerName www.example.com:443
ServerName www.example.com:444
```

## Change mysql port
Location: C:/xampp/mysql/bin/my.ini

```php
port=3306
port=3307
port=8111
```

## Error: MySQL shutdown unexpectedly.
https://stackoverflow.com/questions/18022809/xampp-mysql-shutdown-unexpectedly

```php
my.ini -> 3- change client port and server port - 4000

Go to C:\xampp\mysql\backup.
Copy all files.
Paste them into C:\xampp\mysql\data.
If Windows asks you to replace some files, replace them.
If your XAMPP Control Panes is active close it.
Go to C:\xampp and find xampp-control, run it.
Start Apache, start MySQL.
```

## Table Index
Index - userid, created_at, total
select column `total` also indexing is best practice
> select sum(total) from users where userid=1 and between '2020-01-01 00:00:00' and '2020-12-31 59:59:59'

Index - firstname
Here Query optimizer automatically choose the index column(firstname) first - fast
> select * from users where birthday='2021-01-01' and fistname='vignesh'

compound Index - lastname, firstname - fast
> select * from users lastname='bsdub' and firstname='sdgyu'

# Codeigniter Guideline

## XAMPP
1. Install xampp (version 7.*)

### Extensions
You can install some extenstion, ex: mongodb, redis,... at **https://pecl.php.net/package-search.php**
1. Download an extension-file-name.dll per your current php version (state: stable). Click **DLL** to redirect to new page and scroll down to get the compatible version
2. Copy extension-file-name.dll to C:\XAMPP\php\ext
3. Open the php.ini file and add extension=extension-file-name.dll line into this file. Ex: extension=php_mongodb.dll (line 915...)
4. Restart XAMPP server
5. Open phpinfo: http://localhost/dashboard/phpinfo.php
6. Press Ctrl + f to find the successful installation (type extension name...)

## Composer
1. Install
2. Use

## Mongodb driver on php
1. Install mongodb
2. Install mongo compass (UI)
3. Add the ***extension=php_mongodb.dll*** to php.init (C:\xampp\php\php.init) on top of *extension=bz2*
4. Download php_mongodb.dll
   - List of version: [click here](https://pecl.php.net/package/mongodb)
   - Select the DLL (***stable*** state) of Downloads column (mongodb-1.13.0.tgz (1406.5kB)  ***DLL***)
   - Redirect to new page, please you scroll to the List dll (bottom page)
   - Choose the ST, if it is not work, change to NST and compatible with php version (open cmd and type php -v)
5. Paste the php_mongodb.dll (can you unzip) to ***C:\xampp\php\ext*** folder
6. Open the xampp control panel and start apache
7. Search a ***http://localhost/dashboard/phpinfo.php*** on browser (address search)
8. Press **ctrl + f** and type ***mongo***


## Codeigniter
1. Learn youtube: [click here](https://youtu.be/jnjjzATOgIM?list=PLh89M5lS1CIATULcdS4UHx9pj3GGW8nNM)
2. Codeigniter mvc: [click here](https://media.oiipdf.com/pdf/2f35232b-46b3-47b7-92f3-f449b896f6b7.pdf)
3. Download the codeigniter zip file here: [Click](https://codeigniter.com/userguide3/installation/downloads.html)
4. Unzip and put it to xampp's htdocs folder
5. [Continue...](https://codeigniter.com/userguide3/installation/index.html)

### Router
1. 
2. b
3. c


# mysql-to-google-drive
This is a script that i use to backup my databases to google drive.
### Note
The only file needed here is **dumper.php**


### Install

Set your database creds on **dumper.php**

```sh
composer install
```

Enable your google drive API [here](https://developers.google.com/drive/api/v3/quickstart/php)
Download and replace **credentials.json**

Then ...
```sh
php dumper
```

Visit url and give your application access to the API, a new file named **token.json** is saved under the project directory.

Deploy all files to your server and configure a cron job that runs dumper.php every hour or every day. 
(you are free to choose whatever you like)

# Migrate OJS database
## Requirement
- PHP version 7.4.
## Method:
Upgrade sequentially from 3.0.2 → 3.1.1-2 → 3.2.1-4 → 3.3.0-8.
## Implementation
Download suitable [release package](https://pkp.sfu.ca/software/ojs/download/) versions of OJS (4 versions above):
## Upgrade from old version to nearest new version (example with 3.0.2 to 3.1.1-2)
### Step 0. Disable custom theme
Custom theme will break new version. Follow this step for disable custom theme and use default theme. 
```
    Login admin account  → Settings  → Appearance → Theme → Pick Default theme from the list
```
Should use current version to do that. If you run OJS 3.0.2 on PHP-7.4, you must apply [this patch](https://forum.pkp.sfu.ca/t/ojs-3-1-blank-screen-after-installation/36243).

### Step 1. Backup data:  
- Backup database: Dumps a `.sql` and **MUST** clone to new database. Old database will be modified.
- Backup files: Move `files` folder from old version to new version.
- Backup plugins: Move `plugins` folder from old version to new version.
### Step 2 (Modify configuration):
From 3.1.1-2 package extracted, modify configuration file `config.inc.php` to ensure can use old username and  password. **DON'T FORGET** change encryption to `md5` as old version.
```
...
;;;;;;;;;;;;;;;;;;;;;
; Security Settings ;
;;;;;;;;;;;;;;;;;;;;;

...
encryption = md5
...
```

### Step 3. Upgrade:
To avoid getting error: `Data too long for column`. Access to the MySQL server and run the following command and use old database dump again (new database was modified):
```
mysql> SET @@global.sql_mode= 'NO_ENGINE_SUBSTITUTION';
```
Inside old version of OJS run following command to upgrade database:
```
php tools/upgrade.php upgrade
```
You should see the notification if upgrading succesfully: 
```
Succesfully upgraded to version 3.1.1.2
```

### Step 4. Repeat the process: 
Repeat upgrading to the nearest version:
3.1.1-2 → 3.2.1-4, 3.2.1-4 → 3.3.0-8.

## Fix unicode charset
Fix database to ensure not have error with UTF-8 characters. Inside target database, run:
```
mysql> USE <target-database-name>;
mysql> SOURCE <path to fix_db.sql>;
```

# Deploy JCSCE (OJS 3.3.0-8)
## Build image
Run npm install with:
```
npm i
```
Run this command with `COMPOSER_TOKEN` from Github token. Please check `Dockerfile` in `deployment`.
```
npm run containerize -- --build-arg COMPOSER_TOKEN=ghp_xxxxxxxxxxx
```
Built container image should be tagged as `jcsce-ojs:3.3.0`. Tested on host with `node-v22.13.1` and `npm-v11.0.0`.

## Using Docker-compose
Make sure these volume for mapping with file/folder from old OJS. 
```
    volumes:
      - "./files:/var/www/html/files"
      - "./public:/var/www/html/public"
      - "./cache:/var/www/html/cache"
      - "./config.inc.php:/var/www/html/config.inc.php"
      - "./php.ini:/usr/local/etc/php/php.ini"
```

Run it with `docker-compose up -d`. Please check port mapping in config.

# Config theme

Follow this step for config theme:
```
    Login admin account  → Settings  → Appearance → Theme
```

Options:
- Colour: `#0E5450`.
- Check `Show the journal summary on the homepage`.

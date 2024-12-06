# deploy - OJS in a docker


### How to use

If you want to run it locally (or in your own server), first you need to install
[docker](https://docs.docker.com/get-docker/) (even [docker-compose](https://docs.docker.com/compose/install/) it's also recommended).

You can have it all up and running in less than 10 minutes following this brief howto:
https://www.digitalocean.com/community/tutorials/how-to-install-docker-compose-on-debian-10

After this, we provide a **docker-compose** configuration file so
you will be able to start a full OJS stack (web server + myphpadmin + database ) in 4 easy steps:

1. Clone this repository in your machine (if youprefer, you can also unzip it):

    ```bash
    $ git clone https://github.com/tunghoang/jcsce-ojs.git
    $ mv docker-ojs journalName && cd journalName
    ```

   Replace "journalName" with a short name of your journal.

2. Set your mysql data:
   
	Put your **mysql** data into folder "data".
	
 	Ensure name folder is "mysql" or you can change the 'volumes' path to 'data' in dockercompose.
	
 	Also, make sure that environment variables in your Docker Compose file to match your data for successful access.
4. Set your Open Journal System:
   
	Put your **OJS** into folder "ojs".

	Check if you have not file config.inc.php. You can also modify environment variables in your Docker Compose file to match the settings in your config.inc.php file, or vice versa.

6. Run the docker compose:
    ```bash
    $ docker compose up
    ```

    Docker-compose will pull images from dockerHub and do all the hard work for you to rise a full functional OJS.
    If all goes as expected you will see your app_container informing apache is RUNNING successfully.

   
### OJS requires 
1. PHP: vesion 7.4.44
2. Mysql: version 8.0.40
3. Phpmyadmin:

# premiumizesonarr

Connects Sonarr & Jacket with Premiumize.me
While downloading everything over the cloud. The files while not be downloaded to your local computer.
Sonarr is already preconfigured to start downloading via Torrent. If you have Private trackers you can easly add them following the provided examples.

# Requirements

docker
docker-compose
rclone



# Install

## Download the repository
Not a good docker image so you have to download the whole repo to get started.


## Docker-Compose Image

Most of the settings in the docker-compose can be left untouched.
Only the one which should be edited are commented
```yaml
version: "3.7"
services:
  sonarr:
    image: linuxserver/sonarr:preview
    container_name: sonarr
    environment:
      - PUID=1000
      - PGID=1000
      - TZ=Europe/Zurich #Your Timezone
      - UMASK_SET=022 #optional
    volumes:
      - ./config_sonarr:/config
      - ./blackhole:/blackhole
      - ./rclone:/blackhole_watch
    ports:
      - 127.0.0.1:8989:8989
    expose: 
      - 8989
    restart: unless-stopped
  jackett:
    image: linuxserver/jackett
    container_name: jackett
    environment:
      - PUID=1000
      - PGID=1000
      - TZ=Europe/Zurich #Your Timezone
      - RUN_OPTS=<run options here> #optional
    volumes:
      - ./config_jackett:/config
      - ./blackhole:/downloads
    ports:
      - 127.0.0.1:9117:9117
    expose:
      - 9117
    restart: unless-stopped
  premiumizephp:
    image: premiumizephp:1.0
    container_name: premiumizephp
    environment: 
      - API_KEY= #Premiumize API-KEY
      - LIBRARY_FOLDER= #Id of your libary folder see below for explanation
      - DOWNLOAD_FOLDER= #Id of your download folder see below for explanation
    volumes:
      - ./blackhole:/drophere
      - ./rclone:/watch_folder
  reverseproxy:
      image: nginx:latest
      ports:
          - "127.0.0.1:80:80" # Port on which Sonarr/Jackett
      volumes:
          - ./premiumizephp:/code
          - ./nginx.conf:/etc/nginx/nginx.conf
  php:
    image: php:7-fpm
    volumes:
      - ./premiumizephp:/code
    environment: 
      - API_KEY= #Premiumize API-KEY
      - LIBRARY_FOLDER= #Id of your libary folder see below for explanation
      - DOWNLOAD_FOLDER= #Id of your download folder see below for explanation
```
### Folder IDS
For this config to work you have to create 2 Folders at the root in your premiumize cloud.
The required folders have to be named sonarr & sonarr_download.
In the Folder named sonarr you're sorted library will be kept. While in the Folder sonarr_download pending Downloads while reside before they are imported.

After creating a Folder you have to add the ID of the Folder to your dockercompose settings file.
You can get the id by looking at the url you're currently in after entering the folder.
E.x
```https://www.premiumize.me/files?folder_id=WrjIfPb096CUEc6nrsdfs```
In this URL your ID would be WrjIfPb096CUEc6nrsdfs

## Rclone

Next you have to mount premiumize via rclone, for this to work you either need a gui or a other computer that has rclone installed.
```
rclone config
```
Just follow the steps on the screen and make sure to name your connection "premiumize"

# Run IT
## Mount Rclone
You already configured Rclone in the step above now all you have to do is mount the Folder so that it gets readable for sonarr. First ensure that a folder called rclone exists in the Root Folder of Repository. Create it if it does not exist.

Next you can mount it. Just run the command down belowe after going to the correct folder with your Terminal/PowerShell.
```
nohup rclone mount premiumize:/ rclone --allow-other --allow-non-empty --dir-cache-time=2m --cache-chunk-size=10M --cache-info-age=168h --cache-workers=5 --attr-timeout=1s --syslog --rc --cache-tmp-wait-time 30m --log-level INFO &> logs/rclone.log < /dev/null &
```

## Docker
To execute it go the correct folder either in a Terminal or Powershell and then execute docker-compose up -d
This will start the whole service.

# Hurray

You now got Jackett & Sonarr connected to the Premiumize API
You should now be able to access the urls belowe.
```
http://localhost/
http://localhost/jackett/
http://localhost/premiumapi/index.php
```

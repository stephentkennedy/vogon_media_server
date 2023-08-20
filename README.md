# Vogon Media Server
A custom web interface for a dlna media server

# Warning
This project is in active development, components and interfaces my be overhauled completely during this stage of development which will likely go on for years.

This project is not designed for security. The intended use case is a dedicated single purpose device like a Raspberry Pi that has been suitably separated from the rest of the network so that it can't cause problems. This is not something that should be used as is on a public network, no is it something you should expect to be able to open to the internet. Both of those are goals that might be worth while during the course of development, but there are no current security protections built in. This application as configured by the installation script has the ability to run commands as the root user.

## What is Vogon?
It's a prototyping and developing framework for PHP. You can find the framework by itself at https://github.com/stephentkennedy/vogon

## Notable Features

**Meta Data Aware Mass Import**
* Auto-generate Thumbnails for imported videos
* Auto-populate meta data required for history tracking features
* Auto-import common meta data fields for supported audio formats (Artist, Year, Composer, Album, Track Number)

**User Profiles**
* Support for multiple user profiles
* History tracked individually between profiles

**Audiobook Support**
* Turn individual audio tracks into a coherent audio book
* History tracking allows resuming across devices

**E-Book Support**
* Read EPUB files in the browser using [EPUB.js](https://github.com/futurepress/epub.js/)
* Read PDF files in the browser using [PDF.js](https://github.com/mozilla/pdf.js)
* Read CBZ files in the browser using a custom CBZ reader.

**Custom HTML5 Video Player Interface:**
* Autoplay next episode for items categorized as part of a TV series
* History tracking for media resuming across devices

**Custom HTML5 Audio Player Interface:**
* Playlists (Ongoing development)
* Shuffle Play (client-side and client/server hybrid)
* Visualizers (7 currently, but hopefully the vizualizer code will be rewritten to be module and these can be developed as plugins)
* Media key support
* Partial Media Meta Data support (Depending on your browser and device, you will get media controls on lock screens or when the browser is not the active window)
* Sleep timer

## What makes this project different than others
I wrote it. Other than that I can only tell you the project goals.

The point of the project is to make a lean, easily editable, web interface for a dlna media server. Between both approaches, it should support most devices in a home that will want to access networked media. Other projects were large, did convenient but expensive transcoding, abstracted a lot of the customization away, and sometimes you just want to build something from scratch.

The prototype is running successfully on a Raspberry Pi 3 with 2 simultaneous 1080p video streams. More than that and the RAM fills up and buffering becomes constant.

## Installation

### Debian Users
If you are on a clean install of a Debian based OS, you can download and run the debian_media_server_clean_install.sh script in /install_scripts it will download a copy of this repository, install all of the dependencies, and configure your database for you. Once the script is done running, just access your device in a web browser to log in and start using it.

### Manual Installation
You will need Apache, PHP, MySQL, and FFMPEG installed and available for the Apache user(s). There are Composer dependencies so that will need to be installed and run as well (previous versions of this repository self-hosted these dependencies but that has been removed). I also recommend installing MiniDLNA server and setting it up to be run by the same Apache user(s) so that the web interface can manage starting/stopping/restarting as needed, this is not required, and nothing in the software requires MiniDLNA to run, it just covers a number of devices that don't have access to fully HTML5 compliant web browsers.

You will need to setup a database and database user that can create tables, that done, the installer.php will install the needed database information.

After creating an appropriate virtual host, or running this project as the root, you can simply navigate to the project in a web browser. The installer will autostart as long as the "new_install" file exists.

As mentioned above, this is not in anything close to a release state, so expect things not to work completely, or for hard-coded variables to need to be changed. This will be addressed in the future.

## Building
Since this is primarily a PHP project nothing needs to be compiled, but in an effort to make it easier to deploy there is a build process in place, though it does not currently install anything it might need outside its own directory. It needs access to the PHP zip module to function.

There is a pre-established route at "\/build". Simply navigate there in your browser, and the server will build you a zip file that contains a "fresh install", basically everything the system needs to run, minus the contents of your media server and your database connection information. It does carry over routes and var data. You can find this file in the root directory when it is finished building.

# Vogon Media Server
A custom web interface for a dlna media server

# Warning
This project is in active development, I will come back around and document various dependencies, create better and more user accessible interfaces as development continues, but those are scheduled for after the project is feature complete.

I also don't have a complete build process for uploading here to GitHub, so while this repository can be installed and used, as is, it will likely not work as intended. This repository is mostly intended as a portfolio piece and an example on how I work on a larger scale project. For this reason documentation will absolutely be added, but it isn't here yet.

## What is Vogon?
It's a prototyping and developing framework for PHP. You can find the framework by itself at https://github.com/stephentekennedy/vogon

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

**Custom HTML5 Video Player Interface:**
* Autoplay next episode for items categorized as part of a TV series
* History tracking for media resuming across devices

**Custom HTML5 Audio Player Interface:**
* Playlists (Ongoing development)
* Shuffle Play (client-side and client/server hybrid)
* Visualizers (3 currently, but hopefully the vizualizer code will be rewritten to be module and these can be developed as plugins)
* Media key support
* Partial Media Meta Data support (Depending on your browser and device, you will get media controls on lock screens or when the browser is not the active window)
* Sleep timer

## What makes this project different than others
I wrote it. Other than that I can only tell you the project goals.

The point of the project is to make a lean, easily editable, web interface for a dlna media server. Between both approaches, it should support most devices in a home that will want to access networked media. Other projects were large, did convenient but expensive transcoding, abstracted a lot of the customization away, and sometimes you just want to build something from scratch.

The prototype is running successfully on a Raspberry Pi 3 with 2 simultaneous 1080p video streams. More than that and the RAM fills up and buffering becomes constant.

## Installation
You will need Apache, PHP, MySQL, and FFMPEG installed and available for the Apache user(s). I also recommend installing MiniDLNA server and setting it up to be run by the same Apache user(s) so that the web interface can manage starting/stopping/restarting as needed, this is not required, and nothing in the software requires MiniDLNA to run, it just covers a number of devices that don't have access to fully HTML5 compliant web browsers.

You will need to setup a database and database user that can create tables, that done, the installer.php will install the needed database information.

After creating an appropriate virtual host, or running this project as the root, you can simply navigate to the project in a web browser. The installer will autostart as long as the "new_install" file exists.

As mentioned above, this is not in anything close to a release state, so expect things not to work completely, or for hard-coded variables to need to be changed. This will be addressed in the future.

## Building
Since this is primarily a PHP project nothing needs to be compiled, but in an effort to make it easier to deploy there is a build process in place, though it does not currently install anything it might need outside its own directory. Additionally, it needs access to the PHP zip module to function, and does not currently build the .zip correctly on Windows machines.

There is a pre-established route at "\/build". Simply navigate there in your browser, and the server will build you a zip file that contains a "fresh install", basically everything the system needs to run, minus the contents of your media server and your database connection information. It does carry over routes and var data. You can find this file in the root directory when it is finished building.

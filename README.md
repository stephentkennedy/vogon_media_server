# Vogon Media Server
A custom web interface for a dlna media server

# Warning
This project is in active development, I will come back around and document various dependencies, create better and more user accessible interfaces as development continues, but those are scheduled for after the project is feature complete.

I also don't have a complete build process for uploading here to GitHub, so while this repository can be installed and used, as is, it will likely not work as intended. This repository is mostly intended as a portfolio piece and an example on how I work on a larger scale project. For this reason documentation will absolutely be added, but it isn't here yet.

## What is Vogon?
It's a prototyping and developing framework for PHP. You can find the framework by itself at https://github.com/stephentekennedy/vogon

## What makes this project different than others
I wrote it. Other than that I can only tell you the project goals.

The point of the project is to make a lean, easily editable, web interface for a dlna media server. Between both approaches, it should support most devices in a home that will want to access networked media. Other projects were large, did convenient but expensive transcoding, abstracted a lot of the customization away, and sometimes you just want to build something from scratch.

The prototype is running successfully on a Raspberry Pi 3 with 2 simultaneous 1080p video streams. More than that and the RAM fills up and buffering becomes constant.

## Installation
You will need Apache, PHP, and MySQL installed. After creating an appropriate virtual host, or running this project as the root, you can simply navigate to the project in a web browser.

You will need to setup a database and database user that can create tables, that done, the installer.php will install the needed database information.

As mentioned above, this is not in anything close to a release state, so expect things not to work completely, or for hard-coded variables to need to be changed. This will be addressed in the future.

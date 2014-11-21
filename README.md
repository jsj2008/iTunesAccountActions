iTunesAccountActions
====================

Create regular and EDU iTunes accounts

Usage
-----
Include iTunesAccountActions.class.php in your source

Provides the following public methods, see class comments for more details:
````
getError	- gets error thrown by apple
associateMD - Associates MDInvite URL with logged in user
createAccount - creates regular (13 and over) account from supplied info
verifyAccount - verifys email address
createAccountEDU - creates EDU (12 and under) with parent url and supplied info
````

Installation
------------
Make sure you have PHP 5.5 or greater installed:
On OSX using <a href=http://brew.sh/>Homebrew</a>:

````
brew update
brew tap homebrew/dupes
brew tap homebrew/php
brew install composer
````

Using <a href=https://getcomposer.org/>Composer</a>:

Add this to composer.json in your project directory

````
{
 "require" : {
    "andrewzirkel/itunes-account-actions" : "~1.0"
	 }
}
````


## KISSCMS: Google

A Google services API streamlined to [KISSCMS](http://github.com/makesites/kisscms) conventions.


## Dependencies

This plugin relies on the [Google APIs Client Library for PHP](https://code.google.com/p/google-api-php-client/) **v0.6.7**


## Install

After you download the plugin and the client library (the main dependency noted above), place the plugin in your ```plugins``` folder and the client library in the lib folder or in a separate SDK folder.

If you pick the latter you'lhave to edit the env.json to include an SDK path


## Usage

The namespace the plugin reserves is ```Google``` To start using the api simply type
```
$api = new Google();
```

## Methods

These are the methods available under the namespace:

* **me()**: Returns an object of the logged in user, taken from their google plus profile.


##Credits

Created by Makis Tracend (@tracend)

#Welcome

Welcome to Agile Toolkit for WordPress interface: atk4-wp. 
This interface enable the use of Agile Toolkit framework within WordPress. 
It is now possible to use this great framework within Wordpress plugin developpment.

[For more information on Agile Toolkit: http://www.agiletoolkit.org] (http://www.agiletoolkit.org)

With this interface, it is possible to create WordPress admin page, sub page, meta boxes, widget or shortcode using an Agile Toolkit
view class and also benefit from Agile Toolkit way of handling view like: data display with db model, form submission, view reload and ajax call handling.

##Note


#Getting Started

This interface, along with Agile Toolkit framework (atk4) need to be install within your wp-content folder. 
Each plugin using the atk4-wp interface should be created within the WordPress plugin directory as normal WordPress plugin do.
The atk4-wp interface can be use for multiple plugins within the same site.

##Requirement

This interface required [Composer] (https://getcomposer.org/). Please make sure it is installed before continuing.

###Installing Composer on Linux or Mac OS X

```
$ curl -sS https://getcomposer.org/installer | php
```

```
$ sudo mv composer.phar /usr/local/bin/composer
```

###Installing Composer on Windows

Download the installer from getcomposer.org/download, execute it and follow the instructions.

##Installation

Download or clone this repository within your WordPress wp-content folder. Next, at the root of the atk4-wp folder type:

```
composer install
```

Composer will download and install the Agile Toolkit framework within the atk4-wp/vendor directory.


#Creating a plugin

You are now ready to use either the atk4wp sample plugin, start using an empty template or build one from scratch.

##Using the sample plugin

This plugin use different aspects of WordPress: an admin page and sub page, meta boxes, widget and shortcode.
It also changes the WordPress database by adding an event table that meta boxes and widget uses. 
It is a good sample to show you how to integrate Agile Toolkit views within WordPress.

You can download the sample plugin here: 


##Start from a template

This template will simply install basic directories structure and files that you can uses for developping a new WordPress plugin with Agile Tookit and this interface.

You can download the template here: 

###Create your plugin namespace

Edit the composer.json file 

###Edit the plugin file

1- Open the plugin.php file and replace the top section with proper name and description corresponding to your plugin.

```php
/**
 * @wordpress-plugin
 * Plugin Name:       YOUR_PLUGIN_NAME
 * Plugin URI:        YOUR_SITE
 * Description:       YOUR_DESCRIPTION
 * Version:           1.0.0
 * Author:            YOUR_NAME
 */
 ```
 
 
 2- Still in plugin.php replace PlUGIN_NAMESPACE to your namespace
 
 ```php
 //Rename using your namespace.
 namespace PLUGIN_NAMESPACE;
 ```
 
 3. Finally edit the $atk_plugin_name variable with your plugin name.
 
 ```php
 //Rename using your plugin name.
 $atk_plugin_name  = "PLUGIN_NAME";
 ```

##Create from scracth

For those of you that like to start from scratch:

1. Inside your WordPress plugin folder create a directory structure like this:

``` ruby
PluginName/
+-- lib
+-- public
+-- templates
+-- vendor
```

2. Create your composer.json file inside your main plugin directory.

```json
{
  "name": "Add your name here",
  "description": "Add your description here",
  "license": "MIT",
  "autoload": {
    "psr-4": {
      "YOUR_NAMESPACE\\": "lib/"
    }
  }
}
```

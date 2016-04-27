#Welcome

Welcome to Agile Toolkit for WordPress interface: atk4-wp. 
This interface enable the use of Agile Toolkit framework within WordPress.
 
For those of you familiar with the framework, it is now possible to use this great framework for Wordpress plugin development.

If you are not familiar with Agile Toolkit, this framework is really easy to learn. I know, I was able to learn it!

[For more information on Agile Toolkit: http://www.agiletoolkit.org] (http://www.agiletoolkit.org)

#Benefits

With this interface, it is possible to create many WordPress components like: admin page, sub page, meta boxes, widget or shortcode using the Agile Toolkit
framework. 

* use many of Agile Toolkit predefine views like: header, button, lister, tabs, grid, form, crud, popover and more;
  * easily create the UI of a WordPress admin page;  
* view data binding with db model;
  * associate model with view and let the template engine display the model data automatically;
* out-of-the-box ajax in WordPress;
  * form submission using ajax;
  * view reload using ajax;
  * javascript binding to any view element with jQuery;
* data model with active record pattern and ORM;
* and many more...

#Getting Started

This interface, along with Agile Toolkit framework (atk4) need to be install within your wp-content folder. 
Each plugin using the atk4-wp interface should be created within the WordPress plugin directory as normal WordPress plugin do.

The atk4-wp interface can be use for multiple plugins development within the same WordPress site.

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

Download or clone this repository within your WordPress wp-content folder. Using terminal at the root of the atk4-wp folder type:

```
composer install
```

Composer will download and install the Agile Toolkit framework within the atk4-wp/vendor directory.

#Atk4-wp Plugin Sample and Template

To study this interface or jump start development of your plugin you can use either:
 
 * The [atk4wp-sample](https://github.com/ibelar/atk4wp-sample) plugin;
 * The [atk4wp-template](https://github.com/ibelar/atk4wp-template) plugin;

##Using the sample plugin

This plugin use different WordPress components: admin pages with main and sub menu items, meta boxes, widget and shortcodes.
It also updates the WordPress database by adding an event table. Event data information will also be display in meta boxes and widget view. 
It is a good sample to demonstrate on how to integrate Agile Toolkit views within WordPress.

More information on using the sample here: [atk4wp-sample](https://github.com/ibelar/atk4wp-sample)


##Starting from the template

The template will simply install minimum files needed to start building a new WordPress plugin with Agile Tookit using atk4-wp.

More information on using the template here: [atk4wp-template](https://github.com/ibelar/atk4wp-template)

#WordPress components manage by Atk4

Wordpress components managed by Atk4 class like admin pages or panels, widgets, meta boxes and shortcodes are added to WP using configuration options set in your plugin configuration files.
For example, adding an admin page within WordPress that is managed by an Atk4 View is done by setting the proper options for that page in config-panel. 
Using this configuration file, you would simply set the usual WordPress admin page options, but also specify the Atk4 class that will be use to display the page in WordPress.

When plugin start, it automatically loads WordPress components define in configuration, add them within WordPress and set proper Atk4 classes to manage them.
You can then use these classes for adding others elements like views, models, controllers etc, as you would normally do for a regular Agile toolkit application.

For more information on WordPress components and their configuration, see the [atk4wp-template](https://github.com/ibelar/atk4wp-template) plugin.

#WordPress Plugin using the atk4-wp interface.

##Multiple plugins

It is possible to create more than one plugin within the same WordPress installation using this interface. This is why the Atk4-wp interface resides within the wp-content folder.
Each defines plugin using this interface will run as an instance of an Agile Toolkit application.


##plugin.php file and the Plugin Class

##Directory and file structure

Below is the recommend directory structure for your plugin. You should start by creating a new directory under WordPress plugins folder that reflect the name of your plugin.

```
/wp-content
└───/plugins
    ├───/MyPlugin
        │───plugin.php
        │───/lib   
        │   │───Plugin.php
        │───/public
        │   │───/css
        │   │───/images
        │   │───/js             
        │───/templates 
        │───/vendor  
```

The plugin.php file must be located at the root of your plugin directory. This file is responsible for properly setup your plugin in WordPress and create the Atk4 application instance.

The lib directory must contain the Plugin.php class file. This class file must extends the interface application class. 

The lib directory should also contain other Agile Toolkit classes that you define for your plugin, preferably in their proper directory as well.

The templates directory should contain all your template files for views.

The public directory should contain css, js and images directory needed for your plugin.


###plugin.php

Each plugin required to properly setup the plugin.php file. Inside the plugin.php file, you need to setup the name and namespace of your plugin. 
This file is responsible for creating the Agile Toolkit application instance. It also assigns a dynamic variable name to the instance.

```php
//Rename using your namespace.
namespace PLUGIN_NAMESPACE;
//Rename using your plugin name.
$atk_plugin_name  = "PLUGIN_NAME";
$atk_plugin_classname = __NAMESPACE__."\\Plugin";
$$atk_plugin_name = new $atk_plugin_classname( $atk_plugin_name, plugin_dir_path( __FILE__ ) );
```

When WordPress start, it register the plugin and use the application instance boot() method to register each components set in configuration.

```php
if ( ! is_null( $$atk_plugin_name)) {
	register_activation_hook(__FILE__, [ $$atk_plugin_name, 'activatePlugin']);
	register_deactivation_hook(__FILE__, [ $$atk_plugin_name, 'deactivatePlugin']);
	$$atk_plugin_name->boot();
}
```

###Plugin Class

The Plugin class is responsible for creating and properly initiating the Agile Toolkit application instance to work under WordPress. In order for this to happen, it must extend the WpAtk4 class and implement the Pluggable class interface.

```php
//rename using your namespace.
namespace PLUGIN_NAMESPACE;
class Plugin extends \WpAtk implements \Pluggable
{
	public function init()
	{
		parent::init();
		$this->dbConnect();
	}
	public function activatePlugin()
	{}
	public function deactivatePlugin()
	{}
	public function uninstallPlugin()
	{}
}
```

For more information on plugin.php file and Plugin class, see the [atk4wp-template](https://github.com/ibelar/atk4wp-template) plugin.

#License

Copyright (c) 2016 Alain Belair. MIT Licensed,

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
#Welcome

Welcome to Agile Toolkit for WordPress interface: atk4-wp. 
This interface enable the use of Agile Toolkit framework within WordPress.
 
For those of you familiar with the framework, it is now possible to use this great framework for Wordpress plugin developpment.

If you are not familiar with Agile Toolkit, this framework is really easy to learn. I know, I was able to learned it!

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

This plugin use different components of WordPress: an admin page and sub page, meta boxes, widget and shortcode.
It also changes the WordPress database by adding an event table that meta boxes and widget uses. 
It is a good sample to show you how to integrate Agile Toolkit views within WordPress.

More information on using the sample here: [atk4wp-sample](https://github.com/ibelar/atk4wp-sample)


##Starting from the template

The template will simply install minimum files needed to start building a new WordPress plugin with Agile Tookit using atk4-wp.

More information on using the template here: [atk4wp-template](https://github.com/ibelar/atk4wp-template)

#WordPress components manage by Atk4

Wordpress components managed by Atk4 class like admin pages or panels, widgets, meta boxes and shortcode are added using configuration options within your plugin configuration files.
For example, adding an admin page within WordPress that is managed by an Atk4 View is done by setting the proper options for panel in config-panel. 
In this configuration file, you would simply set the usual WordPress admin page options but also specify the Atk4 class that will be use to display that page in WordPress.

When your plugin start, it will automatically load WordPress components define in configuration, add it within WordPress and set the proper Atk4 class to manage the component.
You can then use this Atk4 class to add others views, model, controller etc, as you would for a regular Agile toolkit application.

For more information on WordPress components and their configuration, see the [atk4wp-template](https://github.com/ibelar/atk4wp-template) plugin.

#WordPress Plugin using the interface.

##Multiple plugins

You can use the same interface for more than one plugins within the same WordPress installation. That is why the Atk4-wp interface has been placed within the wp-content folder.
Each plugin define will run as an instance of the interface, or the Atk4 framework, in order to allow for creating multiple plugins.

##plugin.php file and the Plugin Class

###plugin.php

Each plugin required to properly setup the plugin.php file, located at the root of your plugin directory and the Plugin Class, located within within the lib directory. 
The plugin.php file will register your plugin within WordPress.

```
wp-content
└───plugins
    ├───MyPlugin
    │   │   plugin.php
    │   │───lib   
    │   │   |   Plugin.php
```

Inside the plugin.php file, you need to setup the name and namespace of your plugin. The plugin.php file is responsible for creating an instance of the Atk framework. It also assign a dynamic variable name to the instance.

```php
//Rename using your namespace.
namespace PLUGIN_NAMESPACE;
//Rename using your plugin name.
$atk_plugin_name  = "PLUGIN_NAME";
$atk_plugin_classname = __NAMESPACE__."\\Plugin";
$$atk_plugin_name = new $atk_plugin_classname( $atk_plugin_name, plugin_dir_path( __FILE__ ) );
```

After properly creating the Plugin instance plugin.php will call the boot() function to start things up in WP.

```php
if ( ! is_null( $$atk_plugin_name)) {
	register_activation_hook(__FILE__, [ $$atk_plugin_name, 'activatePlugin']);
	register_deactivation_hook(__FILE__, [ $$atk_plugin_name, 'deactivatePlugin']);
	$$atk_plugin_name->boot();
}
```

###Plugin Class

The Plugin class is responsible for properly creating the Atk4 instance within WordPress and therefore must extends the atk4-wp interface and also implement Pluggable.
It will also load and create all register WordPress component set in configuration files via the boot() function.

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
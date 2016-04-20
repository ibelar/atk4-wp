#Welcome

Welcome to Agile Toolkit for WordPress interface: atk4-wp. 
This interface enable the use of Agile Toolkit framework within WordPress. 
It is now possible to use this great framework within Wordpress plugin developpment.

[For more information on Agile Toolkit: http://www.agiletoolkit.org] (http://www.agiletoolkit.org)

With this interface, it is possible to create WordPress admin page, sub page, meta boxes, widget or shortcode using an Agile Toolkit
view class and also benefit from Agile Toolkit way of handling view like: data display with db model, form submission, view reload, model ORM and ajax call handling.


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


#Creating a WordPress plugin

WordPress components are added to your plugin via configuration file, each component having it's own configuration:

* Panels uses config-panel.php file;
  * Panel are WordPress admin page accessible via admin menu and sub-menu;
  * Example of a [Panel configuration](https://github.com/ibelar/atk4wp-sample/blob/master/config-panel.php) from the sample plugin;
* Meta boxes uses config-metabox.php file;
  * Example of a [Meta boxes configuration](https://github.com/ibelar/atk4wp-sample/blob/master/config-metabox.php) from the sample plugin;
* Widgets uses config-widget.php file;
  * Example of a [Widget configuration](https://github.com/ibelar/atk4wp-sample/blob/master/config-widget.php) from the sample plugin;
* Shortcodes uses config-shortcode.php file;
  * Example of a [Shortcode configuration](https://github.com/ibelar/atk4wp-sample/blob/master/config-shortcode.php) from the sample plugin;

Adding a component to your plugin usually require to add the component definition via the component configuration options.
The component options are required by WordPress to build the component itself inside WordPress but also define the Agile Toolkit view class needed to display the component, via the 'uses' option.

Depending on the component type, the Agile Toolkit view class define for the component 'uses' option need to extends the proper interface class:

* The class define in 'uses' option for panel must extends WpPanel;
* The class define in 'uses' option for meta box must extends WpMetaBox;
* The class define in 'uses' option for shortcode must extends WpShortcode;
* The class define in 'uses' option for widget must extends WpWidget;

Except for the WpWidget class, all others interfaces classes are children of Agile Toolkit AbstractView. 
Therefore, you can treat them as a regular Agile Toolkit view class; like you would do in a normal Agile Toolkit application.

Example of a panel 'uses' configuration option:

```php
$config['panel']['event'] =  [  'type'  => 'panel',
                                'uses'  => 'my_plugin\Panel\MyPanel',
                                //other panel option...
                                ];
```

Then in MyPlugin/lib/Panel folder define the Event class:

```php
namespace my_plugin\Panel;
class MyPanel extends \Wp_WpPanel {}
```
##Note on WpWidget

The WpWidget class is not a children of an Agile Toolkit AbstractView simply because WordPress required that widget use the WordPress Widget class for defining their widgets. 
In order to be able to set the widget display using an Agile Toolkit view, the interface will add an Agile Toolkit view using the addWidgetDisplay('View') function.
You may pass a regular Agile Toolkit view class to the function or define your own. 
You may also set the onDisplay( $callback ) function hook to your widget. This callback will be call prior to display the widget in WordPress and will receive the Agile Toolkit view instance, the one define with addWidgetDisplay(), and a copy of the widget instance field value, if defined.
This is usefull for setting up the view prior to display it in WordPress, by calling the database or any other action need to set it up.

Example of a widget class:

```php
namespace my_plugin\Widget;
class MyWidget extends \Wp_WpWidget
{
    public function init()
    	{
    		parent::init();
    		//inject the atk view
    		$this->addWidgetDisplay('my_plugin\View\MyView', 'my_view_title');
    		//setup the display callback
    		$this->onDisplay( [$this, 'beforeDisplayWidget']);
    	}
    public function beforeDisplayWidget( $atkView, $instance  )
	{
	    //setup the atk view...
		$atkView->setModel('my_plugin\Model\MyModel');
	}
}
```

You are now ready to use either the [atk4wp-sample](https://github.com/ibelar/atk4wp-sample) plugin or start building your own plugin using the [atk4wp-template](https://github.com/ibelar/atk4wp-template).

##Using the sample plugin

This plugin use different components of WordPress: an admin page and sub page, meta boxes, widget and shortcode.
It also changes the WordPress database by adding an event table that meta boxes and widget uses. 
It is a good sample to show you how to integrate Agile Toolkit views within WordPress.

More information on the sample here: [atk4wp-sample](https://github.com/ibelar/atk4wp-sample)


##Start from a template

This template will simply install minimum files needed to start building a new WordPress plugin with Agile Tookit using atk4-wp.

More information on the template here: [atk4wp-template](https://github.com/ibelar/atk4wp-template)

#License

Copyright (c) 2016 Alain Belair. MIT Licensed,

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
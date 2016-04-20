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


#How to add WordPress component to your plugin.

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
The component options are required by WordPress to build the component itself inside WordPress but also to define the Agile Toolkit view class needed to display the component. This class is define via the 'uses' option.

Depending on the component type, the Agile Toolkit view class define by the component 'uses' option need to extends the proper interface class:

* The class define by 'uses' option for panel must extends WpPanel;
* The class define by 'uses' option for meta box must extends WpMetaBox;
* The class define by 'uses' option for shortcode must extends WpShortcode;
* The class define by 'uses' option for widget must extends WpWidget;

Except for the WpWidget class, all others interfaces classes are children of Agile Toolkit AbstractView. 
Therefore, you can treat them as a regular Agile Toolkit view class; like you would do in a normal Agile Toolkit application.

Example of a panel 'uses' configuration option:

```php
$config['panel']['event'] =  [  'type'  => 'panel',
                                'uses'  => 'my_plugin\Panel\MyPanel',
                                //other panel option...
                                ];
```

Then in MyPlugin/lib/Panel folder define MyPanel class:

```php
namespace my_plugin\Panel;
class MyPanel extends \Wp_WpPanel {}
```
##Note on WpWidget Class

This class is not a children of an Agile Toolkit AbstractView, simply because WordPress required that widgets must extends their widget class. 

###Widget display and onDisplay callback

In order to be able to set the widget display using an Agile Toolkit view, you will add it using the addWidgetDisplay('View') function.
You may pass a regular Agile Toolkit view class to the function or define your own.

You may also set the onDisplay( $callback ) function hook for the widget. This callback will be call prior to output the widget in WordPress giving a chance to setup the view.
The callback function will receive the Agile Toolkit view instance, the one define with addWidgetDisplay(), and a copy of the widget instance field value, if defined, as parameters.
This is useful for setting up the view prior to display it in WordPress.

Setting up a widget display in a WpWidget class:

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

###Widget Form and onForm callback

Widget can also display form input element within the widget admin area of WordPress. (Appearance/Widgets). Form add to widgets need to extends Form_WpWidget class.
This is a special Agile Toolkit form adapted for widget display. Form are added via the addForm('Form_WpWidget') function. This function will also return the newly added form instance and this form instance can be use for adding field to the form using the addField() function.

Furthermore, it is also possible to setup a callback function just prior of outputting the form in WordPress admin area with the onForm(callback) function. 
The callback function will receive the Agile Toolkit form instance, the one define with addForm(), and a copy of the widget instance field value.

Field added to the form may also have default value by using the setInstanceDefaults() function by passing an array of field_id=>value pair to the function. 
Note that field input value added to the form is automatically save within WordPress option table when user click the widget 'Save' button.

```php
namespace my_plugin\Widget;
class MyWidget extends \Wp_WpWidget
{
    public function init()
    	{
    		parent::init();
    		//inject the atk form
    		$f = $this->addForm('Form_WpWidget');
    		$f->addField('line', 'title');
    		//setup the display callback
    		$this->onForm( [$this, 'beforeDisplayForm']);
    	    //set widget field default value
    	    $this->setInstanceDefaults( ['title'=> 'My Default Title'] );
    	}
    public function beforeDisplayForm( $atkForm, $instance  )
	{
	    //setup the atk form...
	}
}
```
#Atk4-wp Plugin Sample and Template

To study this interface or jump start development of your plugin you can use either:
 
 * The [atk4wp-sample](https://github.com/ibelar/atk4wp-sample) plugin;
 * The [atk4wp-template](https://github.com/ibelar/atk4wp-template) plugin;

##Using the sample plugin

This plugin use different components of WordPress: an admin page and sub page, meta boxes, widget and shortcode.
It also changes the WordPress database by adding an event table that meta boxes and widget uses. 
It is a good sample to show you how to integrate Agile Toolkit views within WordPress.

More information on the sample here: [atk4wp-sample](https://github.com/ibelar/atk4wp-sample)


##Starting from the template

The template will simply install minimum files needed to start building a new WordPress plugin with Agile Tookit using atk4-wp.

More information on the template here: [atk4wp-template](https://github.com/ibelar/atk4wp-template)

#License

Copyright (c) 2016 Alain Belair. MIT Licensed,

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
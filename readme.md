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


#Creating a plugin

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
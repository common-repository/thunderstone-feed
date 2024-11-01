=== WooCommerce XML feed for Thunderstone ===
Plugin URI: https://www.thunderstone.io/solution/
Description: XML feed creator for Thunderstone
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 4.8
Stable tag: 1.1
Contributors: Thunderstone
Author URI: https://www.thunderstone.io/mention-legale/
Tags: ecommerce, e-commerce,  wordpress ecommerce, xml, feed
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create Thunderstone XML feed for Woocommerce

== Description ==

With this plugin you can create XML feed for Thunderstone.


== Frequently Asked Questions ==

= When in Stock Availability =
Dropdown  option "When in Stock Availability"   with options will show for all in Stock products
"Available", "1 to 3 days", "4 to 7 days", "7+ days" as availability

= If Product Attribute: Availability is used =
Dropdown  option "When in Stock Availability" value "Product Attribute: Availability" must be used

= If Custom Availability plugin is used =
Dropdown  option "When in Stock Availability" value "Custom Availability" must be used

= If a Product is out of Stock =
Dropdown  option "If a Product is out of Stock"  with options will
"Include as out of Stock or Upon Request" or "Exclude from feed"

= Add mpn/isbn to product =

To add mpn/isbn to the product just fill in the SKU field of WooCommerce


= Add color =

To add the color to a product , in order to be printed on the XML feed add an attribute with Slug "color" , Type "Select" and Name of your choice

= Add manufacturer =

To add the manufacturer to a product , in order to be printed on the XML feed add an attribute with Slug "manufacturer" , Type "Select" and Name of your choice

OR

Brands plugins are supported to be shown as manufacturer.


= Add sizes =

To add the size to a product, in order to be printed on the XML feed, add an attribute with Slug "size", Type "Select" and Name of your choice.
Then is created a variable product with this attribute.

If you have stock management enabled on variations, sizes with stock lower or equal to 0 will not be shown on the feed

== Upgrade Notice ==
= 1.0 =
Initialize plugin

= 3rd party =

Your data will be sent to thunderstone.io and thunderstone will use it to display your products on our terminal.
You can still choose after in your thunderstone back-office on which store your products will be displayed.
Do not hesitate to contact us on contact@thunderstone.io.

= Legal and Privacy =

You can find our legal terms: https://www.thunderstone.io/en/legal-notice/
You can find our privacy policies: https://www.thunderstone.io/cgv/

== Changelog ==

= Version: 1.0.2 =
WooCommerce 3.0 compatibility.

= Version: 1.0.0 =
Initial Release

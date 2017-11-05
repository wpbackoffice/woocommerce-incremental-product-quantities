=== WooCommerce Advanced Product Quantities  ===
Contributors: wpbackoffice
Tags: woocommerce, product quantities, product minimum values, product maximum values, product step values, incremental product quantities, min, max
Donate Link: http://wpbackoffice.com/plugins/woocommerce-incremental-product-quantities/
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 2.1.9
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily require your customers to buy a minimum / maximum / incremental amount of products to continue with their checkout.

== Description ==

With WooCommerce Advanced Product Quantities you can easily create rules that restrict the amount of products a user can buy at once. Set Minimum, Maximum and Step values for any product type and must be valid before a customer can proceed to checkout.

New Features

* Added Custom Quantity Message Option
* Added Out of Stock Min/Max
* Added Role Support, create rules based on user roles.
* Improved performance / cacheing
* Improved admin interface
* Allows rules to have a minimum of 0

Features:

* Get started in minutes
* Set a Minimum product quantity requirement
* Set a Maximum product quantity requirement
* Sell products by a desired increment ie. by two's or the dozen
* Create product category based rules
* WooCommerce Validation rules tells users what rules they've broken directly on the cart or product page
* Set rule priority to layer multiple rules on top of each other
* Add your rule based input boxes to products thumbnails using [WooCommerce Thumbnail Input Quantities](http://wordpress.org/plugins/woocommerce-thumbnail-input-quantities/installation/)
* Easily override rules on a per-product basis
* Integrates with [WooCommerce's Product CSV Import Suite](http://www.woothemes.com/products/product-csv-import-suite/?utm_source=docs&utm_medium=docssite&utm_campaign=docs)
* See which rule is being applied to an individual product on your edit product page
* Now fully supports ALL PRODUCT TYPES, simple, variable, grouped and affiliate
* Create Site Wide rules that apply to every product unless overwritten on a per-product basis
* Create rules by Product Tags (opposed to just categories)
* Woocommerce +2.0 compatible 

[Plugin's Official Documentation and Support Page](http://www.wpbackoffice.com/plugins/woocommerce-incremental-product-quantities)

== Installation ==

Automatic WordPress Installation

1. Log-in to your WordPress Site
2. Under the plugin sidebar tab, click ‘Add New’
3. Search for ‘WooCommerce Advanced Product Quantities
4. Install and Activate the Plugin
5. Set Rules for categories by clicking the new ‘Quantity Rules’ sidebar option or assign per-product rules by using the new metabox on your product page.

Manual Installation

1. Download the latest version of the plugin from WooCommerce Advanced Product Quantities WordPress page.
3. Uncompress the file
4. Upload the uncompressed directory to ‘/wp-content/plugins/’ via FTP
5. Active the plugin from your WordPress backend ‘Plugins -> Installed Plugins’
6. Set Rules for categories by clicking the new ‘Quantity Rules’ sidebar option or assign per-product rules by using the new metabox on your product page.

== Upgrade Notice == 

= 2.1.9 = 
* Fixing munged defaults for the sitewide rules that could cause it to fail if all fields weren't populated.

= 2.1.8 = 
* Fixing an issue where rules category/tag rules wouldn't work if the site WP Site used a custom database prefix.

= 2.1.7 = 
* Adding Guest Role, improving interface for role selection, and bug when no roles were selected.

= 2.1.6 = 
* Hides message when no quantity rule is being applied.

= 2.1.5 = 
* Minor bug fix, couldn't unset max out of stock value

= 2.1.4 = 
* Upgrade fix, removed error for unset value

= 2.1.3 = 
* Added Quantity Message Options
* Added Out of Stock min/max values 
* Fixed 0 quantity appearing as 1 bug
* Minor class tweaks

= 2.1.2 = 
* Default user role bug fix.

= 2.1.1 = 
Product Page UI Update
Minor bug fixes.

= 2.1.0 = 
Added user role support for Quantity Rules, improved performance / user interface.

= 2.0.0 =
This major upgrade adds the following features - Now supports all product types, allows you to create site wide rules, and rules by product tags. It is recommended that you back up and test your site with 2.0 before going live.

== Changelog ==

= 2.1.6 = 
* Hides message when no quantity rule is being applied.

= 2.1.5 = 
* Minor bug fix, couldn't unset max out of stock value

= 2.1.4 = 
* Upgrade fix, removed error for unset value

= 2.1.3 = 
* Added Quantity Message Options
* Added Out of Stock min/max values 
* Fixed 0 quantity appearing as 1 bug
* Minor class tweaks

= 2.1.2 = 
* Default user role bug fix.

= 2.1.1 = 
* Product Page UI Update
* Minor bug fixes.

= 2.1.0 = 
* Added Role Support, create rules based on user roles.
* Improved performance / cacheing
* Improved admin interface
* Allows rules to have a minimum of 0

= 2.0.0 = 
* Updated name from WooCommerce Incremental Product Quantities to WooCommerce Advanced Product Quantities
* Now fully supports ALL PRODUCT TYPES, simple, variable, grouped and affiliate
* Create Site Wide rules that apply to every product unless overwritten on a per-product basis
* Create rules by Product Tags (opposed to just categories)
* Code reconfiguration puts everything into classes, the way it should be.

= 1.1.4 =
* Added back WC 2.0.x validation compatibility. 

= 1.1.3 =
* Minor bug fixes.

= 1.1.2 =
* Undefined variable bug fix.

= 1.1.1 =
* Fixed bug that was unsetting rule checkboxes.

= 1.1.0 =
* Updated plugin to work with WC 2.1.2 and below.
* New error response methods.
* Update validations.
* Updated comments.
* Added extra help text.

= 1.0.8 =
* Fixed division by zero error in validations.

= 1.0.7 =
* Contributor consolidation.

= 1.0.6 =
* Fixed cart bug, added additional validation so users can't enter minimum values that are less then the step value.

= 1.0.5 =
* Fixed additional bug related to missing input values and error messages on some installs. Also updated notice window. 

= 1.0.4 =
* Style sheet and link update. 
* Added potential solution for niche validation problem.  

= 1.0.3 =
* Readme.txt updates.

= 1.0.2 =
* Another small url change.

= 1.0.1 =
* Minor variable updates to account for changing directory.

= 1.0.0 =
* Initial Commit

== Screenshots ==

1. Single product page, page loads with it's minimum quantity and notifies the user below.
1. Create rule page. 
1. Single product 'Product Quantity Rules' meta box. Deactivate or override rules. Even set out of stock min/max values.
1. Single product 'Product Quantity Rules' meta box. Display of values by user role.
1. 'Advanced Rules' page, set sitewide rules and configure quantity notifications (screenshot 1)
1. Required configuration for Out of Stock quantities to be displayed.

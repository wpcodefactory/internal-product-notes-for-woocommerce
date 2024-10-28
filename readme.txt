=== Product Notes for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, karzin, omardabbas
Tags: woocommerce, product, product note, woo commerce
Requires at least: 4.4
Tested up to: 6.6
Stable tag: 3.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add notes to WooCommerce products.

== Description ==

**Product Notes for WooCommerce** plugin lets you add notes to WooCommerce products.

### &#9989; Main Features ###

* Add **multiple notes per product**.
* Add product notes **per variation**.
* Make notes **public** or **private**.
* Notes will be visible on **product edit page**.
* Display the notes on **frontend** ("private" notes will be visible to admin and shop manager only): in new **product tab**, in single **product meta** section, on multiple additional positions on **single product** page, on **shop** (e.g. category) pages.
* And more...

### &#127942; Premium Version ###

With [premium plugin version](https://wpfactory.com/item/product-notes-for-woocommerce/) you can:

* Display the notes in **admin products list** column.
* Make the notes **searchable** by admin (in backend).
* **Export** and **import** the notes.
* Display the notes in **admin orders**.
* Add the notes to **admin emails** and **customer emails** (public notes only).
* Edit the notes via **bulk** and **quick** edit.
* And more...

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/product-notes-for-woocommerce/).

### &#8505; More ###

* The plugin is **"High-Performance Order Storage (HPOS)"** compatible.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Product Notes".

== Changelog ==

= 3.0.0 - 28/10/2024 =
* Dev - Plugin settings moved to the "WPFactory" menu.
* Dev - "Recommendations" added.
* Dev - "Key Manager" added.
* Dev - Code refactoring.
* WC tested up to: 9.3.
* WooCommerce added to the "Requires Plugins" (plugin header).

= 2.9.6 - 31/07/2024 =
* WC tested up to: 9.1.
* Tested up to: 6.6.

= 2.9.5 - 08/05/2024 =
* Dev - PHP 8.2 compatibility - "Creation of dynamic property is deprecated" notice fixed.
* WC tested up to: 8.8.
* Tested up to: 6.5.

= 2.9.4 - 03/10/2023 =
* Plugin author updated.

= 2.9.3 - 26/09/2023 =
* WC tested up to: 8.1.
* Plugin icon, banner updated.

= 2.9.2 - 29/08/2023 =
* Dev – "High-Performance Order Storage (HPOS)" compatibility.
* Dev - Developers - `alg_wc_pn_do_display` filter added.
* WC tested up to: 8.0.
* Tested up to: 6.3.

= 2.9.1 - 18/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 2.9.0 - 31/08/2022 =
* Dev - Frontend Options - "Cart" option added.
* Dev - Deploy script added.
* WC tested up to: 6.8.
* Tested up to: 6.0.

= 2.8.0 - 18/05/2022 =
* Dev - Admin settings - "Advanced Options" subsection renamed to "Tools". Descriptions updated.
* Dev - Tools - Products - Delete - Code refactoring.
* Dev - Tools - "Orders" tools added ("Add" and "Delete").
* Dev - Backend Options - Admin orders - Code refactoring.
* WC tested up to: 6.5.

= 2.7.2 - 19/04/2022 =
* Dev - Backend Options - Admin products list column - CSS added.
* WC tested up to: 6.4.

= 2.7.1 - 04/04/2022 =
* Fix - "Call to undefined method `Alg_WC_Product_Notes_Emails::display_in_email()` in ..." error fixed.

= 2.7.0 - 01/04/2022 =
* Fix - Backend Options - Customer emails - The option was incorrectly marked as a non-Pro. This is fixed now.
* Dev - Backend Options - "Visual editor" and "Textarea style" options added for the variation notes.
* Dev - Advanced - "Formatting Options" subsection added (Process shortcodes, Escape HTML, Replace line breaks, Convert plaintext URI to HTML links, Notes glue, Content).
* Dev - Frontend Options - Product tab - Formating options moved to "Advanced > Formatting Options".
* Dev - Notes glue - Now defaults to `<br>` (was `PHP_EOL`) (including admin orders and shortcode).
* Dev - `content` parameter added (defaults to `%product_notes%`) (including shortcode).
* Dev - `do_shortcode` parameter added (defaults to `false`) (including shortcode).
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* WC tested up to: 6.3.

= 2.6.0 - 10/02/2022 =
* Dev - Backend Options - "Customer emails" option added (only to "Public notes").
* Dev - Advanced Options - "WPML > Use default language product ID" option added (defaults to `no`).
* WC tested up to: 6.2.
* Tested up to: 5.9.

= 2.5.2 - 27/10/2021 =
* Fix - Illegal function call in `Alg_WC_Product_Notes_Import_Export` class constructor fixed.
* Dev - Compatibility Options - "Product/Review CSV Import Export" plugin option added.
* WC tested up to: 5.8.

= 2.5.1 - 25/08/2021 =
* Dev - Backend Options - "Visual editor" option added.

= 2.5.0 - 17/08/2021 =
* Fix - Backend Options - Admin search - Properly checking if it's admin (i.e. vs frontend) search query now.
* Dev - Backend Options - Admin orders - "Save in order items meta" option added.
* Dev - General Options - "Note per variation" option added.
* Dev - Frontend Options - "Variation description" option added.
* Tested up to: 5.8.
* WC tested up to: 5.6.

= 2.4.0 - 15/07/2021 =
* Dev - "Dokan" plugin compatibility options added.
* Dev - Admin settings restyled: "Compatibility Options" subsection added, etc.
* Dev - Plugin is initialized on `plugins_loaded` action now.
* Dev - Code refactoring.
* WC tested up to: 5.5.

= 2.3.1 - 21/03/2021 =
* Dev - Sanitization - When saving, notes are sanitized as a standard `textarea` field in WooCommerce settings now (was `sanitize_textarea_field()`). This allows more HTML tags to be saved, e.g. `<img>`, etc.
* Dev - Sanitization - Settings - Frontend Options - Product tab - Tab content - Sanitized as a standard `textarea` field in WooCommerce settings now (was "raw").
* Dev - Sanitization - Settings - Frontend Options - Product tab - Notes glue - Sanitized with `wp_kses_post()` now (was "raw").
* Tested up to: 5.7.
* WC tested up to: 5.1.

= 2.3.0 - 02/03/2021 =
* Dev - Frontend Options - "Single product page" and "Shop pages" sections added.
* Dev - `alg_wc_pn_get_product_notes()` - `make_clickable` param added.
* Dev - Localisation - `load_plugin_textdomain()` function moved to the `init` action.
* Dev - Code refactoring.
* WC tested up to: 5.0.

= 2.2.1 - 05/01/2021 =
* Fix - Frontend Options - Product meta - Private notes are displayed only to admin now.
* Fix - Frontend Options - Product meta - "No break" issue fixed when both private and public notes are displayed.
* Dev - Frontend Options - "Logged users only" option added (only to "Public notes").

= 2.2.0 - 22/12/2020 =
* Fix - Backend Options - Admin orders - Properly getting product ID now (fixes the issue with the variable products).
* Fix - Backend Options - Admin orders - Properly checking if product has notes now (fixes the issue when `private` notes were incorrectly checked for the `public` notes).
* Dev - Backend Options - Admin emails - Code refactoring (`$item['product_id']` instead of `$item->get_product_id()`).
* Dev - Backend Options - "WooCommerce PDF Invoices & Packing Slips" option added.
* Dev - Frontend Options - Product tab - "Tab content" option added (defaults to `%product_notes%`).
* Dev - Frontend Options - Product tab - "Notes glue" option added (defaults to `<br>`, was `PHP_EOL`).
* Dev - Frontend Options - Product tab - "Escape HTML" option added (defaults to `yes`).
* Dev - Frontend Options - Product tab - "Replace line breaks" option added (defaults to `yes`).
* Dev - Frontend Options - Product tab - Tab title per product - Better description in meta box.
* Dev - Frontend Options - Product meta - "Position" option added (defaults to `After product meta`).
* Dev - Admin notes edit - Restyled.
* Dev - JS files minified.
* Dev - Developers - `alg_wc_pn_get_product_notes` filter added.
* Dev - Developers - `alg_wc_pn_get_product_note_values` filter added.
* Dev - Developers - `alg_wc_pn_product_tab` filter added.
* WC tested up to: 4.8.
* Tested up to: 5.6.

= 2.1.0 - 08/08/2020 =
* Dev - Export - Bug fixed.
* Dev - Backend Options - "Quick edit" and "Bulk edit" options added (both default to `no`).
* Dev - Backend Options - "Export" and "Import" options added (both default to `no`).
* Dev - Admin settings - Typo fixed.
* Dev - Code refactoring.

= 2.0.0 - 08/08/2020 =
* Dev - "Multiple notes per product" functionality added.
* Dev - "Public notes" section added.
* Dev - Frontend - "Product meta" options added.
* Dev - Frontend - Product tab - "Tab title" (including "Tab title per product") and "Tab priority" options added.
* Dev - Backend - "Admin orders" option added.
* Dev - Shortcode renamed to `[alg_wc_product_notes]`.
* Dev - Function renamed to `alg_wc_pn_get_product_notes()`.
* Dev - "Enable plugin" option removed.
* Dev - Major code refactoring.
* Dev - Admin settings descriptions updated.
* Dev - Plugin renamed to "Product Notes for WooCommerce".
* Dev - Text domain changed to `product-notes-for-woocommerce`.
* Tested up to: 5.4.
* WC tested up to: 4.3.

= 1.3.1 - 09/03/2020 =
* Dev - Bulk and Quick edit added.
* Dev - Products column - Using filter param to get product ID now (i.e. instead of `get_the_ID()`).

= 1.3.0 - 23/02/2020 =
* Dev - "Admin search" options added.
* Dev - "Products column" option added.
* Dev - "Internal Product Notes" column added to the WooCommerce products exporter.

= 1.2.1 - 20/01/2020 =
* Dev - Admin emails - Setting `$plain_text` to `false` by default now (caused an issue with some themes).
* Dev - Admin emails - Code refactoring.
* WC tested up to: 3.9.

= 1.2.0 - 20/01/2020 =
* Dev - General Options - "Admin emails" option added.
* Dev - Code refactoring.
* WC tested up to: 3.8.
* Tested up to: 5.3.

= 1.1.3 - 25/10/2019 =
* Dev - `[alg_wc_internal_product_note]` shortcode added.
* Dev - `alg_wc_get_internal_product_note()` function added.
* WC tested up to: 3.7.

= 1.1.2 - 23/07/2019 =
* Fix - "Note added" time fixed for some notes (`addslashes()` added).
* Dev - "Delete all internal notes for all products" option added.

= 1.1.1 - 10/06/2019 =
* Dev - "Product note" option added to "WooCommerce products (CSV)" importer.
* Dev - Code refactoring.
* Tested up to: 5.2.

= 1.1.0 - 02/05/2019 =
* Fix - "Note added" time now updated only if note content was changed.
* Fix - Reset Settings - "Reset section settings" option fixed.
* Dev - "Note added" time now shown only if note content is not empty.
* Dev - Product tab - Changing double line-breaks in the text into HTML paragraphs (i.e. applying `wpautop()`).
* Dev - Style Options - "Textarea style" option added.
* Dev - Admin settings restyled and descriptions updated.
* Dev - "WC tested up to" updated.
* Dev - POT file added.

= 1.0.0 - 14/03/2019 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.

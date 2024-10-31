=== (rb) AutoLogin ===
Contributors: ryanbriscall
Tags: admin, wp-admin
Requires at least: 4.0
Tested up to: 5.4.1
Stable tag: 1.0.0
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Auto login for admin.

== Description ==

Bypasses login page for admin, automatically logging you in.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rb-autologin` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Add `define('AUTO_LOGIN_USERNAME', 'admin');` to your `wp-config.php`
1. If you're using a different username, then change 'admin' to 'yourname'.

== Frequently Asked Questions ==

= How can I allow this online? =

Add `define('AUTO_LOGIN_ONLINE', true);` to your `wp-config.php`

Warning: This is not for production.  Make sure your online work is of development nature (e.g. dev.mysite.com), and/or you're using Directory Privacy or equivalent protection.

== Screenshots ==

1. Activated plug-in.
2. Add username to `wp-config.php`

== Changelog ==

= 1.0.0 =

1.0.0 - Initial release.

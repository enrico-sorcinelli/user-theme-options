=== User Theme Options ===
Contributors: Enrico Sorcinelli
Tags: Themes, Options
Requires at least: 4.4
Requires PHP: 5.2.4
Tested up to: 6.0
Stable tag: 1.1.1
License: GPLv2 or later

Allow users to use Appearance menu items.

== Description ==

Allow users to use Appearance menu items.

== Basic Features

* User settings managements.
* Multisite support.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/user-theme-options` directory, or install the plugin through the WordPress _Plugins_ screen directly.
1. Activate the plugin through the _Plugins_ screen in WordPress.

== Usage ==

Once the plugin is installed you can control settings in the following ways:

* **Theme Options** section of user edit page in Dashboard admin.
* **Theme Options** section of user edit page in Network Dashboard admin (Network setting will be overriden by site settings). 

== API ==

= Constants =

Following constants in your _wp-config.php_ file.

`USER_THEME_OPTIONS_DEBUG`

Turn on debug messages.

`USER_THEME_OPTIONS_AUTOENABLE`

Define to `false` to disable (you will have to init it manually).

`USER_THEME_OPTIONS_MANAGED_ROLES`
		
Allow to define array of managed roles. Empty array means all roles.

= Hooks =

**`user_theme_options_fix_menu`**

Action allowin to do other things after plugin menu fixes.

`do_action( 'user_theme_options_fix_menu', $user, $theme_options );`

== Frequently Asked Questions ==

= Does it work with Gutenberg? =

Yes?

= Does it work with multisite installation? =

Yes?

== Screenshots ==

1. First screenshot.
2. Second screenshot.

== Changelog ==

For plugin changelog, please see [the Releases page on GitHub](https://github.com/enrico-sorcinelli/user-theme-options/releases).


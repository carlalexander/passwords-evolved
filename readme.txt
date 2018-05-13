=== Passwords Evolved ===
Contributors: carlalexander, celsobessa
Donate link: https://paypal.me/twigpress
Author URI: https://carlalexander.ca
Plugin URI: https://github.com/carlalexander/passwords-evolved
Tags: security, authentication, bcrypt, password enforcement, leaked password, compromised password, hibp, have i been pwned
Requires at least: 4.8.0
Tested up to: 4.9.4
Requires PHP: 5.3.7
Stable tag: 1.1.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A reimagining of WordPress authentication using modern security practices.

== Description ==

The goal of this plugin is to shore up the WordPress authentication using standard security practice recommendations. At this time, the plugin improves WordPress authentication by doing the following:

= Enforcing uncompromised passwords =

This plugin prevents someone from using passwords that have appeared in data breaches. Whenever someone logs into a WordPress site, it'll verify their password using the [Have I been pwned? API](https://haveibeenpwned.com/API/v2). If their password appeared in a data breach, the plugin will prevent them from logging in until they reset their password.

By default, this level of enforcement is only done on an account that has the "[administrator](https://codex.wordpress.org/Roles_and_Capabilities#Administrator)" role. You can change which roles have their passwords enforced from the settings page. For people that have a role where there's no password enforcement, the plugin will show a warning when they log in with a compromised password.

The enforcement of uncompromised password also extends to when someone resets or changes their password. That said, in those situations, using an uncompromised password is mandatory. Someone will never be able to reset or change their password to one that's appeared in a security breach. (As long as the plugin is able to contact the API.)

= Using stronger password hashing =

The plugin also encrypts passwords using a the stronger [bcrypt](https://en.wikipedia.org/wiki/Bcrypt) hashing function. This is also the current hashing function used by PHP. While no longer the strongest hashing function, bcrypt has been around since 1999 without any significant vulnerabilities found.

You don't have to do anything to convert your password hash to bcrypt. The plugin will take care of converting it the next time that you log in after installing the plugin. If you decide to remove the plugin, your password will continue working and remain bcrypt encrypted until you reset it.

It's also worth noting that using a stronger hashing function is only important in the advent of a data breach. A stronger password hashing function makes decrypting the passwords from the data breach a lot harder to do. This combined with the enforcement of uncompromised passwords will help ensure that those passwords are never decrypted. (Or at least without significant effort.)

== Frequently Asked Questions ==

= Wait so are you sending my password to a 3rd party!? =

No, the plugin never sends your full password to a 3rd party for verification. The plugin only sends the first five characters of the [SHA-1](https://en.wikipedia.org/wiki/Sha1) hashed password to a 3rd party. The 3rd party then sends back all passwords with a hash that starts with those five characters.

The plugin then handles the rest of the password validation itself. It compares the SHA-1 hashed version of your password to the passwords returned by the 3rd party. We call this process [k-anonymity](https://en.wikipedia.org/wiki/K-anonymity). (You can read more about validating leaked passwords with it [here](https://blog.cloudflare.com/validating-leaked-passwords-with-k-anonymity/).)

== Changelog ==

= 1.1.3 =

*Released: 2018-04-29*

 * Fixed missing `settings_saved` string in English translation [carlalexander]
 * Added missing echo when translating `settings_saved` string [carlalexander]

= 1.1.2 =

*Released: 2018-03-21*

 * Added Brazilian Portuguese translation [celsobessa]
 * Reworked how the plugin handles its default translation [carlalexander]

= 1.1.1 =

*Released: 2018-03-06*

Improved how the API client and password generator handled if the API was online or not.

= 1.1.0 =

*Released: 2018-03-01*

Reworked plugin to use the new version of the HIBP API (Have I been pwned? API) which supports k-anonymity. This allows the plugin to be used in production now.

= 1.0.0 =

*Released: 2017-08-24*

Initial release
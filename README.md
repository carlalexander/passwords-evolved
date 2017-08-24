# Passwords Evolved 

[![Build Status](https://travis-ci.org/carlalexander/passwords-evolved.svg)](https://travis-ci.org/carlalexander/passwords-evolved)

## What does this plugin do?

The goal of this plugin is to shore up the WordPress authentication using standard security practice recommendations. At this time, the plugin improves WordPress authentication by doing the following:

### Enforcing uncompromised passwords

This plugin prevents someone from using passwords that have appeared in data breaches. Whenever someone logs into a WordPress site, it'll verify their password using the [Have I been pwned? API](https://haveibeenpwned.com/API/v2). If their password appeared in a data breach, the plugin will prevent them from logging in until they reset their password. 

By default, this level of enforcement is only done on an account that has the "[administrator](https://codex.wordpress.org/Roles_and_Capabilities#Administrator)" role. You can change which roles have their passwords enforced from the settings page. For people that have a role where there's no password enforcement, the plugin will show a warning when they log in with a compromised password.

The enforcement of uncompromised password also extends to when someone resets or changes their password. That said, in those situations, using an uncompromised password is mandatory. Someone will never be able to reset or change their password to one that's appeared in a security breach. (As long as the plugin is able to contact the API.)

### Using stronger password hashing

The plugin also encrypts passwords using a the stronger [bcrypt](https://en.wikipedia.org/wiki/Bcrypt) hashing function. This is also the current hashing function used by PHP. While no longer the strongest hashing function, bcrypt has been around since 1999 without any significant vulnerabilities found.

You don't have to do anything to convert your password hash to bcrypt. The plugin will take care of converting it the next time that you log in after installing the plugin. If you decide to remove the plugin, you won't be able to log in again without resetting your password.

It's also worth noting that using a stronger hashing function is only important in the advent of a data breach. A stronger password hashing function makes decrypting the passwords from the data breach a lot harder to do. This combined with the enforcement of uncompromised passwords will help ensure that those passwords are never decrypted. (Or at least without significant effort.)

## Acknowledgements

This plugin wouldn't have be possible without the awesome work of [Troy Hunt](https://www.troyhunt.com). The original work for this plugin was based on recommendations from [this post](https://www.troyhunt.com/passwords-evolved-authentication-guidance-for-the-modern-era/).

The initial inspiration for bcrypt password hashing code comes from the [roots](https://roots.io) team and their [wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt) plugin. The plugin also uses [Anthony Ferrara](http://blog.ircmaxell.com/)'s [password_compat](https://github.com/ircmaxell/password_compat) library to offer bcrypt password hashing to WordPress sites using PHP 5.3 or 5.4. 
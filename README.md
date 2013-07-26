# Simple Move Login #
Contributors: jconroy  
Tags: login   
Requires at least: 3.5  
Tested up to: 3.5  
Stable tag: 1.0  

Utility plugin (or drop-in) for changing the location of the login page.  

## Goal ##

A plugin, to help reduce risk of automated brute force / bot attacks against your WP site without taking up too additional resources, breaking plugins or confusing users.

## Description ##

A utility plugin (or can be used as a drop-in) for assisting with changing the location of the login page. For example changing `wp-login.php` to be `login.php`.

Why do this? It assists with preventing brute force attacks that target the default `wp-login.php` page. 

Please note this is more to help sysadmin and devs when there is a more servious brute force issue. You new login.php file will need to be manually updated whenever WP is updated.

## Installation ##

Installation is standard and straight forward. 

1. Duplicate and rename `wp-login.php` to your desired "new" login file name e.g. `login.php` and find and replace all instances of `wp-login.php` with the new file name e.g. `login.php`. By default `login.php` is used by the plugin.
2. Edit the your `wp-config.php` file and define the new login file name/path if `login.php` has not been used. e.g.  

```
/* Move wp-login.php */
define('SML_NEW_LOGIN_PATH', 'login.php');
```

3. Upload `simple-move-login` folder (and all it's contents!) to the `/wp-content/plugins/` directory
4. Activate the plugin through the 'Plugins' menu in WordPress

For additional protection (and performance) the `wp-login.php` can be restricted by the web server. For example the following can be used in an nginx config to deny all access:  

```
location ~ ^/(wp-login\.php) {
        deny all;   
}
```  


## Changelog ##

### 1.0 ###
* Initial release
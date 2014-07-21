=== Plugin Name ===
Contributors: danlester
Tags: google apps login, employee directory, company, directory, employee, extranet, intranet, profile, staff, google, staff directory, widget
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Search your Google Apps domain for employee info (from a widget)

== Description ==

Enable logged-in users to search your Google Apps employee directory from a widget on your intranet or client site.

Enter search text to see matching names and email addresses, along with profile photos!

**This plugin requires that you also install the free (or premium) version of the popular [Google Apps Login](http://wp-glogin.com/dirgoogleappslogin) plugin**

Setup should take fifteen minutes following our widely-praised instructions.

= Requirements =

Google Apps Directory should work for the following domains:

*  Google Apps for Business
*  Google Apps for Education
*  Google Apps for Non-profits

Google Apps Login plugin setup requires you to have admin access to any Google Apps domain, or a regular Gmail account, to register and
obtain two simple codes from Google.

To use Google Apps Directory, you will also need to register a Service Account with Google and upload details to the Google Apps Login 
plugin's settings page. This is an extra step that you don't need if you set up Google Apps Login alone, or if you use our 
[Google Drive Embedder](http://wp-glogin.com/dirgoogledriveembed) extension plugin.

= Google Apps Login =

The [Google Apps Login](http://wp-glogin.com/dirgoogleappslogin) plugin (which you must also install) 
allows existing Wordpress user accounts to login to the website 
using Google to securely authenticate their account. This means that if they are already logged into Gmail for example,
they can simply click their way through the Wordpress login screen - no username or password is explicitly required!

Full support and premium features are also available for purchase:

Eliminate the need for Google Apps domain admins to separately manage WordPress user accounts, and get peace 
of mind that only authorized employees have access to the organizations's websites and intranet.

**See [http://wp-glogin.com/](http://wp-glogin.com/?utm_source=Dir%20Readme&utm_medium=freemium&utm_campaign=Freemium)**

= Website =

Please see our website [http://wp-glogin.com/](http://wp-glogin.com/?utm_source=Dir%20Readme%20Website&utm_medium=freemium&utm_campaign=Dir) 
for more information about all our products, and to join our mailing list. 

== Screenshots ==

1. Add the widget to any widget area, then logged in users can search your Google Apps domain for employee details.
2. Configuration is through Google Apps Login plugin, including set up of a Service Account.

== Frequently Asked Questions ==

= How can I obtain support for this product? =

Please feel free to email [contact@wp-glogin.com](mailto:contact@wp-glogin.com) with any questions.

We may occasionally be able to respond to support queries posted on the 'Support' forum here on the wordpress.org
plugin page, but we recommend sending us an email instead if possible.

= What are the system requirements? =

*  PHP 5.3.x or higher
*  Wordpress 3.8 or above

== Installation ==

For Google Apps Directory to work, you will need also need to install and configure the Google Apps Login plugin 
(either before or after).

Google Apps Directory plugin:

1. Go to your WordPress admin control panel's plugin page
1. Search for 'Google Apps Directory'
1. Click Install
1. Click Activate on the plugin
1. If you do not have the correct version of Google Apps Login installed, you will see a warning notice to that effect, in
which case you should follow the instructions below

Google Apps Login plugin:

1. Go to your WordPress admin control panel's plugin page
1. Search for 'Google Apps Login'
1. Click Install
1. Click Activate on the plugin
1. Go to 'Google Apps Login' under Settings in your Wordpress admin area
1. Follow the instructions on that page to obtain two codes from Google, and also submit two URLs back to Google
1. In the Google Cloud Console, you must also enable the switch for Admin SDK access
1. You must also follow the instructions for setting up a Service Account in Settings -> Google Apps Login.

Finally, go to Appearance -> Widgets to add the Google Apps Directory to any widget area.

If you cannot install from the WordPress plugins directory for any reason, and need to install from ZIP file:

1. For Google Apps Directory plugin: Upload `googleappsdirectory` folder and contents to the `/wp-content/plugins/` directory, 
or upload the ZIP file directly in the Plugins section of your Wordpress admin
1. For Google Apps Login plugin: Upload `googleappslogin` folder and contents to the `/wp-content/plugins/` directory, 
or upload the ZIP file directly in the Plugins section of your Wordpress admin
1. Follow the instructions to configure the Google Apps Login plugin post-installation


== Changelog ==

= 1.0 =

Ready for public release

=== FlexiStatic ===
Contributors: 3UU
Tags: static page, performance
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 2.0.1
License: MIT
License URI: http://opensource.org/licenses/mit
Donate link: http://folge.link/?bitcoin=1Ritz1iUaLaxuYcXhUCoFhkVRH6GWiMTP

Flexible make real static (html) posts and pages.

== Description ==

Performance is most important for the first post/page that a visitor of 
your blog will request. Usually if the vistor like what he see the next 
pages are not so critical. But the nature of static sites implies that 
any dynamic elements of your install that reply upon Wordpress plugins 
or internal functions to operate dynamically will no longer work. So 
why do you want lost all the benefit from dynamic elements like 
captchas when making all the post/pages static? Chose only the landing 
pages and let the other posts work as prior.

FlexiStatic create real static html-sites. So you can also give the blog
page (start page) a performance boost whatever do you still use WP static
page option.

== Installation ==

1. Upload everything to the `/wp-content/plugins/` directory
2. Activate the plugin using the plugins menu in WordPress
3. use the link "flexi static" at the tools menu 

== Screenshots ==

== Frequently Asked Questions ==

= Q: Why do I need it? =
A: You do not need it! Your visitors need it ;-) The most performant way to
deliver a website is static html content. But this is not the best way for 
all single posts of your blog because you are losing the benefit of dynamic
elements like captcha checks. So the optimal solution would be only make the
landing pages / the start page static.

= Q: Does it work with a CDN? =
A: Yes. A CDN will cache the content of you blog posts. Usually the CDN ask
for changes on your server before deliver the cached content. So real static
content can speed up this process to.  

= Q: Why should I delete all static content before changing permalinks? =
A: This plugin use the permalink provided by your WP core installation to
create and check if a static version of the post/page is still stored on
your webserver. So if do you change the definition of the permalink structure 
the plugin will not find prior stored content. We are thinking about a log 
for all static URLs. But this will need additional storage in the database 
and we want try to have as small/performant footprint as possible. Perhaps we
will change this with a future version. But it is not on our agenda at this
time.

= Q: My browser start a "download" if request a static file =
A: If your permalinks are defined as directories, the webserver can not 
know the MIME type of the static file. It is up to you that your 
webserver will send the correct headers.

= Q: I get security warnings of the browser if I request a static file =
A: If your server support http and https please make sure to create the 
static files while WP has configured the WP address and website address
to the httpS:// version. BTW: Of course your server must support https 
connections to URI of your blog.

= Q: Can I make a static version of my whole WP blog to transfer it on an
punblishing server? =
A: No. There are other plugins to do this job. But this plugin was developed
with the aim to keep as most dynamic features of Wordpress and plugins as
possible.

= EXPERIMENTAL FEATURES =

Features marked as "experimental" in the admin menu are experimental! 
This means: We think it is a good extension to our plugin that we would 
like to include in a future version, but we are not sure yet about the 
best solution that works for all or most people. So please feel free to 
use and test it and report back to us about it. Experimental features 
might be removed in an update, if it does not work out. So please pay 
close attention to our changelog!

= KNOWN BUGS =

These are bugs or unexpected glitches that we know of, but that do not
have an impact on the majority of users, are not security relevant and 
will perhaps be fixed in the future - if we have time to spend or you 
provide us with a lot of "K&#xF6;lsch" ;-)

- if do you set the Blog URI to http:// but have also https:// enabled the
plugin will fetch the content from the http:// location and all references 
to elements like CSS and/or images within the HTML code are set by WP to the
unsecured protocol. It is up to you to set the configuration of you post to
the secured protocol before creating static html code.

== Changelog ==

= 2.0.1 =
- Bugfix: append index.html if the rel path is a directory

= 2.0.0 =
- Startpage now full compatible with WPMU

= 1.0.4 =
- show creation date of the static content
- added autoupdate of a static startpage on any make/remove of static
  content. This is a workarround for people how forget to recreate the
  static blogpage on new/updated posts/pages.
- bugfix: correct find real localtion of virtual subdirs

= 1.0.3 =
- Bugfix: DOCUMENT_ROOT != blog_root; dont use get_home_path() because of
  potenzial problems in chroot
- cleanup README

= 1.0.2 =
- added hints how to change the .htaccess to support some dirty coded
  plugins if the blog page becomes static
- "finished" German translation

= 1.0.1 =
- Bugfix: if post URL is within a (virtual) directory, make sure that 
  a index.html with the dynamic content is stored in the (sub)dir
- cleanup readme

= 1.0.0 =
- initial stable on https://plugins.svn.wordpress.org/flexistatic/
- code cleanup (intval - thanks to Ipstenu)
- more/better translation
- bugfix: do not make static content while saving post 
(todo: make Option in Metabox)

= 0.4.1 =
- bugfix: hold the page count when ceate/delete static blog startpage
- code cleanup (sanitize really all _REQUEST now and esc_html the output)

= 0.4.0 =
- remove static version before editing a post and add a warning
- add meta box with link to make the post static
- add translations 
- code cleanup

= 0.3.0 =
- make compatible with modPagespeed

= 0.2.0 =
- add search option
- clean up code
- add readme.txt

= 0.1.0 =
- inital version

The complete changelog can be found here: https://plugins.svn.wordpress.org/flexistatic/changelog.txt

=== Plugin Name ===
Contributors: DeBAAT
Donate link: http://www.soavis.eu
Tags: comments, spam
Requires at least: 4.1
Tested up to: 4.1
Stable tag: 0.3.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A plugin to provide a SOA visualization for WordPress sites.

== Description ==

A Service Oriented Architecture is primarily based on the definition and co-operation of services. One of the main benefits is claimed to be the re-use of existing functionality. However, due to the complexity of the environment, the insight of available functionality is often lacking. In new projects, new functionality is thus build completely new with new services. Even when there is already an existing service performing the same task.

This plugin provides a means to manage and visualise the existing service base. With the use of shortcodes, the network of service relations can be visualised. As the relations can change over time, the visualisation is dynamically generated every time it is shown on the site.

The main visualisation is provided by [GraphViz](http://www.graphviz.org/), a powerful tool for visualising network and tree structures that connect objects. It has been implemented in the [WP GraphViz](https://wordpress.org/plugins/wp-graphviz/) plugin. Hence the dependency check.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of the `wp-soavis.zip` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Define new services, projects, components, systems.
1. Use shortcodes as described in the documentation in your posts or pages.

== Frequently Asked Questions ==

= How do I use this plugin? =

Create definitions for services, components, projects, systems using the custom post type sub menu's in the WP SoaVis menu. Use the meta tag 'ServiceDependency' to define on which services this service depends.
Use the `[soavis_*]` shortcodes in the post body to show the desired information. The sub menu 'SoaVis Shortcodes' shows an overview of the shortcodes supported, including a list of parameters to be used per shortcode.
See the site for more explanations and a demo: [soavis.eu](http://www.soavis.eu).

= What is GraphViz? =

[GraphViz](http://www.graphviz.org/) is a way of generating visualisations of structural relationships between objects.
Almost any kind of diagram where something _connects_ to something else can be drawn and automatically laid out using the DOT language.

== Screenshots ==

1. The SoaVis Services sub menu page shows an overview of a number of services defined as soavis_service post_type.
2. The SoaVis Systems sub menu page shows an overview of a number of systems defined as soavis_system post_type.
3. The SoaVis Shortcode sub menu page shows an overview of the SoaVis shortcodes, including the parameters accepted.
4. A SoaVis Service can be edited like any other post. A drop down has been added to ease the use of SaoVis shortcodes.
5. A service post on the front end shows a SoaVis graph example.
6. A system post on the front end showing a different SoaVis graph.

== Changelog ==

= 0.3.0 =
* Fixed version of stable tag.
* Added setting for traverse network level.

= 0.2.0 =
* Fixed SoaVis Chain type.
* Fixed generation of node url.
* Added DebugMP support to public class.
* Added support for Demo by Gravity Forms.
* Added support for category by SoaVis post_types.

= 0.1.0 =
* First version starting the plugin.

== Upgrade Notice ==

= 0.3.0 =
* Fixed some small issues and added some small functionality, see changelog.

= 0.2.0 =
* Added support for Demo by Gravity Forms.
* Fixed some small issues, see changelog.

= 0.1.0 =
As this is the first version, there is no upgrade info yet.

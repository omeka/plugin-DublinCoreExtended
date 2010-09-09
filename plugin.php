<?php
/**
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package DublinCoreExtended
 */

require_once 'DublinCoreExtendedPlugin.php';

add_plugin_hook('install', 'DublinCoreExtendedPlugin::install');
add_plugin_hook('uninstall', 'DublinCoreExtendedPlugin::uninstall');
add_plugin_hook('upgrade', 'DublinCoreExtendedPlugin::upgrade');
add_plugin_hook('admin_append_to_plugin_uninstall_message', 'DublinCoreExtendedPlugin::adminAppendToPluginUninstallMessage');

add_filter('define_response_contexts', 'DublinCoreExtendedPlugin::defineResponseContexts');
add_filter('define_action_contexts', 'DublinCoreExtendedPlugin::defineActionContexts');

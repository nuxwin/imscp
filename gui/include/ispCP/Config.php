<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @version 	SVN: $Id$
 * @link 		http://isp-control.net
 * @author 		ispCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "ispCP - ISP Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 */

/**
 * This class wraps the creation and manipulation of the ispCP_ConfigHandler
 * objects
 *
 * Important consideration:
 *
 * This class implement the Singleton design pattern, so, each type of
 * {@link IspCP_ConfigHandler} objects are instanciated only once.
 *
 * If you want use several instances of an IspCP_ConfigHandler object (e.g: To
 * handle separate configuration parameters that are stored in another container
 * such as a configuration file linked to a specific plugin) you should not use
 * this class. Instead of this, register your own IspCP_ConfigHandler objects
 * into the ispCP_Registry object to be able to use them from all contexts.
 *
 * Example:
 *
 * $parameters = array('PLUGIN_NAME' => 'billing', 'PLUGIN_VERSION' => '1.0.0');
 * IspCP_Registry::set('My_ConfigHandler', new IspCP_ConfigHandler($parameters));
 *
 * From another context:
 * 
 * $my_cfg = IspCP_Registry::get('My_ConfigHandler');
 * echo $my_cfg->PLUGIN_NAME; // billing
 * echo $my_cfg->PLUGIN_VERSION; // 1.0.0
 *
 * See {@link IspCP_Registry} for more information.
 *
 * To resume, the Config class acts as a registry for the IspCP_ConfigHandler
 * objects where the registered values (that are IspCP_ConfigHandler objects)
 * are indexed by they class name.
 * 
 * @version 1.0.5
 */
final class Config {

	/**
	 * List of all the IspCP_ConfigHandler objects that this class can handle
	 */
	const
		ARR = 'ispCP_ConfigHandler',
		DB = 'ispCP_ConfigHandler_Db',
		FILE = 'ispCP_ConfigHandler_File',
		INI = false,
		XML = false,
		YAML = false;

	/**
	 * Array that contain references to {@link ispCP_ConfigHandler} objects
	 * indexed by they class name
	 *
	 * @staticvar array
	 */
	private static $_instances = array();

	/**
	 * Get a IspCP_ConfigHandler instance
	 *
	 * Returns a reference to a {@link ispCP_ConfigHandler} instance, only
	 * creating it if it doesn't already exist.
	 *
	 * The default handler object is set to {@link ispCP_ConfigHandler_File}
	 *
	 * @param string $className IspCP_ConfigHandler class name
	 * @param mixed $params Parameters that are passed to IspCP_ConfigHandler
	 * 	object constructor
	 * @throws Exception
	 * @return IspCP_ConfigHandler
	 */
	public static function getInstance($className = self::FILE, $params = null) {

		if(!array_key_exists($className, self::$_instances)) {

			if($className === false) {
				throw new Exception(
					'The IspCP_ConfigHandler object you trying to use is not ' .
						'yet implemented!'
				);
			} elseif (!class_exists($className, true)) {
				throw new Exception(
					"The class $className is not reachable!"
				);
    		} elseif (!is_subclass_of($className, 'IspCP_ConfigHandler')) {
				throw new Exception(
					'Only IspCP_ConfigHandler objects can be handling by the ' .
						__CLASS__ . ' class!'
				);
			}

			self::$_instances[$className] = new $className($params);
		}

		return self::$_instances[$className];
	}

	/**
	 * Wrapper for getter method of an IspCP_ConfigHandler object
	 *
	 * @see ispCP_ConfigHandler::get()
	 * @param string $index Configuration parameter key name
	 * @param string $className IspCP_ConfigHandler class name
	 * @return Configuration parameter value
	 */
	public static function get($index, $className = self::FILE) {

		return self::getInstance($className)->get($index);
	}

	/**
	 * Wrapper for setter method of an ispCP_ConfigHandler object
	 *
	 * @see ispCP_ConfigHandler::set()
	 * @param string $index Configuration parameter key name
	 * @param mixed $value Configuration parameter value
	 * @param string $className ispCP_ConfigHandler class name
	 * @return void
	 */
	public static function set($index, $value, $className = self::FILE) {

		self::getInstance($className)->set($index, $value);
	}

	/**
	 * Wrapper for {@link ispCP_ConfigHandler::del()} method
	 *
	 * @see ispCP_ConfigHandler::del()
	 * @param string $index Configuration parameter key name
	 * @param string $className IspCP_ConfigHandler class name
	 * @return void
	 */
	public static function del($index, $className = self::FILE) {

		self::getInstance($className)->del($index);
	}
}

<?php
/**
*
* @package Convert Local Url To Link Name
* @copyright (c) 2014 HAMMER663
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace hammer663\ConvertLocalUrlToLinkName\migrations\v0xx;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['clutln_version']) && version_compare($this->config['clutln_version'], '0.0.1', '>=');
	}

	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			// Add configs
			array('config.add', array('clutln_enable', '1')),
			array('config.add', array('clutln_allow_rules', '1')),
			array('config.add', array('clutln_allow_wiki', '1')),
			array('config.add', array('clutln_allow_faq', '1')),
			// Current version
			array('config.add', array('clutln_version', '0.0.1')),

			// Add ACP modules
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_CONVERT_LOCAL_URL')),
			array('module.add', array('acp', 'ACP_CONVERT_LOCAL_URL', array(
					'module_basename'	=> 'hammer663\ConvertLocalUrlToLinkName\acp\convert_local_url_module',
					'module_langname'	=> 'ACP_CONVERT_LOCAL_URL_EXPLAIN',
					'module_mode'		=> 'config_clutln',
					'module_auth'		=> 'ext_hammer663/ConvertLocalUrlToLinkName && acl_a_clutln',
			))),

			// Add permissions
			array('permission.add', array('a_clutln', true)),

			// Set permissions
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_clutln')),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_clutln')),
		);
	}
}

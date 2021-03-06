<?php
/**
 * PHP Server Monitor
 * Monitor your servers and websites.
 *
 * This file is part of PHP Server Monitor.
 * PHP Server Monitor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHP Server Monitor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHP Server Monitor.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     phpservermon
 * @author      Pepijn Over <pep@neanderthal-technology.com>
 * @copyright   Copyright (c) 2008-2014 Pepijn Over <pep@neanderthal-technology.com>
 * @license     http://www.gnu.org/licenses/gpl.txt GNU GPL v3
 * @version     Release: @package_version@
 * @link        http://www.phpservermonitor.org/
 * @since		phpservermon 2.2.0
 **/

namespace psm\Module\Server\Controller;
use psm\Module\AbstractController;
use psm\Service\Database;
use psm\Service\Template;

abstract class AbstractServerController extends AbstractController {

	function __construct(Database $db, Template $tpl) {
		parent::__construct($db, $tpl);
	}

	/**
	 * Get all servers for the current user
	 * @return array
	 */
	public function getServers() {
		$sql_join = '';

		if($this->user != null && $this->user->getUserLevel() > PSM_USER_ADMIN) {
			// restrict by user_id
			$sql_join = "JOIN `".PSM_DB_PREFIX."users_servers` AS `us` ON (
						`us`.`user_id`={$this->user->getUserId()}
						AND `us`.`server_id`=`s`.`server_id`
						)";
		}

		$sql = "SELECT
					`s`.`server_id`,
					`s`.`ip`,
					`s`.`port`,
					`s`.`type`,
					`s`.`label`,
					`s`.`pattern`,
					`s`.`status`,
					`s`.`error`,
					`s`.`rtime`,
					IF(`s`.`last_check`='0000-00-00 00:00:00', 'never', DATE_FORMAT(`s`.`last_check`, '%d-%m-%y %H:%i')) AS `last_check`,
					IF(`s`.`last_online`='0000-00-00 00:00:00', 'never', DATE_FORMAT(`s`.`last_online`, '%d-%m-%y %H:%i')) AS `last_online`,
					`s`.`active`,
					`s`.`email`,
					`s`.`sms`
				FROM `".PSM_DB_PREFIX."servers` AS `s`
				{$sql_join}
				ORDER BY `active` ASC, `status` DESC, `type` ASC, `label` ASC";
		$servers = $this->db->query($sql);

		return $servers;

	}
}
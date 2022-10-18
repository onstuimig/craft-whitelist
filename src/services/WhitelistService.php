<?php
namespace onstuimig\whitelist\services;

use craft\base\Component;
use onstuimig\whitelist\helpers\IpHelper;
use onstuimig\whitelist\Whitelist;

class WhitelistService extends Component
{
	/**
	 * Check IP address against whitelist
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function ipOnWhitelist(string $ip): bool
	{
		$settings = Whitelist::getInstance()->getSettings();

		// If plugin is disabled, ignore whitelist funcionality
		if(!$settings->enabled) {
			return true;
		}
		
		return IpHelper::ipInCidrList($ip, $settings->whitelist);
	}
}

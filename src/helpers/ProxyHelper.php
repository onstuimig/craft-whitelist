<?php
namespace onstuimig\whitelist\helpers;

use Craft;
use yii\validators\IpValidator;

class ProxyHelper
{
	public static function getCurrentIp($forceRemote = false)
	{
		$request = Craft::$app->getRequest();

		if($request->getIsConsoleRequest()) {
			return null;
		}

		$remoteIp = $request->getRemoteIP();

		if(empty($remoteIp)) {
			return null;
		}

		if($forceRemote) {
			return $remoteIp;
		}

		$ipHeaders = self::_trustedIpHeaders();

		foreach($ipHeaders as $ipHeader) {
			$clientIp = self::_getIpFromHeader($ipHeader);

			if(empty($clientIp)) {
				continue;
			}

			return $clientIp;
		}

		return $remoteIp;
	}

	private static function _trustedIpHeaders()
	{
		$request = Craft::$app->getRequest();

		$ipValidator = new IpValidator();

		$trustedIpHeaders = [];

		$trustedHeaders = [];
		foreach(self::_getExplicitlyTrustedHosts() as $ipRange => $headers) {
			$ipValidator->setRanges($ipRange);

			if($ipValidator->validate($request->getRemoteIP())) {
				$trustedHeaders = $headers;
				break;
			}
		}

		$trustedHeaders = array_map('trim', $trustedHeaders);
		$trustedHeaders = array_map('mb_strtolower', $trustedHeaders);

		foreach($request->ipHeaders as $ipHeader) {
			$ipHeader = trim($ipHeader);
			$ipHeader = mb_strtolower($ipHeader);

			if(!in_array($ipHeader, $trustedHeaders, true)) {
				continue;
			}

			$trustedIpHeaders[] = $ipHeader;
		}

		return $trustedIpHeaders;
	}

	private static function _getIpFromHeader($header)
	{
		$request = Craft::$app->getRequest();

		$headerValue = $request->getHeaders()->get($header);

		if(!is_string($headerValue)) {
			return null;
		}

		$headerValue = trim($headerValue);

		$ips = explode(',', $headerValue);

		$ips = array_map('trim', $ips);
		$ips = array_filter($ips);

		$ips = array_reverse($ips);

		$trustedIpRanges = array_keys(self::_getExplicitlyTrustedHosts());

		$clientIp = null;

		$ipValidator = new IpValidator();

		foreach($ips as $ip) {
			if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
				break;
			}

			$clientIp = $ip;

			if(empty($trustedIpRanges)) {
				break;
			}

			$ipValidator->setRanges($trustedIpRanges);

			if(!$ipValidator->validate($ip)) {
				break;
			}
		}

		return $clientIp;
	}

	private static function _getExplicitlyTrustedHosts()
	{
		$request = Craft::$app->getRequest();

		$trustedHosts = [];
		foreach($request->trustedHosts as $ipRange => $headers) {
			if(!is_array($headers)) {
				$ipRange = $headers;
				$headers = $request->secureHeaders;
			}

			if(!is_string($ipRange) || !is_array($headers)) {
				continue;
			}

			$ipRange = trim($ipRange);
			$ipRange = mb_strtolower($ipRange);

			// Don't trust ranges that include every IP
			if(in_array($ipRange, [
				'*',
				'any',
				'ipv4',
				'ipv6',
				'0.0.0.0/0',
				'::/0'
			], true)) {
				continue;
			}

			$trustedHosts[$ipRange] = $headers;
		}

		return $trustedHosts;
	}
}

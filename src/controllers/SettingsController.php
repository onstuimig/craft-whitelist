<?php
namespace onstuimig\whitelist\controllers;

use craft\web\Controller;
use onstuimig\whitelist\models\Ip;
use onstuimig\whitelist\records\Ip as IpRecord;
use onstuimig\whitelist\Whitelist;

class SettingsController extends Controller
{

	/**
	 * Save settings
	 *
	 * @return mixed
	 */
	public function actionSave()
    {
		$this->requireCpRequest();

		$this->requirePermission('whitelist:settings');
		$this->requirePostRequest();

		$whitelist = \Craft::$app->request->getBodyParam('whitelist');
		$whitelistFrontend = \Craft::$app->request->getBodyParam('whitelistFrontend');

		if(empty($whitelist)) {
			$whitelist = [];
		}
		if(empty($whitelistFrontend)) {
			$whitelistFrontend = [];
		}

		/** @var Ip[] */
		$ipModels = [];

		foreach ($whitelist as $key => $item) {
			$ipModel = $this->ipToModel($item, false);
			if($ipModel !== null) {
				$ipModels[] = $ipModel;
			}
		}

		foreach ($whitelistFrontend as $key => $item) {
			$ipModel = $this->ipToModel($item, true);
			if($ipModel !== null) {
				$ipModels[] = $ipModel;
			}
		}

		Whitelist::getInstance()->whitelistService->clearAll();
		Whitelist::getInstance()->whitelistService->bulkAdd($ipModels);
	}

	/**
	 * Save bulk settings
	 *
	 * @return mixed
	 */
	public function actionSaveBulk()
    {
		$this->requireCpRequest();

		$this->requirePermission('whitelist:settings');
		$this->requirePostRequest();

		$whitelistBulk = \Craft::$app->request->getBodyParam('whitelistBulk');
		$whitelistBulkFrontend = \Craft::$app->request->getBodyParam('whitelistBulkFrontend');

		$ips = explode(PHP_EOL, $whitelistBulk);
		$ipsFrontend = explode(PHP_EOL, $whitelistBulkFrontend);
		
		/** @var Ip[] */
		$ipModels = [];
		foreach ($ips as $ip) {
			$ipModel = $this->bulkIpToModel($ip, false);
			if($ipModel !== null) {
				$ipModels[] = $ipModel;
			}
		}
		foreach ($ipsFrontend as $ip) {
			$ipModel = $this->bulkIpToModel($ip, true);
			if($ipModel !== null) {
				$ipModels[] = $ipModel;
			}
		}

		Whitelist::getInstance()->whitelistService->clearAll();
		Whitelist::getInstance()->whitelistService->bulkAdd($ipModels);
	}

	
	/**
	 * Convert IP item from settings to model
	 *
	 * @param array $ipItem
	 * @param ?boolean $frontendOnly
	 * @return ?Ip
	 */
	private function ipToModel($ipItem, $frontendOnly = false)
	{
		$ip = trim($ipItem['ip']);

		if(empty($ip))
			return null;

		$ipModel = new Ip([
			'ip'			=> $ip,
			'comment'		=> trim($ipItem['comment']) ?: null,
			'frontendOnly'	=> $frontendOnly
		]);

		return $ipModel;
	}

	/**
	 * Convert IP line from bulk settings to model
	 * 
	 * @param string $ipLine
	 * @param ?boolean $frontendOnly
	 * @return ?Ip
	 */
	private function bulkIpToModel($ipLine, $frontendOnly = false)
	{
		$ip = trim($ipLine);

		if(empty($ip))
			return null;
		
		$ipObject = explode('#', $ip, 2);

		$ip = trim($ipObject[0]);
		if(empty($ip))
			return null;
		
		$comment = null;
		if(!empty($ipObject[1]))
			$comment = trim($ipObject[1]);

		$ipModel = new Ip([
			'ip'		=> $ip,
			'comment'	=> $comment,
			'frontendOnly'	=> $frontendOnly
		]);

		return $ipModel;
	}
}

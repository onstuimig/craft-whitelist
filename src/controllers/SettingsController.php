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

		if(empty($whitelist)) {
			$whitelist = [];
		}

		/** @var Ip[] */
		$ipModels = [];

		foreach ($whitelist as $key => $item) {
			$ip = trim($item['ip']);

			if(empty($ip))
				continue;
			
			// $id = null;
			// if(substr( $key, 0, 3 ) === "id-") {
			// 	$id = (int) substr( $key, 3 );
			// }

			// if(empty($id)) {
			// 	$ipRecord = new IpRecord();
			// }else {
			// 	/** @var IpRecord|null */
			// 	$ipRecord =  IpRecord::find()->where(['id' => $id])->one();

			// 	if(empty($ipRecord))
			// 		continue;
			// }

			// $ipRecord->ip = $item['ip'];
			// $ipRecord->comment = $item['comment'];

			// $ipRecord->save();

			$ipModel = new Ip([
				'ip'		=> $ip,
				'comment'	=> trim($item['comment']) ?: null
			]);
			
			$ipModels[] = $ipModel;
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

		$ips = explode(PHP_EOL, $whitelistBulk);
		
		/** @var Ip[] */
		$ipModels = [];
		foreach ($ips as $ip) {
			$ip = trim($ip);

			if(empty($ip))
				continue;
			
			$ipObject = explode('#', $ip, 2);

			$ip = trim($ipObject[0]);
			if(empty($ip))
				continue;
			
			$comment = null;
			if(!empty($ipObject[1]))
				$comment = trim($ipObject[1]);

			$ipModel = new Ip([
				'ip'		=> $ip,
				'comment'	=> $comment
			]);
			
			$ipModels[] = $ipModel;
		}

		Whitelist::getInstance()->whitelistService->clearAll();
		Whitelist::getInstance()->whitelistService->bulkAdd($ipModels);
	}
}

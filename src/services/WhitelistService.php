<?php
namespace onstuimig\whitelist\services;

use Craft;
use craft\base\Component;
use onstuimig\whitelist\helpers\IpHelper;
use onstuimig\whitelist\Whitelist;
use onstuimig\whitelist\models\Ip as IpModel;
use onstuimig\whitelist\records\Ip as IpRecord;

class WhitelistService extends Component
{
	private $skipPermissionCheck = false;

	/**
	 * Check IP address against whitelist
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function ipOnWhitelist(string $ip, bool $includeFrontend = false): bool
	{
		$this->skipPermissionCheck = true;
		$ips = $this->getIps(false);
		if($includeFrontend) {
			$ips = array_merge($ips, $this->getIps(true));
		}
		$this->skipPermissionCheck = false;

		$whitelist = array_column($ips, 'ip');
		
		return IpHelper::ipInCidrList($ip, $whitelist);
	}

	/**
	 * Get all ips in whitelist
	 *
	 * @param bool $includeFrontend
	 * @return IpModel[]
	 */
	public function getIps($frontend = false): array
	{
		if(!$this->skipPermissionCheck && !Craft::$app->user->checkPermission('whitelist:settings')) {
			throw new \Exception('You do not have permission to view the whitelist');
		}

		$settings = Whitelist::getInstance()->getSettings();

		// First check for overrides in plugin settings
		$whitelistOverrides = $frontend ? $settings->whitelistFrontend : $settings->whitelist;
		if(!empty($whitelistOverrides) && !empty(implode('', $whitelistOverrides))) {
			$ips = [];
			foreach ($whitelistOverrides as $ip) {
				$ips[] = new IpModel(['ip' => $ip, 'comment' => null, 'frontendOnly' => $frontend]);
			}

			return $ips;
		}

		/** @var IpRecord[] */
		$ipRecords = IpRecord::find()->where(['frontendOnly' => $frontend])->all();

		$ips = [];
		foreach ($ipRecords as $ipRecord) {
			$ips[] = new IpModel($ipRecord->toArray());
		}

		return $ips;
	}

	/**
	 * Add ip to whitelist
	 *
	 * @param string $ip
	 * @param string|null $comment
	 * @return void
	 */
	public function add(string $ip, ?string $comment = null): void
	{
		if(!Craft::$app->user->checkPermission('whitelist:settings')) {
			throw new \Exception('You do not have permission to add to the whitelist');
		}

		$ipItem = new IpModel(['ip' => $ip, 'comment' => $comment]);
		$ipItemRecord = new IpRecord($ipItem);

		$ipItemRecord->save();
	}

	/**
	 * Bulk add ips to whitelist
	 *
	 * @param IpModel[] $ipModels
	 * @return void
	 */
	public function bulkAdd(array $ipModels): void
	{
		if(!Craft::$app->user->checkPermission('whitelist:settings')) {
			throw new \Exception('You do not have permission to add to the whitelist');
		}

		$insertRows = [];
		foreach ($ipModels as $ipModel) {
			$insertRows[] = [$ipModel->ip, $ipModel->comment ?? null, $ipModel->frontendOnly ?? false];
		}
			
		$query = Craft::$app->getDb()->createCommand()
			->batchInsert(IpRecord::tableName(), ['ip', 'comment', 'frontendOnly'], $insertRows);

		$sql = $query->getRawSql();
		$sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);

		$query->setRawSql($sql);

		$query->execute();
	}

	/**
	 * Clear all ips in whitelist
	 *
	 * @return void
	 */
	public function clearAll(): void
	{
		if(!Craft::$app->user->checkPermission('whitelist:settings')) {
			throw new \Exception('You do not have permission to clear the whitelist');
		}

		IpRecord::deleteAll();
	}
}

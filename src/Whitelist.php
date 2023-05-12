<?php
namespace onstuimig\whitelist;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use craft\web\Request;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use onstuimig\whitelist\models\Settings;
use onstuimig\whitelist\services\WhitelistService;
use yii\base\Event;
use yii\web\HttpException;

/**
 * Whitelist
 * 
 * @author Whitelist
 * @package Whitelist
 * 
 * @property WhitelistService $whitelistService
 * @property Settings $settings
 * @method Settings getSettings()
 */
class Whitelist extends Plugin
{
	public function init()
	{
		parent::init();

		$this->setComponents([
			'whitelistService' => WhitelistService::class,
		]);

		/** @var Request */
		$request = Craft::$app->getRequest();

		// Check for CP request
		if ($request->isCpRequest) {
			$ip = $request->getRemoteIP();
			if(!$this->whitelistService->ipOnWhitelist($ip)) {
				throw new HttpException(403, Craft::t('whitelist', 'Your IP is not allowed to access the control panel. Please contact your site administrator to add this IP to the whitelist: ') . $ip);
			}
		}

		Event::on(
			UserPermissions::class,
			UserPermissions::EVENT_REGISTER_PERMISSIONS,
			function(RegisterUserPermissionsEvent $event) {
				$event->permissions[Craft::t('whitelist', 'Whitelist')] = [
					'whitelist:settings' => [
						'label' => Craft::t('whitelist', 'Settings'),
					]
				];
			}
		);

		Event::on(
			Cp::class,
			Cp::EVENT_REGISTER_CP_NAV_ITEMS,
			function(RegisterCpNavItemsEvent $event) {
				if (Craft::$app->user->checkPermission('whitelist:settings')) {
					$event->navItems[] = [
						'url' => 'whitelist',
						'label' => Craft::t('whitelist', 'Whitelist'),
						'icon' => '@onstuimig/whitelist/icon-mask.svg',
					];
				}
			}
		);

		Event::on(
			CraftVariable::class,
			CraftVariable::EVENT_INIT,
			function(Event $e) {
				/** @var CraftVariable $variable */
				$variable = $e->sender;
				
				$variable->set('whitelist', WhitelistService::class);
			}
		);

		Craft::info(
            Craft::t(
                'whitelist',
                '{name} plugin loaded',
                [ 'name' => $this->name ]
            ),
            __METHOD__
        );
	}

	public function beforeSaveSettings(): bool
	{
		if(!is_array($this->settings->whitelist)) {
			$this->settings->whitelist = array_map('trim', explode(PHP_EOL, $this->settings->whitelist));
		}
		
		return parent::beforeSaveSettings();
	}
	
	protected function createSettingsModel(): ?Model
	{
		return new Settings();
	}

	protected function settingsHtml(): ?string
    {
        return \Craft::$app->getView()->renderTemplate(
            'whitelist/settings',
            [ 'settings' => $this->getSettings() ]
        );
    }
}

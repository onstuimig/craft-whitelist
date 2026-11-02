<?php
namespace onstuimig\whitelist\models;

use craft\base\Model;

class Settings extends Model
{
	/**
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * @var bool
	 */
	public $enabledFrontend = false;
	
	/**
	 * @var string[]
	 */
	public $whitelist = [];

	/**
	 * @var bool
	 */
	public $useRemoteIp = false;


	public function rules(): array
	{
		return [
			[['enabled', 'enabledFrontend', 'useRemoteIp'], 'boolean'],
		];
	}

}

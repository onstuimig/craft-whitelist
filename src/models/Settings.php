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
	 * @var string[]
	 */
	public $whitelist = [];

	public function rules(): array
	{
		return [
			[['enabled'], 'boolean'],
		];
	}

}

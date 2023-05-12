<?php
namespace onstuimig\whitelist\records;

use craft\db\ActiveRecord;

/**
 * Class Ip record
 * 
 * @property int $id ID
 * @property string $ip IP address
 * @property string $comment Comment
 */
class Ip extends ActiveRecord
{
	/**
	 * @inheritdoc
	 * @return string
	 */
	public static function tableName(): string
	{
		return '{{%whitelist_ips}}';
	}
}

<?php

namespace onstuimig\whitelist\models;

use craft\base\Model;

class Ip extends Model
{
	/**
	 * @var int|null ID
	 */
	public $id;

	/**
	 * @var \DateTime The date that the item was created
	 */
	public $dateCreated;

	/**
	 * @var \DateTime The date that the item was created
	 */
	public $dateUpdated;

	/**
	 * @var string|null UID
	 */
	public $uid;

	/**
	 * @var string The ip to be whitelisted
	 */
	public $ip;

	/**
	 * @var string A comment about the ip
	 */
	public $comment;

}

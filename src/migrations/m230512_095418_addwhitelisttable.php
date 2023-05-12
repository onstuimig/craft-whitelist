<?php

namespace onstuimig\whitelist\migrations;

use Craft;
use craft\db\Migration;

/**
 * m230512_095418_addwhitelisttable migration.
 */
class m230512_095418_addwhitelisttable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
		if (!$this->db->tableExists('{{%whitelist_ips}}')) {
			// create the cache queue table
			$this->createTable('{{%whitelist_ips}}', [
				'id' => $this->primaryKey(11),
				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
				'ip' => $this->string(64)->notNull()->unique(),
				'comment' => $this->string(255),
			]);
		}

		Craft::$app->db->schema->refresh();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%whitelist_ips}}');
    }
}

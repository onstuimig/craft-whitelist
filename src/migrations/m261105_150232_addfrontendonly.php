<?php

namespace onstuimig\whitelist\migrations;

use Craft;
use craft\db\Migration;

/**
 * m261105_150232_addfrontendonly migration.
 */
class m261105_150232_addfrontendonly extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%whitelist_ips}}', 'frontendOnly', $this->boolean()->notNull()->defaultValue(false));

		Craft::$app->db->schema->refresh();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn( '{{%whitelist_ips}}', 'frontendOnly');
    }
}

<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2021, Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OCA\WebPush\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\DB\Types;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Recreate notifications_pushtoken(s) with a primary key for cluster support
 */
class Version000001Date20230506151500 extends SimpleMigrationStep {
	/** @var IDBConnection */
	protected $connection;

	public function __construct(IDBConnection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('webpush_subscriptions')) {
			$table = $schema->createTable('webpush_subscriptions');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,				
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('identifier', Types::STRING, [
				'notnull' => true,
				'length' => 128,
			]);
			$table->addColumn('subscription', Types::STRING, [
				'notnull' => true,
				'length' => 512,
			]);
			$table->addColumn('subscription_endpoint', Types::STRING, [
				'notnull' => true,
				'length' => 128,
			]);
			$table->addColumn('subscription_keys_p256dh', Types::STRING, [
				'notnull' => true,
				'length' => 128,
			]);
			$table->addColumn('subscription_keys_auth', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('subscription_expiration_time', 'integer', [
				'notnull' => false,
			]);
			$table->addColumn('crt_user_id', 'string', [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('crt_date', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('last_update', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('is_archived', 'boolean', [
				'notnull' => false,
			]);
			$table->addColumn('update_user_id', 'string', [
			'notnull' => false,
			'length' => 64,
			]);
			$table->addColumn('etag', 'string', [
				'notnull' => false,
				'length' => 32,
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'identifier'], 'oc_webpush_unq');
		}
		return $schema;
	}

}

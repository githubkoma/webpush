<?php
namespace OCA\WebPush\Model;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class WebPushSubscriptionMapper extends QBMapper {
	// table was created here: https://github.com/nextcloud/notifications/blob/master/lib/Migration/Version2010Date20210218082811.php
    // and see here: https://github.com/nextcloud/notifications/issues/1225#issuecomment-1384488771

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'webpush_subscriptions', WebPushSubscription::class);
    }

    // inherited methods: insert, delete, ...

	public function find(int $id) {
        $qb = $this->db->getQueryBuilder();

                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
									$qb->expr()->eq('id', $qb->createNamedParameter($id))
           );
        
        return $this->findEntity($qb);
    }	

	public function findSubscription(string $subscription) {
        $qb = $this->db->getQueryBuilder();

                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
									$qb->expr()->eq('subscription', $qb->createNamedParameter($subscription))
           );
        
        return $this->findEntity($qb);
    }

	public function findAllByUser(string $userid) {
        $qb = $this->db->getQueryBuilder();

                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
									$qb->expr()->eq('user_id', $qb->createNamedParameter($userid))
           );
        
        return $this->findEntities($qb);
    }

	public function deleteAllSubscriptions() {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName());
        
        return $qb->executeStatement();
    }

	// inherited methods: insert, delete, ...

}
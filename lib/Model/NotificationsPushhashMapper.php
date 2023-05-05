<?php
namespace OCA\WebPush\Model;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class NotificationsPushhashMapper extends QBMapper {
	// table was created here: https://github.com/nextcloud/notifications/blob/master/lib/Migration/Version2010Date20210218082811.php
    // and see here: https://github.com/nextcloud/notifications/issues/1225#issuecomment-1384488771

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'notifications_pushhash', NotificationsPushhash::class);
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

	public function findBySubscription(string $subscription) {
        $qb = $this->db->getQueryBuilder();

                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
									$qb->expr()->eq('devicepublickey', $qb->createNamedParameter($subscription))
           );
        
        return $this->findEntity($qb);
    }

	public function findAllByUserAndApptype(string $userid, string $apptype) {
        $qb = $this->db->getQueryBuilder();

                    $qb->select('*')
                             ->from($this->getTableName())
                             ->where(
									$qb->expr()->eq('uid', $qb->createNamedParameter($userid))
                             )->andWhere(
                                $qb->expr()->eq('apptype', $qb->createNamedParameter($apptype))
           );
        
        return $this->findEntities($qb);
    }

	// inherited methods: insert, delete, ...

}
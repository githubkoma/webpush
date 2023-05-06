<?php
namespace OCA\WebPush\Model;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class WebPushSubscription extends Entity implements JsonSerializable {

	// protected $id // <- inherited from 'Entity'
    protected $userId;
    protected $identifier;    
    protected $subscription;    
    protected $subscriptionEndpoint;    
    protected $subscriptionKeysP256dh; 
    protected $subscriptionKeysAuth; 
    protected $subscriptionExpirationTime; 
    protected $crtUserId; 
    protected $crtDate; 
    protected $lastUpdate; 
    protected $isArchived; 
    protected $updateUserId; 
    protected $etag; 

    public function jsonSerialize(): Array {
        return [
            'id' => $this->id, 
            'userId' => $this->userId,
			'identifier' => $this->identifier,
            'subscription' => $this->subscription,            
            'subscriptionEndpoint' => $this->subscriptionEndpoint,            
            'subscriptionKeysP256dh' => $this->subscriptionKeysP256dh,    
            'subscriptionKeysAuth' => $this->subscriptionKeysAuth,    
            'subscriptionExpirationTime' => $this->subscriptionExpirationTime,    
            'crtUserId' => $this->crtUserId,    
            'crtDate' => $this->crtDate, 
            'lastUpdate' => $this->lastUpdate,                
            'isArchived' => $this->isArchived,
            'updateUserId' => $this->updateUserId,
            'etag' => $this->etag,
        ];
    }
}

<?php
namespace OCA\WebPush\Model;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class NotificationsPushhash extends Entity implements JsonSerializable {

	// protected $id // <- inherited from 'Entity'
    protected $uid;
    protected $token;    
    protected $deviceidentifier;    
    protected $devicepublickey;    
    protected $devicepublickeyhash; 
    protected $pushtokenhash; 
    protected $proxyserver; 
    protected $apptype; 

    public function jsonSerialize(): Array {
        return [
            'id' => $this->id, 
            'uid' => $this->uid,
			'token' => $this->token,
            'deviceidentifier' => $this->deviceidentifier,            
            'devicepublickey' => $this->devicepublickey,            
            'devicepublickeyhash' => $this->devicepublickeyhash,    
            'pushtokenhash' => $this->pushtokenhash,    
            'proxyserver' => $this->proxyserver,    
            'apptype' => $this->apptype,                
        ];
    }
}

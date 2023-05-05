<?php
namespace OCA\WebPush\Service;

use Exception;

use OCP\IConfig;
use OCA\WebPush\Service\WebPushLibraryService;
use OCA\WebPush\Model\NotificationsPushhash;
use OCA\WebPush\Model\NotificationsPushhashMapper;

class PersonalSettingsService {
    
	// Additional Services
	private $appConfig;
    private $webPushLibraryService;
	private $notificationsPushhashMapper;

	public function __construct(string $AppName,
                                WebPushLibraryService $webPushLibraryService,
								NotificationsPushhashMapper $notificationsPushhashMapper,
								IConfig $appConfig ) {		
		$this->appConfig = $appConfig;
		$this->appName = $AppName;
		$this->appVersion = $this->appConfig->getAppValue($this->appName, "installed_version");			
        $this->webPushLibraryService = $webPushLibraryService;
		$this->notificationsPushhashMapper = $notificationsPushhashMapper;		
	}

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new NotFoundException($e->getMessage());
        } elseif ($e instanceof UniqueConstraintViolationException) {
			throw new DuplicateEntryException($e->getMessage());
		} else {
			//$class = get_class($e);
			//throw new \Exception( "$class + $e" ); // should be logged in Nextcloud			
            throw $e;
        }
    }	

    public function subscribe(string $userId, string $subscription) {    

		if (trim($userId == "") || $subscription == "") {
			throw new WrongParameterException;
		}
		
		$arrResult = array();
		$arrResult["appVersion"] = $this->appVersion;	

		try {
			
			$jsonSubscription = json_decode($subscription);			

			try {
				$this->notificationsPushhashMapper->findBySubscription($subscription);
			} catch(\OCP\AppFramework\Db\DoesNotExistException $e) {					
				$notificationsPushhash = new NotificationsPushhash();
				$notificationsPushhash->setUid($userId);			
				$notificationsPushhash->setToken("unused");
				$notificationsPushhash->setDeviceidentifier("unused");
				$notificationsPushhash->setDevicepublickey($subscription);
				$notificationsPushhash->setDevicepublickeyhash("unused");
				$notificationsPushhash->setPushtokenhash("unused");
				$notificationsPushhash->setProxyserver("unused");
				$notificationsPushhash->setApptype("webpush");					
				$arrResult["notificationsPushhash"] = $this->notificationsPushhashMapper->insert($notificationsPushhash);	
			}		

			$arrResult["subscriptionEcho"] = $subscription;
			$arrResult["userSubscriptions"] = $this->webPushLibraryService->findUserSubscriptions($userId);
            $arrResult["notifyEcho"] = $this->webPushLibraryService->notifyOne($subscription, $title = "Success!", $body = "Thank you, you have subscribed successfully", $action = "", $actionURL = "");

		  } catch(Exception $e) {
			$this->handleException($e);
		  }	

        return $arrResult;
    
    }

}

?>
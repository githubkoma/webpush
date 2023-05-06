<?php
namespace OCA\WebPush\Service;

use Exception;

use OCP\IConfig;
use OCA\WebPush\Service\WebPushLibraryService;

class PersonalSettingsService {
    
	// Additional Services
	private $appConfig;
    private $webPushLibraryService;

	public function __construct(string $AppName,
                                WebPushLibraryService $webPushLibraryService,
								IConfig $appConfig ) {		
		$this->appConfig = $appConfig;
		$this->appName = $AppName;
		$this->appVersion = $this->appConfig->getAppValue($this->appName, "installed_version");			
        $this->webPushLibraryService = $webPushLibraryService;
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
				$this->webPushLibraryService->findSubscription($subscription);				
				$this->webPushLibraryService->notifyOne($userId, $subscription, $title = "Success!", $body = "Thank you, you have subscribed successfully", $action = "", $actionURL = "");
			} catch(\OCP\AppFramework\Db\DoesNotExistException $e) {
				$this->webPushLibraryService->add($userId, $subscription);								

				$this->webPushLibraryService->notifyOne($userId, $subscription, $title = "Success!", $body = "Thank you, you have subscribed successfully", $action = "", $actionURL = "");
			}
			
			$arrResult["subscriptionEcho"] = $jsonSubscription;	

		  } catch(Exception $e) {
			$this->handleException($e);
		  }	

        return $arrResult;
    
    }

}

?>
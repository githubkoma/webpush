<?php
namespace OCA\WebPush\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IUserManager;
use OCA\WebPush\Service\PersonalSettingsService;

class PersonalSettingsController extends Controller {

    private $userId;
    private $userManager;

	/** @var service */
	private $service;

    use Errors;

    public function __construct($appName, IRequest $request,
                                IUserManager $userManager,
                                PersonalSettingsService $service,
                                $UserId){
            parent::__construct(
                $appName, $request,                
                $corsMethods = 'PUT, POST, GET, DELETE, PATCH',
                $corsAllowedHeaders = 'Authorization, Content-Type, Accept, Origin',                
                $corsMaxAge = 1728000);
            
        $this->service = $service;
        $this->userId = $UserId;
        $this->userManager = $userManager;        
    }

    /**
     * @ CORS // <- doesnt work with this activated     
     * @NoCSRFrequired
     * @NoAdminRequired
     *
     */
    public function subscribe($subscription) {
        
        $userId = $this->userId;

        $response = $this->handleServiceErrors(function () use($userId, $subscription) {			
			return $this->service->subscribe($userId, $subscription);		 
		});

        return $response;

	}

}

?>
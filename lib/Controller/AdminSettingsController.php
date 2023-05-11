<?php
namespace OCA\WebPush\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IUserManager;
use OCA\WebPush\Service\WebPushLibraryService;

class AdminSettingsController extends Controller {

    private $userId;
    private $userManager;

	/** @var service */
	private $webPushLibraryService;

    use Errors;

    public function __construct($appName, IRequest $request,
                                IUserManager $userManager,
                                WebPushLibraryService $webPushLibraryService,
                                $UserId){
            parent::__construct(
                $appName, $request,                
                $corsMethods = 'PUT, POST, GET, DELETE, PATCH',
                $corsAllowedHeaders = 'Authorization, Content-Type, Accept, Origin',                
                $corsMaxAge = 1728000);
            
        $this->webPushLibraryService = $webPushLibraryService;
        $this->userId = $UserId;
        $this->userManager = $userManager;        
    }

    /**
     * @ CORS    // temporarily disabled
     * @NoCSRFrequired // temporarily
     * @ NoAdminRequired // = ADMIN REQUIRED !
     *
     */
    public function generateVapidkeys() {
        
        $userId = $this->userId; // Admin check already happened above: @ NoAdminRequired // = ADMIN REQUIRED !

        $response = $this->handleServiceErrors(function () use($userId) {			
			return $this->webPushLibraryService->generateAndStoreVapidKeys($userId);			 
            
		});

        return $response;

	}

}

?>
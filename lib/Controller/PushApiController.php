<?php
namespace OCA\WebPush\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\ApiController;
use OCP\IUserManager;
use OCA\WebPush\Service\WebPushLibraryService;

class PushApiController extends ApiController {

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
                $corsAllowedHeaders = 'Authorization, Content-Type, Accept, Origin, OCS-APIREQUEST',                
                $corsMaxAge = 1728000);
            
        $this->webPushLibraryService = $webPushLibraryService;
        $this->userId = $UserId;
        $this->userManager = $userManager;        
    }

    /**
     * @ CORS    
     * @NoCSRFrequired 
     * @NoAdminRequired
     * @PublicPage
     * @AnonRateThrottle(limit=20, period=60)
     * #[AnonRateLimit(limit: 20, period: 60)]
     *
     */
    public function pushMe(string $myself, string $userApiKey, string $message) {
        
        //$userId = $this->userId;

        $response = $this->handleServiceErrors(function () use($myself, $userApiKey, $message) {	            
            return $this->webPushLibraryService->pushMeByApiKey($myself, $userApiKey, $message);
		});

        return $response;

	}

}

?>
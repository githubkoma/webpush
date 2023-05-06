<?php
namespace OCA\WebPush\Service;

use Exception;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

use OCA\WebPush\Model\WebPushSubscription;
use OCA\WebPush\Model\WebPushSubscriptionMapper;

require __DIR__ . '/web-push-php/vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushLibraryService {
    
	// Additional Services
	private $appConfig;
	/** @var LoggerInterface */
	protected $log;
	private $webPushSubscriptionMapper;

	public function __construct(string $AppName, 
								WebPushSubscriptionMapper $webPushSubscriptionMapper,
								LoggerInterface $log,
								IConfig $appConfig) {		
		$this->appConfig = $appConfig;
		$this->appName = $AppName;
		$this->log = $log;
		//$this->appVersion = $this->appConfig->getAppValue($this->appName, "installed_version");			
		$this->webPushSubscriptionMapper = $webPushSubscriptionMapper;
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

	public function webpushToUser(string $userId, string $title, string $body, string $actionTitle, string $actionURL) {   
		$arrUserSubscriptions = $this->findUserSubscriptions($userId);
		foreach ($arrUserSubscriptions as $subscription) {
			$this->notifyOne($userId, $subscription->getSubscription(), $title, $body, $actionTitle, $actionURL);
		}
	}

    public function notifyOne(string $userId, string $subscription, string $title, string $body, string $actionTitle, string $actionURL) {    

		if ($subscription == "" || $title == "" || $body == "" ) {
			throw new WrongParameterException;
		}
				
		$result = "";
		
		try {	
			
			$this->log->debug('Debug: {details}', [
				'app' => 'webpush',
				'details' => 'Trying WebPush now: "'  . $title . '" - "' . $body . '"',
			]);

			$jsonSubscription = json_decode($subscription);

			$result = $jsonSubscription->keys->auth;		
			
			//$result = \Minishlink\WebPush\VAPID::createVapidKeys();

			$auth = [
				// Create VAPID Key first -> "private_key.pem"
				// https://github.com/web-push-libs/pywebpush
				//   -> bin/vapid
				'VAPID' => [						

					'subject' => 'mailto:admin@example.com', // can be a mailto: or your website address
					// $ openssl ec -in private_key.pem -pubout -outform DER|tail -c 65|base64|tr -d '=' |tr '/+' '_-' >> public_key.txt
					// Client has to get the publicKey as well, called applicationServerKey(=serverPublicKey):
					'publicKey' => '~88 chars', // (recommended) uncompressed public key P-256 encoded in Base64-URL
					// $ openssl ec -in private_key.pem -outform DER|tail -c +8|head -c 32|base64|tr -d '=' |tr '/+' '_-' >> private_key.txt
					'privateKey' => '~44 chars', // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
					//'pemFile' => '/var/www/html/apps/webpush/pywebpush/private_key.pem', // if you have a PEM file and can link to it on your filesystem
					'pem' => '-----BEGIN PRIVATE KEY-----
					xyz
					xyz
					xyz
					-----END PRIVATE KEY-----', // if you have a PEM file and want to hardcode its content
					/*
					REGARDING 'pem':
					IF: PHP Fatal error: Uncaught ErrorException: [VAPID] Private key should be 32 bytes long when decoded. in /path/to/project/vendor/minishlink/web-push/src/VAPID.php:84
					THEN: "I have noticed that (/path/to/project/vendor/minishlink/web-push/src/VAPID.php:64) doubles the length" -> remove "2 *"
					*/
				],
			];

			// array of notifications
			$notifications = [
				[
					'subscription' => Subscription::create([
						'endpoint' => $jsonSubscription->endpoint, // Firefox 43+,
						'publicKey' => $jsonSubscription->keys->p256dh, // base 64 encoded, should be 88 chars
						'authToken' => $jsonSubscription->keys->auth, // base 64 encoded, should be 24 chars
					]),
					'payload' => json_encode([
						"title" => $title,
						"body" => $body . " (WebPush)",
						"actionURL" => $actionURL,
						"actionTitle" => $actionTitle,
					])
				]
			];

			$webPush = new WebPush($auth);			

			$report = $webPush->sendOneNotification(
				$notifications[0]['subscription'],
				$notifications[0]['payload'], // optional (defaults null)
				['TTL' => 172800]
			);

			$result = $notifications[0]['payload'];

		  } catch(Exception $e) {
			$this->handleException($e);
		  }	

        return $result;
    
    }

    public function findUserSubscriptions(string $userId) {    

		if ($userId == "") {
			throw new WrongParameterException;
		}
		
		$result = [];
		
		try {
			$result = $this->webPushSubscriptionMapper->findAllByUser($userId);		
		} catch (Exception $e) {
			// this isnt a problem			
		}

		return $result;

	}

    public function findSubscription(string $subscription) {    

		if ($subscription == "") {
			throw new WrongParameterException;
		}
		
		$result = [];

		$result = $this->webPushSubscriptionMapper->findSubscription($subscription);		

		return $result;

	}

    public function add(string $userId, string $subscription) {    

		if ($subscription == "") {
			throw new WrongParameterException;
		}

		$jsonSubscription = json_decode($subscription);

		$result = [];		

		$webPushSubscription = new WebPushSubscription();
		$webPushSubscription->setUserId($userId);			
		$webPushSubscription->setIdentifier(time());			
		$webPushSubscription->setSubscription($subscription);
		$webPushSubscription->setSubscriptionEndpoint($jsonSubscription->endpoint);
		$webPushSubscription->setSubscriptionKeysP256dh($jsonSubscription->keys->p256dh);
		$webPushSubscription->setSubscriptionKeysAuth($jsonSubscription->keys->auth);
		$webPushSubscription->setSubscriptionExpirationTime(0);
		$webPushSubscription->setCrtUserId($userId);
		$webPushSubscription->setCrtDate(time());
		$webPushSubscription->setLastUpdate(time());					
		$webPushSubscription->setIsArchived(0);					
		$webPushSubscription->setUpdateUserId($userId);					
		$webPushSubscription->setEtag(0);					
		$result = $this->webPushSubscriptionMapper->insert($webPushSubscription);	
		$this->log->debug('Debug: {details}', [
			'app' => 'webpush',
			'details' => 'Added Subscription: ' .  $userId . ' ' . $webPushSubscription->getIdentifier(),
		]);

		return $result;

	}


}

?>
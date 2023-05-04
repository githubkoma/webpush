<?php

namespace OCA\WebPush\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\WebPush\Service\MissingDependencyException;
use OCA\WebPush\Service\NotFoundException;
use OCA\WebPush\Service\DuplicateEntryException;
use OCA\WebPush\Service\WrongParameterException;

trait Errors {

	protected function handleServiceErrors (Closure $callback) {
        try {
			return new DataResponse($callback());
		
        } catch(MissingDependencyException $e) {			
			// $message = ['message' => $e->getMessage()];
			$message = ['message' => "Error: Circles App Not Installed"];
			return new DataResponse($message, Http::STATUS_NOT_IMPLEMENTED);

        } catch(NotFoundException $e) {			
			// $message = ['message' => $e->getMessage()];
			$message = ['message' => "Error: Not Found"];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);

        } catch(DuplicateEntryException $e) {			
			$message = ['message' => $e->getMessage()];
			//$message = ['message' => "Error: Duplicate"];
			return new DataResponse($message, Http::STATUS_CONFLICT);

        } catch(WrongParameterException $e) {			
			$message = ['message' => $e->getMessage()];
			//$message = ['message' => "Error: Bad Request"];
			return new DataResponse($message, HTTP::STATUS_BAD_REQUEST);		
		
        } catch(\Exception $e) { // "\Exception" = ANY TYPE OF Exception					
			$message = ['message' => "Error: Other", 'type' => get_class($e)];
			if ($e->getMessage()) {
				$message = ['message' => $e->getMessage(), 'type' => get_class($e)];
			}
			return new DataResponse($message, Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
	
	// ToDo: Handle other Errors as well..
		// OCP\Share\Exceptions\ShareNotFound
		// {"message":"You are not allowed to share .PAD31.md","type":"OCP\\Share\\Exceptions\\GenericShareException"}

}
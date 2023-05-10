<?php
namespace OCA\WebPush\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;

class WebPushAdmin implements ISettings {
    private IL10N $l;
    private IConfig $config;

    public function __construct(IConfig $config, IL10N $l) {
        $this->config = $config;
        $this->l = $l;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm() {
        $parameters = [
            //'mySetting' => $this->config->getSystemValue('my_notes_setting', true),
            'VAPID_subject' => $this->config->getAppValue("webpush", "VAPID_subject"),
            'VAPID_publicKey' => $this->config->getAppValue("webpush", "VAPID_publicKey"),
            'VAPID_privateKey' => $this->config->getAppValue("webpush", "VAPID_privateKey"),
            //'VAPID_pem' => $this->config->getAppValue("webpush", "VAPID_pem"),
        ];

        return new TemplateResponse('webpush', 'settings/admin', $parameters, '');
    }

    public function getSection() {
        return 'webpush'; // Name of the previously created section.
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     *
     * E.g.: 70
     */
    public function getPriority() {
        return 10;
    }
}
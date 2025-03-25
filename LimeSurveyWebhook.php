<?php

/***** ***** ***** ***** *****
* Send a curl post request after each afterSurveyComplete event
*
* @originalauthor Stefan Verweij <stefan@evently.nl>
* @copyright 2016 Evently <https://www.evently.nl>
  @author IrishWolf
* @copyright 2023 Nerds Go Casual e.V.
* @license GPL v3
* @version 1.0.0
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
***** ***** ***** ***** *****/

class LimeSurveyWebhook extends PluginBase
	{
		protected $storage = 'DbStorage';
		static protected $description = 'A simple Webhook for LimeSurvey';
		static protected $name = 'LimeSurveyWebhook';
		protected $surveyId;

		public function init()
			{
                $this->subscribe('afterFindSurvey');
                $this->subscribe('afterGenerateToken');
                $this->subscribe('afterPluginLoad');
                $this->subscribe('afterQuickMenuLoad');
                $this->subscribe('afterSurveyActivate');
                $this->subscribe('afterSurveyComplete');
                $this->subscribe('afterSurveyDeactivate');
                $this->subscribe('afterSurveyQuestionAssessment');
                $this->subscribe('afterSurveyQuota');
                $this->subscribe('afterSurveySettingsSave');
                $this->subscribe('beforeActivate');
                $this->subscribe('beforeAdminMenuRender');
                $this->subscribe('beforeCloseHtml');
                $this->subscribe('beforeControllerAction');
                $this->subscribe('beforeDeactivate');
                $this->subscribe('beforeHasPermission');
                $this->subscribe('beforeLoadResponse');
                $this->subscribe('beforePermissionSetSave');
                $this->subscribe('beforeProcessFileUpload');
                $this->subscribe('beforePluginManagerMenuRender');
                $this->subscribe('beforeQuestionRender');
                $this->subscribe('beforeRegister');
                $this->subscribe('beforeRegisterForm');
                $this->subscribe('beforeSideMenuRender');
                $this->subscribe('beforeSurveyAdminView');
                $this->subscribe('beforeSurveyActivate');
                $this->subscribe('beforeSurveyBarRender');
                $this->subscribe('beforeSurveyDeactivate');
                $this->subscribe('beforeSurveyPage');
                $this->subscribe('beforeSurveySettings');
                $this->subscribe('beforeTwigRenderTemplate');
                $this->subscribe('beforeToolsMenuRender');
                $this->subscribe('beforeUrlCheck');
                $this->subscribe('beforeWelcomePageRender');
                $this->subscribe('createNewUser');
                $this->subscribe('createRandomPassword');
                $this->subscribe('checkPasswordRequirement');
                $this->subscribe('ExpressionManagerStart');
                $this->subscribe('getGlobalBasePermissions');
                $this->subscribe('getPluginTwigPath');
                $this->subscribe('getValidScreenFiles');
                $this->subscribe('listExportOptions');
                $this->subscribe('listExportPlugins');
                $this->subscribe('listQuestionPlugins');
                $this->subscribe('newDirectRequest');
                $this->subscribe('newExport');
                $this->subscribe('newQuestionAttributes');
                $this->subscribe('newSurveySettings');
                $this->subscribe('NewUnsecureRequest');
                $this->subscribe('onSurveyDenied');
                $this->subscribe('setVariableExpressionEnd');
                $this->subscribe('saveSurveyForm');
                $this->subscribe('afterReceiveOAuthResponse');
                $this->subscribe('afterSelectEmailPlugin');
                $this->subscribe('beforeEmail');
                $this->subscribe('beforeSurveyEmail');
                $this->subscribe('beforeTokenEmail');
                $this->subscribe('beforeEmailDispatch');
                $this->subscribe('beforePrepareRedirectToAuthPage');
                $this->subscribe('beforeRedirectToAuthPage');
                $this->subscribe('listEmailPlugins');
                $this->subscribe('MailerConstruct');
                $this->subscribe('beforeSurveyDelete');
                $this->subscribe('beforeSurveySave');
                $this->subscribe('afterSurveyDelete');
                $this->subscribe('afterSurveySave');
                $this->subscribe('beforeTokenDelete');
                $this->subscribe('beforeTokenSave');
                $this->subscribe('afterTokenDelete');
                $this->subscribe('afterTokenSave');
                $this->subscribe('beforeResponseDelete');
                $this->subscribe('beforeResponseSave');
                $this->subscribe('afterResponseDelete');
                $this->subscribe('afterResponseSave');
                $this->subscribe('beforeTokenDynamicDelete');
                $this->subscribe('beforeTokenDynamicSave');
                $this->subscribe('afterTokenDynamicDelete');
                $this->subscribe('afterTokenDynamicSave');
                $this->subscribe('beforeSurveyDynamicDelete');
                $this->subscribe('beforeSurveyDynamicSave');
                $this->subscribe('afterSurveyDynamicDelete');
                $this->subscribe('afterSurveyDynamicSave');
                $this->subscribe('beforeModelDelete');
                $this->subscribe('beforeModelSave');
                $this->subscribe('afterModelDelete');
                $this->subscribe('afterModelSave');
            }

            public function afterFindSurvey() {
                if ($this->isEventOn('afterFindSurvey')) {
                    $this->callWebhook('afterFindSurvey');
                }
                return;
            }

            public function afterGenerateToken() {
                if ($this->isEventOn('afterGenerateToken')) {
                    $this->callWebhook('afterGenerateToken');
                }
                return;
            }

            public function afterPluginLoad() {
                if ($this->isEventOn('afterPluginLoad')) {
                    $this->callWebhook('afterPluginLoad');
                }
                return;
            }

            public function afterQuickMenuLoad() {
                if ($this->isEventOn('afterQuickMenuLoad')) {
                    $this->callWebhook('afterQuickMenuLoad');
                }
                return;
            }

            public function afterSurveyActivate() {
                if ($this->isEventOn('afterSurveyActivate')) {
                    $this->callWebhook('afterSurveyActivate');
                }
                return;
            }

            public function afterSurveyComplete() {
                if ($this->isEventOn('afterSurveyComplete')) {
                    $this->callWebhookSurvey('afterSurveyComplete');
                }
                return;
            }

            public function afterSurveyDeactivate() {
                if ($this->isEventOn('afterSurveyDeactivate')) {
                    $this->callWebhook('afterSurveyDeactivate');
                }
                return;
            }

            public function afterSurveyQuestionAssessment() {
                if ($this->isEventOn('afterSurveyQuestionAssessment')) {
                    $this->callWebhook('afterSurveyQuestionAssessment');
                }
                return;
            }

            public function afterSurveyQuota() {
                if ($this->isEventOn('afterSurveyQuota')) {
                    $this->callWebhook('afterSurveyQuota');
                }
                return;
            }

            public function afterSurveySettingsSave() {
                if ($this->isEventOn('afterSurveySettingsSave')) {
                    $this->callWebhook('afterSurveySettingsSave');
                }
                return;
            }

            public function beforeActivate() {
                if ($this->isEventOn('beforeActivate')) {
                    $this->callWebhook('beforeActivate');
                }
                return;
            }

            public function beforeAdminMenuRender() {
                if ($this->isEventOn('beforeAdminMenuRender')) {
                    $this->callWebhook('beforeAdminMenuRender');
                }
                return;
            }

            public function beforeCloseHtml() {
                if ($this->isEventOn('beforeCloseHtml')) {
                    $this->callWebhook('beforeCloseHtml');
                }
                return;
            }

            public function beforeControllerAction() {
                if ($this->isEventOn('beforeControllerAction')) {
                    $this->callWebhook('beforeControllerAction');
                }
                return;
            }

            public function beforeDeactivate() {
                if ($this->isEventOn('beforeDeactivate')) {
                    $this->callWebhook('beforeDeactivate');
                }
                return;
            }

            public function beforeHasPermission() {
                if ($this->isEventOn('beforeHasPermission')) {
                    $this->callWebhook('beforeHasPermission');
                }
                return;
            }

            public function beforeLoadResponse() {
                if ($this->isEventOn('beforeLoadResponse')) {
                    $this->callWebhook('beforeLoadResponse');
                }
                return;
            }

            public function beforePermissionSetSave() {
                if ($this->isEventOn('beforePermissionSetSave')) {
                    $this->callWebhook('beforePermissionSetSave');
                }
                return;
            }

            public function beforeProcessFileUpload() {
                if ($this->isEventOn('beforeProcessFileUpload')) {
                    $this->callWebhook('beforeProcessFileUpload');
                }
                return;
            }

            public function beforePluginManagerMenuRender() {
                if ($this->isEventOn('beforePluginManagerMenuRender')) {
                    $this->callWebhook('beforePluginManagerMenuRender');
                }
                return;
            }

            public function beforeQuestionRender() {
                if ($this->isEventOn('beforeQuestionRender')) {
                    $this->callWebhook('beforeQuestionRender');
                }
                return;
            }

            public function beforeRegister() {
                if ($this->isEventOn('beforeRegister')) {
                    $this->callWebhook('beforeRegister');
                }
                return;
            }

            public function beforeRegisterForm() {
                if ($this->isEventOn('beforeRegisterForm')) {
                    $this->callWebhook('beforeRegisterForm');
                }
                return;
            }

            public function beforeSideMenuRender() {
                if ($this->isEventOn('beforeSideMenuRender')) {
                    $this->callWebhook('beforeSideMenuRender');
                }
                return;
            }

            public function beforeSurveyAdminView() {
                if ($this->isEventOn('beforeSurveyAdminView')) {
                    $this->callWebhook('beforeSurveyAdminView');
                }
                return;
            }

            public function beforeSurveyActivate() {
                if ($this->isEventOn('beforeSurveyActivate')) {
                    $this->callWebhook('beforeSurveyActivate');
                }
                return;
            }

            public function beforeSurveyBarRender() {
                if ($this->isEventOn('beforeSurveyBarRender')) {
                    $this->callWebhook('beforeSurveyBarRender');
                }
                return;
            }

            public function beforeSurveyDeactivate() {
                if ($this->isEventOn('beforeSurveyDeactivate')) {
                    $this->callWebhook('beforeSurveyDeactivate');
                }
                return;
            }

            public function beforeSurveyPage() {
                if ($this->isEventOn('beforeSurveyPage')) {
                    $this->callWebhookSurvey('beforeSurveyPage');
                }
                return;
            }

            public function beforeSurveySettings() {
                if ($this->isEventOn('beforeSurveySettings')) {
                    $this->callWebhook('beforeSurveySettings');
                }
                return;
            }

            public function beforeTwigRenderTemplate() {
                if ($this->isEventOn('beforeTwigRenderTemplate')) {
                    $this->callWebhook('beforeTwigRenderTemplate');
                }
                return;
            }

            public function beforeToolsMenuRender() {
                if ($this->isEventOn('beforeToolsMenuRender')) {
                    $this->callWebhook('beforeToolsMenuRender');
                }
                return;
            }

            public function beforeUrlCheck() {
                if ($this->isEventOn('beforeUrlCheck')) {
                    $this->callWebhook('beforeUrlCheck');
                }
                return;
            }

            public function beforeWelcomePageRender() {
                if ($this->isEventOn('beforeWelcomePageRender')) {
                    $this->callWebhook('beforeWelcomePageRender');
                }
                return;
            }

            public function createNewUser() {
                if ($this->isEventOn('createNewUser')) {
                    $this->callWebhook('createNewUser');
                }
                return;
            }

            public function createRandomPassword() {
                if ($this->isEventOn('createRandomPassword')) {
                    $this->callWebhook('createRandomPassword');
                }
                return;
            }

            public function checkPasswordRequirement() {
                if ($this->isEventOn('checkPasswordRequirement')) {
                    $this->callWebhook('checkPasswordRequirement');
                }
                return;
            }

            public function ExpressionManagerStart() {
                if ($this->isEventOn('ExpressionManagerStart')) {
                    $this->callWebhook('ExpressionManagerStart');
                }
                return;
            }

            public function getGlobalBasePermissions() {
                if ($this->isEventOn('getGlobalBasePermissions')) {
                    $this->callWebhook('getGlobalBasePermissions');
                }
                return;
            }

            public function getPluginTwigPath() {
                if ($this->isEventOn('getPluginTwigPath')) {
                    $this->callWebhook('getPluginTwigPath');
                }
                return;
            }

            public function getValidScreenFiles() {
                if ($this->isEventOn('getValidScreenFiles')) {
                    $this->callWebhook('getValidScreenFiles');
                }
                return;
            }

            public function listExportOptions() {
                if ($this->isEventOn('listExportOptions')) {
                    $this->callWebhook('listExportOptions');
                }
                return;
            }

            public function listExportPlugins() {
                if ($this->isEventOn('listExportPlugins')) {
                    $this->callWebhook('listExportPlugins');
                }
                return;
            }

            public function listQuestionPlugins() {
                if ($this->isEventOn('listQuestionPlugins')) {
                    $this->callWebhook('listQuestionPlugins');
                }
                return;
            }

            public function newDirectRequest() {
                if ($this->isEventOn('newDirectRequest')) {
                    $this->callWebhook('newDirectRequest');
                }
                return;
            }

            public function newExport() {
                if ($this->isEventOn('newExport')) {
                    $this->callWebhook('newExport');
                }
                return;
            }

            public function newQuestionAttributes() {
                if ($this->isEventOn('newQuestionAttributes')) {
                    $this->callWebhook('newQuestionAttributes');
                }
                return;
            }

            public function newSurveySettings() {
                if ($this->isEventOn('newSurveySettings')) {
                    $this->callWebhook('newSurveySettings');
                }
                return;
            }

            public function NewUnsecureRequest() {
                if ($this->isEventOn('NewUnsecureRequest')) {
                    $this->callWebhook('NewUnsecureRequest');
                }
                return;
            }

            public function onSurveyDenied() {
                if ($this->isEventOn('onSurveyDenied')) {
                    $this->callWebhookSurvey('onSurveyDenied');
                }
                return;
            }

            public function setVariableExpressionEnd() {
                if ($this->isEventOn('setVariableExpressionEnd')) {
                    $this->callWebhook('setVariableExpressionEnd');
                }
                return;
            }

            public function saveSurveyForm() {
                if ($this->isEventOn('saveSurveyForm')) {
                    $this->callWebhook('saveSurveyForm');
                }
                return;
            }

            public function afterReceiveOAuthResponse() {
                if ($this->isEventOn('afterReceiveOAuthResponse')) {
                    $this->callWebhook('afterReceiveOAuthResponse');
                }
                return;
            }

            public function afterSelectEmailPlugin() {
                if ($this->isEventOn('afterSelectEmailPlugin')) {
                    $this->callWebhook('afterSelectEmailPlugin');
                }
                return;
            }

            public function beforeEmail() {
                if ($this->isEventOn('beforeEmail')) {
                    $this->callWebhook('beforeEmail');
                }
                return;
            }

            public function beforeSurveyEmail() {
                if ($this->isEventOn('beforeSurveyEmail')) {
                    $this->callWebhook('beforeSurveyEmail');
                }
                return;
            }

            public function beforeTokenEmail() {
                if ($this->isEventOn('beforeTokenEmail')) {
                    $this->callWebhook('beforeTokenEmail');
                }
                return;
            }

            public function beforeEmailDispatch() {
                if ($this->isEventOn('beforeEmailDispatch')) {
                    $this->callWebhook('beforeEmailDispatch');
                }
                return;
            }

            public function beforePrepareRedirectToAuthPage() {
                if ($this->isEventOn('beforePrepareRedirectToAuthPage')) {
                    $this->callWebhook('beforePrepareRedirectToAuthPage');
                }
                return;
            }

            public function beforeRedirectToAuthPage() {
                if ($this->isEventOn('beforeRedirectToAuthPage')) {
                    $this->callWebhook('beforeRedirectToAuthPage');
                }
                return;
            }

            public function listEmailPlugins() {
                if ($this->isEventOn('listEmailPlugins')) {
                    $this->callWebhook('listEmailPlugins');
                }
                return;
            }

            public function MailerConstruct() {
                if ($this->isEventOn('MailerConstruct')) {
                    $this->callWebhook('MailerConstruct');
                }
                return;
            }

            public function beforeSurveyDelete() {
                if ($this->isEventOn('beforeSurveyDelete')) {
                    $this->callWebhook('beforeSurveyDelete');
                }
                return;
            }

            public function beforeSurveySave() {
                if ($this->isEventOn('beforeSurveySave')) {
                    $this->callWebhook('beforeSurveySave');
                }
                return;
            }

            public function afterSurveyDelete() {
                if ($this->isEventOn('afterSurveyDelete')) {
                    $this->callWebhook('afterSurveyDelete');
                }
                return;
            }

            public function afterSurveySave() {
                if ($this->isEventOn('afterSurveySave')) {
                    $this->callWebhook('afterSurveySave');
                }
                return;
            }

            public function beforeTokenDelete() {
                if ($this->isEventOn('beforeTokenDelete')) {
                    $this->callWebhook('beforeTokenDelete');
                }
                return;
            }

            public function beforeTokenSave() {
                if ($this->isEventOn('beforeTokenSave')) {
                    $this->callWebhook('beforeTokenSave');
                }
                return;
            }

            public function afterTokenDelete() {
                if ($this->isEventOn('afterTokenDelete')) {
                    $this->callWebhook('afterTokenDelete');
                }
                return;
            }

            public function afterTokenSave() {
                if ($this->isEventOn('afterTokenSave')) {
                    $this->callWebhook('afterTokenSave');
                }
                return;
            }

            public function beforeResponseDelete() {
                if ($this->isEventOn('beforeResponseDelete')) {
                    $this->callWebhook('beforeResponseDelete');
                }
                return;
            }

            public function beforeResponseSave() {
                if ($this->isEventOn('beforeResponseSave')) {
                    $this->callWebhook('beforeResponseSave');
                }
                return;
            }

            public function afterResponseDelete() {
                if ($this->isEventOn('afterResponseDelete')) {
                    $this->callWebhook('afterResponseDelete');
                }
                return;
            }

            public function afterResponseSave() {
                if ($this->isEventOn('afterResponseSave')) {
                    $this->callWebhook('afterResponseSave');
                }
                return;
            }

            public function beforeTokenDynamicDelete() {
                if ($this->isEventOn('beforeTokenDynamicDelete')) {
                    $this->callWebhook('beforeTokenDynamicDelete');
                }
                return;
            }

            public function beforeTokenDynamicSave() {
                if ($this->isEventOn('beforeTokenDynamicSave')) {
                    $this->callWebhook('beforeTokenDynamicSave');
                }
                return;
            }

            public function afterTokenDynamicDelete() {
                if ($this->isEventOn('afterTokenDynamicDelete')) {
                    $this->callWebhook('afterTokenDynamicDelete');
                }
                return;
            }

            public function afterTokenDynamicSave() {
                if ($this->isEventOn('afterTokenDynamicSave')) {
                    $this->callWebhook('afterTokenDynamicSave');
                }
                return;
            }

            public function beforeSurveyDynamicDelete() {
                if ($this->isEventOn('beforeSurveyDynamicDelete')) {
                    $this->callWebhook('beforeSurveyDynamicDelete');
                }
                return;
            }

            public function beforeSurveyDynamicSave() {
                if ($this->isEventOn('beforeSurveyDynamicSave')) {
                    $this->callWebhook('beforeSurveyDynamicSave');
                }
                return;
            }

            public function afterSurveyDynamicDelete() {
                if ($this->isEventOn('afterSurveyDynamicDelete')) {
                    $this->callWebhook('afterSurveyDynamicDelete');
                }
                return;
            }

            public function afterSurveyDynamicSave() {
                if ($this->isEventOn('afterSurveyDynamicSave')) {
                    $this->callWebhook('afterSurveyDynamicSave');
                }
                return;
            }

            public function beforeModelDelete() {
                if ($this->isEventOn('beforeModelDelete')) {
                    $this->callWebhook('beforeModelDelete');
                }
                return;
            }

            public function beforeModelSave() {
                if ($this->isEventOn('beforeModelSave')) {
                    $this->callWebhook('beforeModelSave');
                }
                return;
            }

            public function afterModelDelete() {
                if ($this->isEventOn('afterModelDelete')) {
                    $this->callWebhook('afterModelDelete');
                }
                return;
            }

            public function afterModelSave() {
                if ($this->isEventOn('afterModelSave')) {
                    $this->callWebhook('afterModelSave');
                }
                return;
            }

        protected $settings = [];

        /**
        * @param mixed $getValues
        */
        public function getPluginSettings($getValues = true) {
            /* Definition and default */
            $fixedPluginSettings = $this->getFixedGlobalSetting();
		    $this->settings = array(
                'sUrl' => array(
                    'type' => 'string',
                    'label' => 'The default URL to send the webhook to:',
                    'default' => $this->getGlobalSetting('sUrl', ''),
                    'htmlOptions' => [
                        'readonly' => in_array('sUrl', $fixedPluginSettings)
                    ],
                    'help' => 'To test get one from https://webhook.site'
                ),
                'sId' => array(
                    'type' => 'string',
                    'default' => '000000',
                    'label' => 'The ID of the surveys:',
                    'default' => $this->getGlobalSetting('sId', ''),
                    'htmlOptions' => [
                        'readonly' => in_array('sId', $fixedPluginSettings)
                    ],
                    'help' => 'The unique number of the surveys. You can set multiple surveys with an "," as separator. Example: 123456, 234567, 345678. Let empty to treat all'
                ),
                'sAuthToken' => array(
                    'type' => 'string',
                    'label' => 'API Authentication Token',
                    'default' => $this->getGlobalSetting('sAuthToken', ''),
                    'htmlOptions' => [
                        'readonly' => in_array('sAuthToken', $fixedPluginSettings)
                    ],
                    'help' => 'Maybe you need a token to verify your request? <br> This will be send in plain text / not encoded!'
                ),
                'sHeaderSignatureName' => array(
                    'type' => 'string',
                    'label' => 'Header Signature Name',
                    'default' => $this->getGlobalSetting('sHeaderSignatureName', 'X-Signature-SHA256'),
                    'htmlOptions' => [
                        'readonly' => in_array('sHeaderSignatureName', $fixedPluginSettings)
                    ],
                    'help' => 'Header Signature Name. Default to X-Signature-SHA256.'
                ),
                'sHeaderSignaturePrefix' => array(
                    'type' => 'string',
                    'label' => 'Header Signature Prefix',
                    'default' => $this->getGlobalSetting('sHeaderSignaturePrefix', ''),
                    'htmlOptions' => [
                        'readonly' => in_array('sHeaderSignaturePrefix', $fixedPluginSettings)
                    ],
                    'help' => 'Header Signature Prefix'
                ),
                'sBug' => array(
                    'type' => 'checkbox',
                    'default' => $this->getGlobalSetting('sBug', false),
                    'htmlOptions' => [
                        'readonly' => in_array('sBug', $fixedPluginSettings)
                    ],
                    'label' => 'Enable Debug Mode',
                    'help' => 'Enable debugmode to see what data is transmitted. Respondents will see this as well so you should turn this off for live surveys'
                ),
                'events' => array(
                    'type' => 'json',
                    'label' => $this->gT('Events to send to the webhook server'),
                    'help' => sprintf(
                        $this->gT('A JSON object describing the events to send to the webhook server. The JSON object has the following form: %s'),
                        CHtml::tag('pre', [], "{\n\t\"admin\": { ... },\n\t\"surveys\": {\n\t\t\"beforeSurveyPage\": true,\n\t\t\"afterSurveyComplete\": false,\n\t},\n\t\"users\": { ... },\n\t...\n}")
                    ),
                    'editorOptions' => array('mode' => 'tree'),
                    'default' => $this->getGlobalSetting(
                        'events',
                        self::getDefaultEvents()
                    ),
                    'htmlOptions' => [
                        'disabled' => in_array('events', $fixedPluginSettings)
                    ],
                ),
		    );

            /* Get current */
            $pluginSettings = parent::getPluginSettings($getValues);
            /* Update current for fixed one */
            if ($getValues) {
                foreach ($fixedPluginSettings as $setting) {
                    $pluginSettings[$setting]['current'] = $this->getGlobalSetting($setting);
                }
            }
            /* Remove hidden */
            foreach ($this->getHiddenGlobalSetting() as $setting) {
                unset($pluginSettings[$setting]);
            }
            return $pluginSettings;
        }

        /***** ***** ***** ***** *****
		* Calls a webhook
		* @return array | response
		***** ***** ***** ***** *****/
		private function callWebhook($comment, $details=null, $time_start=null)
        {
            if(!$time_start) {
                $time_start=microtime(true);
            }
            $url = $this->getGlobalSetting('sUrl');              
            // Validate webhook URL
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                error_log('Invalid webhook URL: ' . $url);
                return; // Exit if the URL is not valid
            }
            $bug = (boolean)$this->getGlobalSetting('sBug');
            $parameters=array();
            $parameters["event"] = $comment;

            if($details==null) {
                $event = $this->getEvent();
                $reflection = new ReflectionObject($event);
                $property = $reflection->getProperty('_parameters');
                $property->setAccessible(true);  // Make the property accessible

                // Now you can get the value of _parameters
                $details = $property->getValue($event);
            }
            $parameters["event_details"] = $details;
            // Convert to DateTime
            $utcDateTime = DateTime::createFromFormat("U.u", number_format($time_start, 6, '.', ''));
            $utcDateTime->setTimezone(new DateTimeZone("UTC")); // Ensure it's in UTC
            if($bug) {
                error_log("UTC Time: " . $utcDateTime->format("Y-m-d H:i:s.u") . " UTC.");
            }
            $parameters["datetime"] = $utcDateTime->format("Y-m-d H:i:s.u"). " UTC";
            $postData=json_encode($parameters);
            $hookSent = $this->httpPost($url, $postData);
            $this->debug($url, $parameters, $hookSent, $time_start, $comment);
            return;
        }

		/***** ***** ***** ***** *****
		* Calls a webhook
		* @return array | response
		***** ***** ***** ***** *****/
		private function callWebhookSurvey($comment)
			{
                $time_start=microtime(true);
                $bug = (boolean)$this->getGlobalSetting('sBug');
                
                $event = $this->getEvent();
                $surveyId = $event->get('surveyId');
                $hookSurveyId = $this->getGlobalSetting('sId','');
                $hookSurveyIdArray = explode(',', preg_replace('/\s+/', '', $hookSurveyId));
                
                if (!$hookSurveyId == '') {
                    if(!in_array($surveyId, $hookSurveyIdArray)) {
                        return;
                    }
                }

                if($bug) {
                    error_log($comment . " : " . $surveyId);
                }
                
                // Try to fetch the current from the URL manually or default language
                $surveyInfo = Survey::model()->findByPk($surveyId);
                $languageRequest=Yii::app()->request->getParam('lang', null);
                $lang = $languageRequest !== null ? $languageRequest : $surveyInfo->language; // Fallback to default language

                // Get token from the URL manually
                $token = Yii::app()->request->getParam('token', null);

                $details = array(
                    "surveyid" => $surveyId,
                    'lang' => $lang,
                    "token" => $token
                );
                
                // Include response data only for completion
                if ($comment === 'afterSurveyComplete') {
                    $responseId = $event->get('responseId');
                    $details['responseId'] = $responseId;
                    // Fetch response data manually from the survey table
                    $response = $this->pluginManager->getAPI()->getResponse($surveyId, $responseId);
                    $details['response'] = $response;
                    $details['submitDate'] = $response['submitdate'];
                }

                return $this->callWebhook($comment, $details, $time_start);
            }

        private function getLastResponse($surveyId, $additionalFields)
            {
                if ($additionalFields)
                    {
                        $columnsInDB = \getQuestionInformation\helpers\surveyCodeHelper::getAllQuestions($surveyId);

                        $aadditionalSQGA = array();
                        foreach ($additionalFields as $field)
                            {
                                $push_val = array_search(trim($field), $columnsInDB);
                                if ($push_val) array_push($aadditionalSQGA, $push_val);
                            }
                        if (count($additionalFields) > 0)
                            {
                            $sadditionalSQGA = ", " . implode(', ', $aadditionalSQGA);
                            }
                    }

                $responseTable = $this->api->getResponseTable($surveyId);
                $query = "SELECT id, token, submitdate {$sadditionalSQGA} FROM {$responseTable} ORDER BY submitdate DESC LIMIT 1";
                $rawResult = Yii::app()->db->createCommand($query)->queryRow();

                $result = $rawResult;

                if (count($aadditionalSQGA) > 0)
                    {
                        foreach ($aadditionalSQGA as $SQGA)
                            {
                                $result[$columnsInDB[$SQGA]] = htmlspecialchars($rawResult[$SQGA]);
                                if ($push_val)
                                    array_push($aadditionalSQGA, $push_val);
                            }
                    }

                return $result;
            }

        /***** ***** ***** ***** *****
        * httpPost function http://hayageek.com/php-curl-post-get/
        * creates and executes a POST request
        * returns the output
        ***** ***** ***** ***** *****/
        private function httpPost($url, $postData)
            {
                $bug = $this->getGlobalSetting('sBug');
                error_log('Webhook call started');
                if (empty($url)) {
                    error_log('Webhook call failed: No URL defined!');
                    return; // No URL defined
                }

                // Initialize cURL session
                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                $headers=[
                    'Content-Type: application/json',
                    "Content-Length: " . strlen($postData) // Helps some servers parse JSON correctly
                ];
                $signingSecret = $this->getGlobalSetting('sAuthToken', '');
                if($signingSecret !== '') {
                    $signingHeaderName = $this->getGlobalSetting('sHeaderSignatureName');
                    $signingPrefix = $this->getGlobalSetting('sHeaderSignaturePrefix');
                    // Calculate HMAC
                    $signature = hash_hmac("sha256", $signingPrefix . $postData, $signingSecret);
                    $headerToAdd="$signingHeaderName: $signature";
                    $headers[] = $headerToAdd;
                }
                if($bug) {
                    error_log(implode(" , ", $headers));
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $output = curl_exec($ch);
                
                // Handle errors (optional)
                if (curl_errno($ch)) {
                    error_log('Webhook call failed: ' . curl_error($ch));
                }
                curl_close($ch);
                return $output;
            }

        /***** ***** ***** ***** *****
        * debugging
        ***** ***** ***** ***** *****/
        private function debug($url, $parameters, $hookSent, $time_start, $comment)
            {
                $bug=(boolean)$this->getGlobalSetting('sBug', 0);
                if ($bug)
                  {
                    error_log($comment . " | Url sent : ". $url . " | Params: ". json_encode($parameters) . " | Response received : " . json_encode($hookSent));
                    $html = '<pre><br><br>----------------------------- DEBUG ----------------------------- <br><br>';
                    $html .= 'Comment: <br>' . print_r($comment, true);
                    $html .= '<br><br>Parameters: <br>' . print_r($parameters, true);
                    $html .= '<br><br>Response: <br>' . print_r($hookSent, true);
                    $html .= "<br><br> ----------------------------- <br><br>";
                    $html .= 'Hook sent to: ' . print_r($url, true) . '<br>';
                    $html .= 'Total execution time in seconds: ' . (microtime(true) - $time_start);
                    $html .= '</pre>';
                    $event = $this->getEvent();
                    $event->getContent($this)->addContent($html);
                  }
		    }

        /**
         * get settings according to current DB and fixed config.php
         * @param string $setting
         * @param mixed $default
         * @return mixed
         */
        private function getGlobalSetting($setting, $default = null)
            {
                $WebhookSettings = App()->getConfig('WebhookSettings');
                if (isset($WebhookSettings['fixed'][$setting])) {
                    return $WebhookSettings['fixed'][$setting];
                }
                if (isset($WebhookSettings[$setting])) {
                    return $this->get($setting, null, null, $WebhookSettings[$setting]);
                }
                return $this->get($setting, null, null, $default);
            }

        /**
         * Get the fixed settings name
         * @return string[]
         */
        private function getFixedGlobalSetting()
            {
                $WebhookSettings = App()->getConfig('WebhookSettings');
                if (isset($WebhookSettings['fixed'])) {
                    return array_keys($WebhookSettings['fixed']);
                }
                return [];
            }

        /**
         * Get the hidden settings name
         * @return string[]
         */
        private function getHiddenGlobalSetting()
            {
                $WebhookSettings = App()->getConfig('WebhookSettings');
                if (isset($WebhookSettings['hidden'])) {
                    return $WebhookSettings['hidden'];
                }
                return [];
            }
        /**
         * Return global default permission
         * @return string
         */
        private static function getDefaultEvents() {
            return json_encode([
                'render' => [
                  'beforeAdminMenuRender' => false,
                  'beforePluginManagerMenuRender' => false,
                  'beforeQuestionRender' => false,
                  'beforeSideMenuRender' => false,
                  'beforeSurveyBarRender' => false,
                  'beforeTwigRenderTemplate' => false,
                  'beforeToolsMenuRender' => false,
                  'beforeWelcomePageRender' => false,
                ],
                'authentification' => [
                  'beforeRegister' => false,
                  'beforeRegisterForm' => false,
                  'createNewUser' => false,
                  'createRandomPassword' => false,
                  'checkPasswordRequirement' => false,
                ],
                'admin' => [
                  'afterQuickMenuLoad' => false,
                  'beforeCloseHtml' => false,
                  'beforeControllerAction' => false,
                  'beforeHasPermission' => false,
                  'beforePermissionSetSave' => false,
                  'beforeProcessFileUpload' => false,
                  'beforeUrlCheck' => false,
                  'ExpressionManagerStart' => false,
                  'getGlobalBasePermissions' => false,
                  'getValidScreenFiles' => false,
                  'listExportOptions' => false,
                  'newDirectRequest' => false,
                  'newExport' => false,
                  'NewUnsecureRequest' => false,
                  'setVariableExpressionEnd' => false,
                ],
                'surveyStatus' => [
                  'afterSurveyComplete' => false,
                  'beforeSurveyPage' => false,
                  'onSurveyDenied' => false,
                ],
                'plugin' => [
                  'afterPluginLoad' => false,
                  'getPluginTwigPath' => false,
                  'listExportPlugins' => false,
                  'listQuestionPlugins' => false,
                ],
                'dynamicmodel' => [
                  'beforeModelDelete' => false,
                  'beforeModelSave' => false,
                  'afterModelDelete' => false,
                  'afterModelSave' => false,
                ],
                'email' => [
                  'afterReceiveOAuthResponse' => false,
                  'afterSelectEmailPlugin' => false,
                  'beforeEmail' => false,
                  'beforeSurveyEmail' => false,
                  'beforeTokenEmail' => false,
                  'beforeEmailDispatch' => false,
                  'beforePrepareRedirectToAuthPage' => false,
                  'beforeRedirectToAuthPage' => false,
                  'listEmailPlugins' => false,
                  'MailerConstruct' => false,
                ],
                'dynamicsurvey' => [
                  'beforeSurveyDynamicDelete' => false,
                  'beforeSurveyDynamicSave' => false,
                  'afterSurveyDynamicDelete' => false,
                  'afterSurveyDynamicSave' => false,
                ],
                'response' => [
                  'beforeResponseDelete' => false,
                  'beforeResponseSave' => false,
                  'afterResponseDelete' => false,
                  'afterResponseSave' => false,
                ],
                'token' => [
                  'afterGenerateToken' => false,
                  'beforeTokenDelete' => false,
                  'beforeTokenSave' => false,
                  'afterTokenDelete' => false,
                  'afterTokenSave' => false,
                  'beforeTokenDynamicDelete' => false,
                  'beforeTokenDynamicSave' => false,
                  'afterTokenDynamicDelete' => false,
                  'afterTokenDynamicSave' => false,
                ],
                'surveyAdmin' => [
                  'afterFindSurvey' => false,
                  'afterSurveyActivate' => false,
                  'afterSurveyDeactivate' => false,
                  'afterSurveyQuestionAssessment' => false,
                  'afterSurveyQuota' => false,
                  'afterSurveySettingsSave' => false,
                  'beforeActivate' => false,
                  'beforeDeactivate' => false,
                  'beforeLoadResponse' => false,
                  'beforeSurveyAdminView' => false,
                  'beforeSurveyActivate' => false,
                  'beforeSurveyDeactivate' => false,
                  'beforeSurveySettings' => false,
                  'newQuestionAttributes' => false,
                  'newSurveySettings' => false,
                  'saveSurveyForm' => false,
                  'beforeSurveyDelete' => false,
                  'beforeSurveySave' => false,
                  'afterSurveyDelete' => false,
                  'afterSurveySave' => false,
                ],
            ]);
        }
        
        /**
        * Get the hidden settings name
         * @return boolean
         */
        private function isEventOn($event) {
            $bug = $this->getGlobalSetting('sBug');
            $eventsGlobalSettings=json_decode($this->getGlobalSetting(
                'events',
                $this->getDefaultEvents()
            ), true);
            foreach ($eventsGlobalSettings as $category => $events) {
                if (array_key_exists($event, $events)) {
                    $isOn = $events[$event];
                    if($bug) {
                        error_log("Category : " . $category . " | Event : " .$event . " | Event Status: " . json_encode($isOn));
                    }
                    if ($isOn) {  // Compare with boolean true, not string
                        error_log("Event Triggered: " . $event);
                    }
                    return $isOn;
                }
            }
            return false;
        }
    }

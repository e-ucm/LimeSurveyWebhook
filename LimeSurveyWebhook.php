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
				// Hook into events triggered when a survey is completed and when a survey is initialized (before the first page is loaded)
                $this->subscribe('afterSurveyComplete'); // This event will be triggered when a respondent completed the survey
                $this->subscribe('beforeSurveyPage');  // This event will be triggered when a respondent initializes the survey
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
                    'help' => 'The unique number of the surveys. You can set multiple surveys with an "," as separator. Example: 123456, 234567, 345678'
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
		* Send the webhook on completion of a survey
		* @return array | response
		***** ***** ***** ***** *****/
		public function beforeSurveyPage()
        {
            $oEvent = $this->getEvent();
            if($this->isEventOn('beforeSurveyPage')) {
                $this->callWebhookSurvey('beforeSurveyPage');
            }
            return;
        }

		/***** ***** ***** ***** *****
		* Send the webhook on completion of a survey
		* @return array | response
		***** ***** ***** ***** *****/
		public function afterSurveyComplete()
		{
            if($this->isEventOn('afterSurveyComplete')) {
                $this->callWebhookSurvey('afterSurveyComplete');
            }
            return;
		}

        /***** ***** ***** ***** *****
		* Calls a webhook
		* @return array | response
		***** ***** ***** ***** *****/
		private function callWebhook($comment, $parameters=array(), $time_start=null)
        {
            if(!$time_start) {
                $time_start=microtime(true);
            }
            $event = $this->getEvent();

            $url = $this->getGlobalSetting('sUrl');                
            // Validate webhook URL
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                error_log('Invalid webhook URL: ' . $url);
                return; // Exit if the URL is not valid
            }
            error_log($url);

            if(!key_exists("events", $parameters)) {
                $parameters["event"] = $comment;
            }
            if(!key_exists("time", $parameters)) {
                // Convert to DateTime
                $utcDateTime = DateTime::createFromFormat("U.u", number_format($time_start, 6, '.', ''));
                $utcDateTime->setTimezone(new DateTimeZone("UTC")); // Ensure it's in UTC
                error_log("UTC Time: " . $utcDateTime->format("Y-m-d H:i:s.u") . " UTC.");
                $parameters["time"] = $utcDateTime->format("Y-m-d H:i:s.u"). " UTC";
            }

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
                $event = $this->getEvent();
                $surveyId = $event->get('surveyId');
                $hookSurveyId = $this->getGlobalSetting('sId','');
                $hookSurveyIdArray = explode(',', preg_replace('/\s+/', '', $hookSurveyId));
                if (!$hookSurveyId == '') {
                    if(!in_array($surveyId, $hookSurveyIdArray)) {
                        return;
                    }
                }
                error_log($comment . " : " . $surveyId);
                $time_start=microtime(true);

                // Try to fetch the current from the URL manually or default language
                $surveyInfo = Survey::model()->findByPk($surveyId);
                $languageRequest=Yii::app()->request->getParam('lang', null);
                $lang = $languageRequest !== null ? $languageRequest : $surveyInfo->language; // Fallback to default language

                // Get token from the URL manually
                $token = Yii::app()->request->getParam('token', null);
               
                $parameters = array(
                    "survey" => $surveyId,
                    "event" => $comment,
                    'lang' => $lang,
                    "token" => $token
                );
                
                // Include response data only for completion
                if ($comment === 'afterSurveyComplete') {
                    $responseId = $event->get('responseId');
                    $parameters['responseId'] = $responseId;
                    // Fetch response data manually from the survey table
                    $response = $this->pluginManager->getAPI()->getResponse($surveyId, $responseId);
                    $parameters['response'] = $response;
                    $parameters['submitDate'] = $response['submitdate'];
                }

                return $this->callWebhook($comment, $parameters, $time_start);
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
                error_log(implode(" , ", $headers));
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
            private static function getDefaultEvents()
            {
                return json_encode([
                    'surveys' => [
                        'beforeSurveyPage' => true,
                        'afterSurveyComplete' => true,
                    ],
                ]);
            }
            /**
            * Get the hidden settings name
             * @return boolean
             */
            private function isEventOn($event) {
                $eventsGlobalSettings=json_decode($this->getGlobalSetting('events'), true);
                //error_log(json_encode($eventsGlobalSettings));
                foreach ($eventsGlobalSettings as $category => $events) {
                    if (array_key_exists($event, $events)) {
                        $isOn = $events[$event];
                        error_log("Category : " . $category . " | Event : " .$event . " | Event Status: " . json_encode($isOn));
                        if ($isOn === true) {  // Compare with boolean true, not string
                            error_log("Event Triggered: " . $event);
                            return true;
                        }
                    }
                }
                return false;
            }
    }
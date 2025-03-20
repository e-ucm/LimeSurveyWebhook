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
                'sBug' => array(
                    'type' => 'select',
                    'options' => array(
                        0 => 'No',
                        1 => 'Yes'
                    ),
                    'default' => $this->getGlobalSetting('sBug', 0),
                    'htmlOptions' => [
                        'readonly' => in_array('sBug', $fixedPluginSettings)
                    ],
                    'label' => 'Enable Debug Mode',
                    'help' => 'Enable debugmode to see what data is transmitted. Respondents will see this as well so you should turn this off for live surveys'
                )
		    );

            /* Get current */
            $pluginSettings = parent::getPluginSettings($getValues);
            error_log(json_encode($pluginSettings));
            /* Update current for fixed one */
            if ($getValues) {
                foreach ($fixedPluginSettings as $setting) {
                    $pluginSettings[$setting]['current'] = $this->getGlobalSetting($setting);
                }
            }
            error_log(json_encode($pluginSettings));
            /* Remove hidden */
            foreach ($this->getHiddenGlobalSetting() as $setting) {
                unset($pluginSettings[$setting]);
            }
            error_log(json_encode($pluginSettings));
            return $pluginSettings;
        }

        
		/***** ***** ***** ***** *****
		* Send the webhook on completion of a survey
		* @return array | response
		***** ***** ***** ***** *****/
		public function beforeSurveyPage()
        {
            $oEvent = $this->getEvent();
            $surveyId = $oEvent->get('surveyId');
            error_log("survey_initialized" . $surveyId);
            $hookSurveyId = $this->get('sId', null, null, $this->settings['sId']);
            $hookSurveyIdArray = explode(',', preg_replace('/\s+/', '', $hookSurveyId));
            if (in_array($surveyId, $hookSurveyIdArray))
                {
                    $this->callWebhook('beforeSurveyPage');
                }
            return;
        }


		/***** ***** ***** ***** *****
		* Send the webhook on completion of a survey
		* @return array | response
		***** ***** ***** ***** *****/
		public function afterSurveyComplete()
		{
            $oEvent = $this->getEvent();
            $surveyId = $oEvent->get('surveyId');
            error_log("survey_completed" . $surveyId);
            $hookSurveyId = $this->get('sId', null, null, $this->settings['sId']);
            $hookSurveyIdArray = explode(',', preg_replace('/\s+/', '', $hookSurveyId));
            if (in_array($surveyId, $hookSurveyIdArray)) {
                $this->callWebhook('afterSurveyComplete');
            }
            return;
		}

		/***** ***** ***** ***** *****
		* Calls a webhook
		* @return array | response
		***** ***** ***** ***** *****/
		private function callWebhook($comment)
			{
				$time_start = microtime(true);
				$event = $this->getEvent();
                error_log(json_encode($event));
                $surveyId = $event->get('surveyId');

                // Try to fetch the current or default language
                $surveyInfo = Survey::model()->findByPk($surveyId);
                $lang = null !== $event->get('lang') ? $event->get('lang') : $surveyInfo->language; // Fallback to default language

				$submitDate = $response['submitdate'];
				$url = $this->getGlobalSetting('sUrl');
                $hookSurveyId = $this->getGlobalSetting('sId');
                $auth = $this->getGlobalSetting('sAuthToken');
                
                // Validate webhook URL
                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    error_log('Invalid webhook URL: ' . $url);
                    return; // Exit if the URL is not valid
                }

                // Get token from the URL manually
                //$token = Yii::app()->request->getParam('token', null);
                //if($token == null) {
                //    $token = $event->get('token'); // Attempt to get from event object
                //}

                $parameters = array(
                    "api_token" => $auth,
                    "survey" => $surveyId,
                    "event" => $comment,
                    "respondId" => $responseId,
                    "response" => $response,
                    "submitDate" => $submitDate,
                    'lang' => $lang,
                    "token" => isset($sToken) ? $sToken : null
                );

                // Include response data only for completion
                if ($comment === 'survey_completed') {
                    $responseId = $event->get('responseId');
                    $parameters['responseId'] = $responseId;
                    // Fetch response data manually from the survey table
                    $response = $this->pluginManager->getAPI()->getResponse($surveyId, $responseId);
                    $parameters['response'] = $response;
                }
                $hookSent = $this->httpPost($url, $parameters);

                $this->log($comment . " | Params: ". json_encode($parameters) . json_encode($hookSent));
                $this->debug($url, $parameters, $hookSent, $time_start, $response);

                return;
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
        private function httpPost($url, $params)
            {
                $postData = $params;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, count($postData));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
            }

        /***** ***** ***** ***** *****
        * debugging
        ***** ***** ***** ***** *****/
        private function debug($url, $parameters, $hookSent, $time_start, $response)
            {
                if ($this->getGlobalSetting('sBug', 0) == 1)
                  {
                    $this->log($comment);
                    $html = '<pre><br><br>----------------------------- DEBUG ----------------------------- <br><br>';
                    $html .= 'Parameters: <br>' . print_r($parameters, true);
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
                $AuthOAuth2Settings = App()->getConfig('WebHookStatusSettings');
                if (isset($AuthOAuth2Settings['fixed'][$setting])) {
                    return $AuthOAuth2Settings['fixed'][$setting];
                }
                if (isset($AuthOAuth2Settings[$setting])) {
                    return $this->get($setting, null, null, $AuthOAuth2Settings[$setting]);
                }
                return $this->get($setting, null, null, $default);
            }

        /**
         * Get the fixed settings name
         * @return string[]
         */
        private function getFixedGlobalSetting()
            {
                $AuthOAuth2Setting = App()->getConfig('WebHookStatusSettings');
                if (isset($AuthOAuth2Setting['fixed'])) {
                    return array_keys($AuthOAuth2Setting['fixed']);
                }
                return [];
            }

        /**
         * Get the hidden settings name
         * @return string[]
         */
        private function getHiddenGlobalSetting()
            {
                $AuthOAuth2Setting = App()->getConfig('AuthOAuth2Settings');
                if (isset($AuthOAuth2Setting['hidden'])) {
                    return $AuthOAuth2Setting['hidden'];
                }
                return [];
            }
    }
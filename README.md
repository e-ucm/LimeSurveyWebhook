# Webhook for LimeSurvey
This is a simple Plugin for [LimeSurvey](https://github.com/LimeSurvey/LimeSurvey) that allows you to send a post request on each event.

This plugin is a fork of the uncontinued [zesthook](https://github.com/evently-nl/zesthook) and can easily be adapted to suit your own needs.

This LimeSurvey plugin allows you to send survey-related events to an external **webhook endpoint**. Below is a full description of the available configuration options.

## Installation
- go to [releases](https://github.com/e-ucm/LimeSurveyWebhook/releases) and download the latest release Zip archive
- for LimeSurvey 5.x and above: upload the Zip archive in the plugin manager
- configure the plugin in the plugin manager
- activate the plugin in the plugin manager

To test the latest development version `git clone` [this repository](https://github.com/e-ucm/LimeSurveyWebhook.git)
into `<limesurvey_root>/plugins/LimeSurveyWebhook/`.

# Configuration

Before activating the plugin open its configuration from the plugin manager or create your own configuration in `application/config/config.php` file

### 🔧 General Settings

| Key | Type | Description |
|-----|------|-------------|
| `sUrl` | `string` | **The default webhook URL** where events will be sent. You can use tools like https://webhook.site for testing. |
| `sId` | `string` | **Survey ID filter.** Enter one or more survey IDs separated by commas (e.g. `123456,234567`). Leave empty to send events for **all surveys**. |
| `sAuthToken` | `string` | **Optional authentication token** added to the request for API verification. _Note: this value is sent in plain text (not encoded)._ |
| `sHeaderSignatureName` | `string` | **Header name for signature validation**, if used. Default: `X-Signature-SHA256`. |
| `sHeaderSignaturePrefix` | `string` | **Optional prefix** added before the signature value (e.g. `sha256=`). |
| `sBug` | `checkbox` | **Enable debug mode.** When enabled, the transmitted webhook payload is **displayed directly to the respondent**. _Only enable this during testing._ |


### 🧠 Event Configuration (`events`)

The `events` field allows you to define **which LimeSurvey events should trigger webhook calls**. This is configured as a **JSON object**.

#### Example Structure

```json
{
    "render": { ... },
    "authentication": { ... },
    "admin": { ... },
    "surveyStatus": {
        "beforeSurveyPage": true,
        "afterSurveyComplete": false
    },
    "plugin": { ... }
}
```

## Default of fixed configuration

You can set default configuration by array in config part of LimeSurvey config file.

The config are set at `WebhookSettings` key with array of settings by name. For fixed config part you use an array with settings name in `fixed` array. If you want to hide some element from gui, you can use `hidden` array.

For example :
```php
	// Update default LimeSurvey config here
	'WebhookSettings' => [
            'fixed' => [
                'sUrl' => 'https://example.com/webhook',
                'sAuthToken' => 'apitoken',
                'sHeaderSignatureName' => 'X-Webhook-Sig',
                'sHeaderSignaturePrefix' => 'pref-',
                'sId'=> '',
                'events' => '{
                    "surveyStatus":{
                        "afterSurveyComplete":true,
                        "beforeSurveyPage":true
                    }
                }'
            ],
            'hidden' => ['sAuthToken'],
            'sBug' => '0'
        ],
```

# Supported LimeSurvey Versions

This plugin was tested with

- A recent version v6.4.3 (PHP 8.1)
- the latest stable release v5.2.5

and should work with all version 5.x or newer.

The minimum required PHP version is 8.1.

## Usage
You are free to use/change/fork this code for your own products.
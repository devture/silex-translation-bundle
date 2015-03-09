# Description

Finds all `{sourceLanguage}.json` files (example: `en.json`) in a given base directory (`base_path`)
and allows these files to be translated to all given languages (`locales`).

A `{targetLanguage}.json` file is generated and saved next to `{sourceLanguage}.json` for each locale, whenever the translations for it are saved.

A `{targetLanguage}.json.hint` file is also saved in the same directory.
It contains "hints" telling the translation system which source translation string a given translation is derived from.
This is so that a translation can be considered outdated if the source translation string changes.
At this moment, such outdated translations are considered new and untranslated.

# Installation

## Permissions

Since the translation system needs to save translation files in the project, we need to grant file-writing privileges
to the web server user.

Example:

	$ find /srv/http/my-project/src -type d -name translations | xargs chown :http
	$ find /srv/http/my-project/src -type d -name translations | xargs chmod g+w

## Configuration

TranslationBundle config:

	"TranslationBundle": {
		"source_language_locale_key": "en",
		"base_path": "%app_base_path%",
		"locales": "%locales%"
	}

TranslationBundle config example:

	"TranslationBundle": {
		"source_language_locale_key": "en",
		"base_path": "/srv/http/my-project/src",
		"locales": {
			"en": {"key": "en", "name": "English"},
			"bg": {"key": "bg", "name": "Bulgarian"},
			"bg": {"key": "ja", "name": "Japanese"}
		}
	}


## Initialization

Only set-up during debug to protect the production environment:

	if ($app['debug']) {
		$app->register(new \Devture\Bundle\TranslationBundle\ServicesProvider('devture_translation', $app['config']['TranslationBundle']));
		$app->mount('/admin/{locale}/translation/', $app['devture_translation.controllers_provider.management']);
		$app['devture_user.access_control']->requireRoleForRoutePrefix('devture_translation.', 'devture_translation');
	}

The main route would be `devture_translation.manage` (assuming the `devture_translation` namespace was used, as done above).


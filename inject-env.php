<?php
if (php_sapi_name() !== 'cli') {
	exit('This script should not be called directly from/via web server');
}

//if (file_exists('config.inc.php')) exit();

include 'lib/pkp/classes/config/ConfigParser.inc.php';

$config = ConfigParser::readConfig(__DIR__ . '/config.TEMPLATE.inc.php');

function override_cfg($env_key, $cfg_group, $cfg_key) {
	global $config;
	$cfg_value = getenv($env_key);

	if (!isset($cfg_value) || !strlen($cfg_value)) return;

	if (is_numeric($config[$cfg_group][$cfg_key])) $config[$cfg_group][$cfg_key] = intval($cfg_value);
	else $config[$cfg_group][$cfg_key] = $cfg_value;
}

$config['general']['installed'] = 'On';

override_cfg(env_key: 'OJS_BASE_URL', cfg_group: 'general', cfg_key: 'base_url');
override_cfg(env_key: 'OJS_SESSION_COOKIE_NAME', cfg_group: 'general', cfg_key: 'session_cookie_name');
override_cfg(env_key: 'OJS_SESSION_LIFETIME', cfg_group: 'general', cfg_key: 'session_lifetime');
override_cfg(env_key: 'OJS_SESSION_SAMESITE', cfg_group: 'general', cfg_key: 'session_samesite');
override_cfg(env_key: 'OJS_SCHEDULED_TASKS', cfg_group: 'general', cfg_key: 'scheduled_tasks');
override_cfg(env_key: 'OJS_SCHEDULED_TASKS_REPORT_ERROR_ONLY', cfg_group: 'general', cfg_key: 'scheduled_tasks_report_error_only');
override_cfg(env_key: 'OJS_TIMEZONE', cfg_group: 'general', cfg_key: 'time_zone');
override_cfg(env_key: 'OJS_ALLOWED_HOSTS', cfg_group: 'general', cfg_key: 'allowed_hosts');

$database_cfg = getenv('OJS_DATABASE_URI');

if (isset($database_cfg) && $database_cfg && strlen($database_cfg)) {
	$database_uri = parse_url($database_cfg);

	$config['database']['host'] = $database_uri['host'];
	if (isset($database_uri['port']) && $database_uri['port'])
		$config['database']['port'] = $database_uri['port'];
	$config['database']['username'] = $database_uri['user'];
	$config['database']['password'] = $database_uri['pass'];

	if (isset($database_uri['path'])) {
		$path = trim(preg_replace('/^\\/+/', '', $database_uri['path']));

		if (strlen($path) && !str_contains($path, '/'))
			$config['database']['name'] = $path;
	}
}

override_cfg(env_key: 'OJS_LOCALE', cfg_group: 'i18n', cfg_key: 'locale');
override_cfg(env_key: 'OJS_ENCRYPTION', cfg_group: 'security', cfg_key: 'encryption');
override_cfg(env_key: 'OJS_SALT', cfg_group: 'security', cfg_key: 'salt');
override_cfg(env_key: 'OJS_API_KEY_SECRET', cfg_group: 'security', cfg_key: 'api_key_secret');
override_cfg(env_key: 'OJS_RECAPTCHA', cfg_group: 'captcha', cfg_key: 'recaptcha');
override_cfg(env_key: 'OJS_RECAPTCHA_PUBLIC_KEY', cfg_group: 'captcha', cfg_key: 'recaptcha_public_key');
override_cfg(env_key: 'OJS_RECAPTCHA_PRIVATE_KEY', cfg_group: 'captcha', cfg_key: 'recaptcha_private_key');
override_cfg(env_key: 'OJS_CAPTCHA_ON_REGISTER', cfg_group: 'captcha', cfg_key: 'captcha_on_register');
override_cfg(env_key: 'OJS_RECAPTCHA_ENFORCE_HOSTNAME', cfg_group: 'captcha', cfg_key: 'recaptcha_enforce_hostname');

$new_config = [
	'; <?php exit(); // DO NOT DELETE ?>'
];

foreach ($config as $cfg_group => $cfg_keys) {
	array_push($new_config, '[' . $cfg_group . ']');

	foreach ($cfg_keys as $cfg_key => $cfg_value)
		array_push($new_config, $cfg_key . ' = ' . $cfg_value);
}

file_put_contents(__DIR__ . '/config.inc.php', implode("\n", $new_config));

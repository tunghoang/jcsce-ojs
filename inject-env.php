<?php
if (php_sapi_name() !== 'cli') {
	exit('This script should not be called directly from/via web server');
}

if (file_exists('config.inc.php')) exit();

include 'lib/pkp/classes/config/ConfigParser.inc.php';

$config = ConfigParser::readConfig(__DIR__ . '/config.TEMPLATE.inc.php');

function override_cfg($env_key, $cfg_group, $cfg_key, $is_ojs_debug = false) {
	global $config;
	$cfg_value = getenv($env_key);

	if ($is_ojs_debug) {
		if (!isset($cfg_value) || !strlen($cfg_value)) return;
		$config[$cfg_group][$cfg_key] = $cfg_value == 0 ? 'Off' : 'On';
		return;
	}

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
override_cfg(env_key: 'OJS_DATE_FORMAT_SHORT', cfg_group: 'general', cfg_key: 'date_format_short');
override_cfg(env_key: 'OJS_DATE_FORMAT_LONG', cfg_group: 'general', cfg_key: 'date_format_long');
override_cfg(env_key: 'OJS_DATETIME_FORMAT_SHORT', cfg_group: 'general', cfg_key: 'datetime_format_short');
override_cfg(env_key: 'OJS_DATETIME_FORMAT_LONG', cfg_group: 'general', cfg_key: 'datetime_format_long');
override_cfg(env_key: 'OJS_TIME_FORMAT', cfg_group: 'general', cfg_key: 'time_format');
override_cfg(env_key: 'OJS_DISABLE_PATH_INFO', cfg_group: 'general', cfg_key: 'disable_path_info');
override_cfg(env_key: 'OJS_ALLOW_URL_FOPEN', cfg_group: 'general', cfg_key: 'allow_url_fopen');
override_cfg(env_key: 'OJS_RESTFUL_URLS', cfg_group: 'general', cfg_key: 'restful_urls');
override_cfg(env_key: 'OJS_ALLOWED_HOSTS', cfg_group: 'general', cfg_key: 'allowed_hosts');
override_cfg(env_key: 'OJS_TRUST_X_FORWARDED_FOR', cfg_group: 'general', cfg_key: 'trust_x_forwarded_for');
override_cfg(env_key: 'OJS_SHOW_UPGRADE_WARNING', cfg_group: 'general', cfg_key: 'show_upgrade_warning');
override_cfg(env_key: 'OJS_ENABLE_MINIFIED', cfg_group: 'general', cfg_key: 'enable_minified');
override_cfg(env_key: 'OJS_ENABLE_BEACON', cfg_group: 'general', cfg_key: 'enable_beacon');
override_cfg(env_key: 'OJS_SITEWIDE_PRIVACY_STATEMENT', cfg_group: 'general', cfg_key: 'sitewide_privacy_statement');

$database_cfg = getenv('OJS_DATABASE_URI');

if (isset($database_cfg) && $database_cfg && strlen($database_cfg)) {
	$database_uri = parse_url($database_cfg);

	$config['database']['host'] = $database_uri['host'];
	if (isset($database_uri['port']) && $database_uri['port'])
		$config['database']['port'] = $database_uri['port'];
	$config['database']['username'] = $database_uri['user'];
	$config['database']['password'] = $database_uri['pass'];

	if (isset($database_uri['query'])) {
		$database_uri_params = [];
		parse_str($database_uri['query'], $database_uri_params);

		if (isset($database_uri_params['collation']))
			$config['database']['collation'] = $database_uri_params['collation'];
	}

	if (isset($database_uri['path'])) {
		$path = trim(preg_replace('/^\\/+/', '', $database_uri['path']));

		if (strlen($path) && !str_contains($path, '/'))
			$config['database']['name'] = $path;
	}
}
override_cfg(env_key: 'OJS_DEBUG', cfg_group: 'database', cfg_key: 'debug', is_ojs_debug: true);

override_cfg(env_key: 'OJS_OBJECT_CACHE', cfg_group: 'cache', cfg_key: 'object_cache');
override_cfg(env_key: 'OJS_MEMCACHE_HOSTNAME', cfg_group: 'cache', cfg_key: 'memcache_hostname');
override_cfg(env_key: 'OJS_MEMCACHE_PORT', cfg_group: 'cache', cfg_key: 'memcache_port');
override_cfg(env_key: 'OJS_WEB_CACHE', cfg_group: 'cache', cfg_key: 'web_cache');
override_cfg(env_key: 'OJS_WEB_CACHE_HOURS', cfg_group: 'cache', cfg_key: 'web_cache_hours');

override_cfg(env_key: 'OJS_LOCALE', cfg_group: 'i18n', cfg_key: 'locale');
override_cfg(env_key: 'OJS_CLIENT_CHARSET', cfg_group: 'i18n', cfg_key: 'client_charset');
override_cfg(env_key: 'OJS_CONNECTION_CHARSET', cfg_group: 'i18n', cfg_key: 'connection_charset');

override_cfg(env_key: 'OJS_FILES_DIR', cfg_group: 'files', cfg_key: 'files_dir');
override_cfg(env_key: 'OJS_PUBLIC_FILES_DIR', cfg_group: 'files', cfg_key: 'public_files_dir');
override_cfg(env_key: 'OJS_PUBLIC_USER_DIR_SIZE', cfg_group: 'files', cfg_key: 'public_user_dir_size');
override_cfg(env_key: 'OJS_UMASK', cfg_group: 'files', cfg_key: 'umask');

override_cfg(env_key: 'OJS_FORCE_SSL', cfg_group: 'security', cfg_key: 'force_ssl');
override_cfg(env_key: 'OJS_FORCE_LOGIN_SSL', cfg_group: 'security', cfg_key: 'force_login_ssl');
override_cfg(env_key: 'OJS_SESSION_CHECK_IP', cfg_group: 'security', cfg_key: 'session_check_ip');
override_cfg(env_key: 'OJS_ENCRYPTION', cfg_group: 'security', cfg_key: 'encryption');
override_cfg(env_key: 'OJS_SALT', cfg_group: 'security', cfg_key: 'salt');
override_cfg(env_key: 'OJS_API_KEY_SECRET', cfg_group: 'security', cfg_key: 'api_key_secret');
override_cfg(env_key: 'OJS_RESET_SECONDS', cfg_group: 'security', cfg_key: 'reset_seconds');
override_cfg(env_key: 'OJS_ALLOWED_HTML', cfg_group: 'security', cfg_key: 'allowed_html');

override_cfg(env_key: 'OJS_TIME_BETWEEN_EMAILS', cfg_group: 'email', cfg_key: 'time_between_emails');
override_cfg(env_key: 'OJS_MAX_RECIPIENTS', cfg_group: 'email', cfg_key: 'max_recipients');
override_cfg(env_key: 'OJS_REQUIRE_VALIDATION', cfg_group: 'email', cfg_key: 'require_validation');
override_cfg(env_key: 'OJS_VALIDATION_TIMEOUT', cfg_group: 'email', cfg_key: 'validation_timeout');

override_cfg(env_key: 'OJS_MIN_WORD_LENGTH', cfg_group: 'search', cfg_key: 'min_word_length');
override_cfg(env_key: 'OJS_RESULTS_PER_KEYWORD', cfg_group: 'search', cfg_key: 'results_per_keyword');

override_cfg(env_key: 'OJS_OAI', cfg_group: 'oai', cfg_key: 'oai');
override_cfg(env_key: 'OJS_REPOSITORY_ID', cfg_group: 'oai', cfg_key: 'repository_id');
override_cfg(env_key: 'OJS_OAI_MAX_RECORDS', cfg_group: 'oai', cfg_key: 'oai_max_records');

override_cfg(env_key: 'OJS_ITEMS_PER_PAGE', cfg_group: 'interface', cfg_key: 'items_per_page');
override_cfg(env_key: 'OJS_PAGE_LINKS', cfg_group: 'interface', cfg_key: 'page_links');

override_cfg(env_key: 'OJS_RECAPTCHA', cfg_group: 'captcha', cfg_key: 'recaptcha');
override_cfg(env_key: 'OJS_RECAPTCHA_PUBLIC_KEY', cfg_group: 'captcha', cfg_key: 'recaptcha_public_key');
override_cfg(env_key: 'OJS_RECAPTCHA_PRIVATE_KEY', cfg_group: 'captcha', cfg_key: 'recaptcha_private_key');
override_cfg(env_key: 'OJS_CAPTCHA_ON_REGISTER', cfg_group: 'captcha', cfg_key: 'captcha_on_register');
override_cfg(env_key: 'OJS_RECAPTCHA_ENFORCE_HOSTNAME', cfg_group: 'captcha', cfg_key: 'recaptcha_enforce_hostname');

override_cfg(env_key: 'OJS_EMAIL_SMTP', cfg_group: 'email', cfg_key: 'smtp');
override_cfg(env_key: 'OJS_EMAIL_SMTP_SERVER', cfg_group: 'email', cfg_key: 'smtp_server');
override_cfg(env_key: 'OJS_EMAIL_SMTP_PORT', cfg_group: 'email', cfg_key: 'smtp_port');
override_cfg(env_key: 'OJS_EMAIL_SMTP_USERNAME', cfg_group: 'email', cfg_key: 'smtp_username');
override_cfg(env_key: 'OJS_EMAIL_SMTP_PASSWORD', cfg_group: 'email', cfg_key: 'smtp_password');

override_cfg(env_key: 'OJS_EMAIL_REQUIRE_VALIDATION', cfg_group: 'email', cfg_key: 'require_validation');

override_cfg(env_key: 'OJS_TAR', cfg_group: 'cli', cfg_key: 'tar');
override_cfg(env_key: 'OJS_XSLT_COMMAND', cfg_group: 'cli', cfg_key: 'xslt_command');

override_cfg(env_key: 'OJS_SHOW_STACKTRACE', cfg_group: 'debug', cfg_key: 'show_stacktrace');
override_cfg(env_key: 'OJS_DISPLAY_ERRORS', cfg_group: 'debug', cfg_key: 'display_errors');
override_cfg(env_key: 'OJS_DEPRECATION_WARNINGS', cfg_group: 'debug', cfg_key: 'deprecation_warnings');
override_cfg(env_key: 'OJS_LOG_WEB_SERVICE_INFO', cfg_group: 'debug', cfg_key: 'log_web_service_info');

$new_config = [
	'; <?php exit(); // DO NOT DELETE ?>'
];

foreach ($config as $cfg_group => $cfg_keys) {
	array_push($new_config, '[' . $cfg_group . ']');

	foreach ($cfg_keys as $cfg_key => $cfg_value)
		array_push($new_config, $cfg_key . ' = ' . $cfg_value);
}

file_put_contents(__DIR__ . '/config.inc.php', implode("\n", $new_config));

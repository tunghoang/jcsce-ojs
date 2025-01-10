# JCSCE Docker Image

## Build the image

The project has a npm script to automatically build docker images, run this script at the project's root.

```sh
npm run containerize
```

Composer can sometimes download dependencies from their source, i.e. remote SCM like Github or Gitlab, which has their own rate limiting rule. Composer recommends using Github access token to bypass this rate limiting rule, see [this](https://getcomposer.org/doc/articles/authentication-for-private-packages.md).

To use access token during build process:

```sh
npm run containerize -- --build-arg COMPOSER_TOKEN=ghp_xxxxxxxxxxx
```

Built container image should be tagged as `jcsce-ojs:$npm_package_version`.

## Environment variables

> More variables can be made available, contact for update.

We made OJS more cloud-native by injecting environment variables into running container's config.inc.php file, to specify environment variables, use:

```sh
docker run -e OJS_BASE_URL=https://ojs.yourdomain.com jcsce-ojs:<tag>
```

Full supported environment variable list:

`OJS_BASE_URL`: Map to `general.base_url`.

`OJS_SESSION_COOKIE_NAME`: Map to `general.session_cookie_name`.

`OJS_SESSION_LIFETIME`: Map to `general.session_lifetime`.

`OJS_SESSION_SAMESITE`: Map to `general.session_samesite`.

`OJS_SCHEDULED_TASKS`: Map to `general.scheduled_tasks`.

`OJS_SCHEDULED_TASKS_REPORT_ERROR_ONLY`: Map to `general.scheduled_tasks_report_error_only`.

`OJS_TIMEZONE`: Map to `general.timezone`.

`OJS_ALLOWED_HOSTS`: Map to `general.allowed_hosts`.

`OJS_DATABASE_URI`: Map to `database.*`, use full database connection URI format: `mysql://username:password@host[:port][/database]`.

`OJS_LOCALE`: Map to `i18n.locale`.

`OJS_ENCRYPTION`: Map to `security.encryption`.

`OJS_SALT`: Map to `security.salt`.

`OJS_API_KEY_SECRET`: Map to `security.api_key_secret`.

`OJS_EMAIL_BOX`: Map to `email.*`, use full email URI format: `smtp://uri_encoded_username@uri_encoded_password@smtp_host[:smtp_port]`

`OJS_RECAPTCHA`: Map to `captcha.*`, use full captcha configuration URI format: `public_key:private_key?[on_register=(on|off)][&enforce_hostname=(on|off)]`.

`OJS_DEBUG`: Set the level of debug logging:
- `0`: No debug log.
- `1`: `log_web_service_info=on`.
- `2`: `deprecation_warnings=on`.
- `3`: `display_errors=on`.
- `4`: `show_stacktrace=on`

# Persistency

OJS stores uploaded resources (journals, photos) locally, you **MUST** mount their data volume somewhere on your physical server to prevent data loss after restarting container.

```sh
docker run -v /path/to/physical/location:/var/www/html/files jcsce-ojs:<tag>
```

# Keep OJS running in background

```sh
docker run -d --restart always jcsce-ojs:<tag>
```

# Forward container's HTTP port to host

```sh
docker run -p 8080:80 jcsce-ojs:<tag>
```

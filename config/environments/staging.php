<?php

/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */

Config::define('DISALLOW_INDEXING', true);

// Constants below this line are added/updated by the Vivid kickstart script
Config::define('WP_MEMORY_LIMIT', env('WP_MEMORY_LIMIT') ?: '512M');
Config::define('WP_DEFAULT_THEME', env('WP_DEFAULT_THEME') ?: 'nova');
Config::define('WP_DEVELOPMENT_MODE', env('WP_DEVELOPMENT_MODE') ?: 'theme');

//Config::define('NV_SYNC_DISABLED', env('NV_SYNC_DISABLED') ?: false);
Config::define('NV_SYNC_API_USER', env('NV_SYNC_API_USER') ?: 'api_user_placeholder');
Config::define('NV_SYNC_API_TOKEN', env('NV_SYNC_API_TOKEN') ?: 'AUTH_KEY=\'=RnQAPa[a-g-J/:+3.+H!b2Jd4&]bN1!%={.c_M:i3?F}LObAQrdT7w.&PuYyvYg\'');

Config::define('WP_REDIS_PORT', env('WP_REDIS_PORT') ?: 6379);
Config::define('WP_REDIS_HOST', env('WP_REDIS_HOST') ?: '127.0.0.1');
// WP_CACHE_KEY_SALT should preferentially come from .env if defined there
Config::define('WP_CACHE_KEY_SALT', env('WP_CACHE_KEY_SALT') ?: 'SECURE_AUTH_KEY=\'jGSX<OEI}Ey2=<Q+M&`RachFZ}&_zOQLQrzj}5!FM_EdRMPzcAhk@%{VGJMt#H}S\'');


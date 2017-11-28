<?php

/**
 * @file
 * Contains docker.env.settings.php.
 */

### amazee.io Database connection
if (getenv('AMAZEEIO_SITENAME')) {
  $databases['default']['default'] = array(
    'driver' => 'mysql',
    'database' => getenv('AMAZEEIO_SITENAME'),
    'username' => getenv('AMAZEEIO_DB_USERNAME'),
    'password' => getenv('AMAZEEIO_DB_PASSWORD'),
    'host' => getenv('AMAZEEIO_DB_HOST'),
    'port' => getenv('AMAZEEIO_DB_PORT'),
    'prefix' => '',
  );
}

### amazee.io Solr connection
// WARNING: you have to create a search_api server having "solr" machine name at
// /admin/config/search/search-api/add-server to make this work.
if (getenv('AMAZEEIO_SOLR_HOST') && getenv('AMAZEEIO_SOLR_PORT')) {
  $config['search_api.server.solr']['backend_config']['host'] = getenv('AMAZEEIO_SOLR_HOST');
  $config['search_api.server.solr']['backend_config']['path'] = '/solr/' . (getenv('AMAZEEIO_SOLR_CORE') ?: getenv('AMAZEEIO_SITENAME')) . '/';
  $config['search_api.server.solr']['backend_config']['port'] = getenv('AMAZEEIO_SOLR_PORT');
  $config['search_api.server.solr']['backend_config']['http_user'] = (getenv('AMAZEEIO_SOLR_USER') ?: '');
  $config['search_api.server.solr']['backend_config']['http']['http_user'] = (getenv('AMAZEEIO_SOLR_USER') ?: '');
  $config['search_api.server.solr']['backend_config']['http_pass'] = (getenv('AMAZEEIO_SOLR_PASSWORD') ?: '');
  $config['search_api.server.solr']['backend_config']['http']['http_pass'] = (getenv('AMAZEEIO_SOLR_PASSWORD') ?: '');
  $config['search_api.server.solr']['name'] = 'AmazeeIO Solr - Environment: ' . getenv('AMAZEEIO_SITE_ENVIRONMENT');
}

### amazee.io Varnish & Reverse proxy settings
if (getenv('AMAZEEIO_VARNISH_HOSTS') && getenv('AMAZEEIO_VARNISH_SECRET')) {
  $varnish_hosts = explode(',', getenv('AMAZEEIO_VARNISH_HOSTS'));
  array_walk($varnish_hosts, function(&$value, $key) { $value .= ':6082'; });

  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_addresses'] = array_merge(explode(',', getenv('AMAZEEIO_VARNISH_HOSTS')), array('127.0.0.1'));

  $config['varnish.settings']['varnish_control_terminal'] = implode($varnish_hosts, " ");
  $config['varnish.settings']['varnish_control_key'] = getenv('AMAZEEIO_VARNISH_SECRET');
  $config['varnish.settings']['varnish_version'] = 4;
}

### Temp directory
if (getenv('AMAZEEIO_TMP_PATH')) {
  $config['system.file']['path']['temporary'] = getenv('AMAZEEIO_TMP_PATH');
}

### Hash Salt
if (getenv('AMAZEEIO_HASH_SALT')) {
  $settings['hash_salt'] = getenv('AMAZEEIO_HASH_SALT');
}

// Show all error messages on the site
$config['system.logging']['error_level'] = 'all';
// Disable Google Analytics from sending dev GA data.
$config['google_analytics.settings']['account'] = 'UA-XXXXXXXX-YY';
// Expiration of cached pages to 0
$config['system.performance']['cache']['page']['max_age'] = 0;
// Aggregate CSS files on
$config['system.performance']['css']['preprocess'] = 0;
// Aggregate JavaScript files on
$config['system.performance']['js']['preprocess'] = 0;

// Set the default URL for the german language.
$language_settings['de']['local_url'] = getenv('AMAZEEIO_SITE_URL');

### Trusted Host Patterns, see https://www.drupal.org/node/2410395 for more information.
### If your site runs on multiple domains, you need to add these domains here
$settings['trusted_host_patterns'] = array(
  '^' . str_replace('.', '\.', getenv('AMAZEEIO_SITE_URL')) . '$',
);
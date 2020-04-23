<?php

# when the pod starts up the following settings are exported as env vars 
# by vaultenv or similar

// connection to RDS database
$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => getenv('DB_DATABASE'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
  'prefix' => 'drupal_',
  'collation' => 'utf8mb4_general_ci', // For Drupal 8
  'pdo' => array()
);

// s3fs settings 
// We avoid having keys here by using instance profiles
$config['s3fs.settings']['use_instance_profile'] = TRUE;
$config['s3fs.settings']['use_path_style_endpoint'] = FALSE;
$settings['s3fs.bucket'] = getenv('AWS_BUCKET_NAME');
$settings['s3fs.region'] = getenv('AWS_REGION');
$config['s3fs.settings']['bucket'] = getenv('AWS_BUCKET_NAME');
$settings['s3fs.use_s3_for_public'] = TRUE;
$settings['php_storage']['twig']['directory'] = '../storage/php';

// Sendgrid integration API key
$config['sendgrid_integration.settings']['apikey'] = getenv('SENDGRID_API_KEY');

// Ensure the devel config environment is off
$config['config_split.config_split.devel']['status'] = FALSE;
// Ensure the correct CRM config environment is active
$config['config_split.config_split.crm']['status'] = FALSE;
$config['config_split.config_split.crm_prod']['status'] = TRUE;
$config['config_split.config_split.crm_stage']['status'] = TRUE;

$settings['trusted_host_patterns'] = array('^staging\.connect\.nationalleadership\.gov\.uk$');

// enable s3fs for css and js
$config['system.performance']['css']['preprocess'] = getenv('PREPROCESS_CSS') ? getenv('PREPROCESS_CSS') : false;
$config['system.performance']['js']['preprocess'] = getenv('PREPROCESS_JS') ? getenv('PREPROCESS_JS') : false;

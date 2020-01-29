<?php

# when the pod starts up the following settings are exported as env vars 
# by vault-env-porter or similar

// connection to RDS database
$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => getenv('DB_DATABASE'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci', // For Drupal 8
  'pdo' => array()
);

// s3fs settings 
// perhaps we can avoid having AWS keys altogether by using policies / roles 
$settings['s3fs.secret_key'] = getenv('AWS_SECRET_ACCESS_KEY');
$settings['s3fs.access_key'] = getenv('AWS_ACCESS_KEY_ID');
$settings['s3fs.bucket'] = getenv('AWS_BUCKET_NAME');
$settings['s3fs.region'] = getenv('AWS_REGION');
$config['s3fs.settings']['bucket'] = getenv('AWS_BUCKET_NAME');
$settings['s3fs.use_s3_for_public'] = TRUE;

// Sendgrid integration API key
$config['sendgrid_integration.settings']['apikey'] = getenv('SENDGRID_API_KEY');

// Ensure the devel config environment is off
$config['config_split.config_split.devel']['status'] = FALSE;
// Ensure the correct CRM config environment is active
$config['config_split.config_split.crm']['status'] = FALSE;
$config['config_split.config_split.crm_prod']['status'] = TRUE;
$config['config_split.config_split.crm_stage']['status'] = FALSE;



<?php

# when the pod starts up the following settings are exported as env vars 
# by (vault-env-porter?) # TODO finalise how 
#

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

# following are TODO
$s3bucketCreds = $vcapServices['aws-s3-bucket']['credentials'];

$settings['s3fs.access_key'] = $s3bucketCreds['aws_access_key_id'];
$settings['s3fs.secret_key'] = $s3bucketCreds['aws_secret_key_id'];
$settings['s3fs.bucket'] = $s3bucketCreds['bucket_name'];
$settings['s3fs.region'] = $s3bucketCreds['aws_region'];
$config['s3fs.settings']['bucket'] = $s3bucketCreds['bucket_name'];
$settings['s3fs.use_s3_for_public'] = TRUE;

// Sendgrid integration API key
$config['sendgrid_integration.settings']['apikey'] = getenv('SG_API_KEY');

// Ensure the devel config environment is off
$config['config_split.config_split.devel']['status'] = FALSE;

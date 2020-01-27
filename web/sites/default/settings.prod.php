<?php

$vcapServices = json_decode(getenv('VCAP_SERVICES'), true);
$mysqlCreds = $vcapServices['mysql'][0]['credentials'];

$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => $mysqlCreds['name'],
  'username' => $mysqlCreds['username'],
  'password' => $mysqlCreds['password'],
  'host' => $mysqlCreds['host'],
  'port' => $mysqlCreds['port'],
  'prefix' => 'drupal_',
  'collation' => 'utf8mb4_general_ci', // For Drupal 8
  'pdo' => array(PDO::MYSQL_ATTR_SSL_CAPATH => '/etc/ssl/certs')
);

$s3bucketCreds = $vcapServices['aws-s3-bucket'][0]['credentials'];

$settings['s3fs.access_key'] = $s3bucketCreds['aws_access_key_id'];
$settings['s3fs.secret_key'] = $s3bucketCreds['aws_secret_access_key'];
$settings['s3fs.bucket'] = $s3bucketCreds['bucket_name'];
$settings['s3fs.region'] = $s3bucketCreds['aws_region'];
$config['s3fs.settings']['bucket'] = $s3bucketCreds['bucket_name'];
$settings['s3fs.use_s3_for_public'] = TRUE;
$settings['s3fs.upload_as_private'] = TRUE;

// Sendgrid integration API key
$config['sendgrid_integration.settings']['apikey'] = getenv('SG_API_KEY');

// Ensure the devel config environment is off
$config['config_split.config_split.devel']['status'] = FALSE;
// Ensure the correct CRM config environment is active
$config['config_split.config_split.crm']['status'] = TRUE;
$config['config_split.config_split.crm_stage']['status'] = FALSE;

if (getenv('CONNECT_DOMAIN')) {
  $settings['trusted_host_patterns'] = [getenv('CONNECT_DOMAIN')];
}

/*****************************************
 * ELASTICSEARCH FROM PaaS VCAP_SERVICES *
 *****************************************/

$esCreds = $vcapServices['elasticsearch'][0]['credentials'];
$settings['elasticsearch_connector.cluster.live_cluster']['url'] = $esCreds['uri'];

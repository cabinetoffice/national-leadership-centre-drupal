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
$settings['s3fs.secret_key'] = $s3bucketCreds['aws_secret_key_id'];
$config['s3fs.settings']['bucket'] = $s3bucketCreds['bucket_name'];
<?php
global $wpdb;

$table_prefix = $wpdb->prefix . "esign_";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // UPgrade Documents Table
  

// existing user table is being updated. 
$sql_update_user_table = "ALTER TABLE ". $table_prefix ."users
ADD COLUMN `is_admin` SMALLINT(6) NOT NULL AFTER `last_name`,
ADD COLUMN `is_signer` SMALLINT(6) NOT NULL AFTER `is_admin`,
ADD COLUMN `is_sa` SMALLINT(6) NOT NULL AFTER `is_signer`,
ADD COLUMN `is_inactive` SMALLINT(6) NOT NULL AFTER `is_sa`;";

$wpdb->query($sql_update_user_table);

// document events table upgrade scripts 

$sql_update_event_table = "ALTER TABLE ". $table_prefix ."documents_events
ADD COLUMN `ip_address` varchar(20) NOT NULL AFTER `date`;";

$wpdb->query($sql_update_event_table );



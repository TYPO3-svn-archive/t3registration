#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
  tx_t3registration_privacy tinyint(3) DEFAULT '0' NOT NULL,
  tx_t3registration_marketing_permission tinyint(3) DEFAULT '0' NOT NULL,
  admin_disable tinyint(3) DEFAULT '0' NOT NULL,
    user_auth_code varchar(50) DEFAULT '' NOT NULL,
    admin_auth_code varchar(50) DEFAULT '' NOT NULL
);
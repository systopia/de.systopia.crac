-- central data structure recording the timestamps
CREATE TABLE IF NOT EXISTS `civicrm_resource_access` (
     `entity_id`          int unsigned      COMMENT 'entity resource being used/edited',
     `entity_table`       varchar(128)      COMMENT 'entity resource being used/edited',
     `contact_id`         int unsigned      COMMENT 'FK to Contact ID that is using/editing the resource',
     `using_since`        timestamp    NULL COMMENT 'when did the editing of that resource start?',
     `timeout`            timestamp    NULL COMMENT 'when will the resource time out',
    PRIMARY KEY (entity_id,entity_table,contact_id),
    INDEX `timeout` (timeout),
    CONSTRAINT FK_civicrm_crac_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

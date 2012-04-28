-- Companies that manufacture a Product or offer a Service --
CREATE TABLE IF NOT EXISTS `company` (
  `company_id` BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `company_title` VARCHAR(100) NOT NULL,
  `company_image` VARCHAR(100) NOT NULL,
  `company_link` VARCHAR(100) NOT NULL,
  `company_description` VARCHAR(500) NOT NULL,
  `company_i18n_l10n_id` int(3) NOT NULL,
  `company_review_id` BIGINT(20) NOT NULL,
  `company_rating_id` BIGINT(20) NOT NULL,
  `company_last_accessed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

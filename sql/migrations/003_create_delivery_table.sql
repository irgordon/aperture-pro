CREATE TABLE {prefix}ap_delivery (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NOT NULL,
  zip_path TEXT NULL,
  zip_size BIGINT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY project_id (project_id),
  KEY status (status)
);

CREATE TABLE {prefix}ap_proofing (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NOT NULL,
  image_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL,
  note TEXT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY project_image (project_id, image_id),
  KEY status (status)
);

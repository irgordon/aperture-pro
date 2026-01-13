-- Jobs table
CREATE TABLE {prefix}ap_jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(50) NOT NULL,
  status VARCHAR(20) NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 3,
  last_error TEXT NULL,
  payload LONGTEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  KEY project_status (project_id, status),
  KEY created_at (created_at)
);

-- Tokens table
CREATE TABLE {prefix}ap_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NOT NULL,
  token CHAR(64) NOT NULL,
  type VARCHAR(20) NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL,
  UNIQUE KEY token (token),
  KEY project_type (project_id, type),
  KEY expires_at (expires_at)
);

-- Logs table
CREATE TABLE {prefix}ap_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NULL,
  level VARCHAR(20) NOT NULL,
  message TEXT NOT NULL,
  context LONGTEXT NULL,
  created_at DATETIME NOT NULL,
  KEY project_level (project_id, level),
  KEY created_at (created_at)
);

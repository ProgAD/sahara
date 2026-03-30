-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,
    phone       VARCHAR(20)     DEFAULT NULL,
    address     TEXT            DEFAULT NULL,
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    is_active   TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Fundraising Posts Table
-- ============================================================
CREATE TABLE IF NOT EXISTS campaigns (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT             NOT NULL,
    title               VARCHAR(255)    NOT NULL,
    description         TEXT            NOT NULL,
    amount_needed       DECIMAL(12,2)   NOT NULL DEFAULT 0,
    category            VARCHAR(80)     NOT NULL DEFAULT 'general',
    beneficiary_name    VARCHAR(120)    DEFAULT NULL,
    beneficiary_phone   VARCHAR(20)     DEFAULT NULL,
    beneficiary_relation VARCHAR(80)    DEFAULT NULL,
    beneficiary_city    VARCHAR(80)     DEFAULT NULL,
    urgency             ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    status              ENUM('pending','approved','rejected','paused') NOT NULL DEFAULT 'pending',
    admin_note          TEXT            DEFAULT NULL,
    views               INT             NOT NULL DEFAULT 0,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at         DATETIME        DEFAULT NULL,
    paused_at           DATETIME        DEFAULT NULL,
    rejected_at         DATETIME        DEFAULT NULL,
    delete_flag         TINYINT(1)      NOT NULL DEFAULT 0,

    -- FIXED: changed constraint name
    CONSTRAINT fk_campaign_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS donations (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id         INT             NOT NULL,
    user_id             INT             NOT NULL,
    amount              DECIMAL(12,2)   NOT NULL DEFAULT 0,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- ADDED proper foreign keys (you missed these)
    CONSTRAINT fk_donation_campaign 
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,

    CONSTRAINT fk_donation_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
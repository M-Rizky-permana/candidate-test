CREATE DATABASE IF NOT EXISTS clt_feature_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clt_feature_test;

CREATE TABLE IF NOT EXISTS suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clt_layups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT clt_layups_supplier_id_foreign
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON DELETE CASCADE,
    UNIQUE KEY clt_layups_supplier_id_name_unique (supplier_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clt_layers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    layup_id BIGINT UNSIGNED NOT NULL,
    layer_order INT UNSIGNED NOT NULL,
    thickness DECIMAL(10,2) NOT NULL,
    width DECIMAL(10,2) NOT NULL,
    angle DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT clt_layers_layup_id_foreign
        FOREIGN KEY (layup_id) REFERENCES clt_layups(id)
        ON DELETE CASCADE,
    UNIQUE KEY clt_layers_layup_id_layer_order_unique (layup_id, layer_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

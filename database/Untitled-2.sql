USE pos_system;

INSERT INTO users (username, password, full_name, role)
VALUES ('cashier1', '$2y$10$paaQgydEJWReJRkV8bBOou0qY1UCmtQJ34y0523DyqiATu77WAJeu', 'Cashier 1', 'Cashier')
ON DUPLICATE KEY UPDATE
    password = '$2y$10$paaQgydEJWReJRkV8bBOou0qY1UCmtQJ34y0523DyqiATu77WAJeu',
    full_name = 'Cashier 1',
    role = 'Cashier';

SELECT username, role FROM users WHERE username = 'cashier1';
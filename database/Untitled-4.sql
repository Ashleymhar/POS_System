INSERT INTO users (username, password, full_name, role)
VALUES ('admin1', '$2y$10$gBEi7pSkT4Hxm3j2mf7sgOFTki3nD34jOkL5BlnMSd77k7NWoRapi', 'System Administrator', 'Administrator')
ON DUPLICATE KEY UPDATE
    password = '$2y$10$gBEi7pSkT4Hxm3j2mf7sgOFTki3nD34jOkL5BlnMSd77k7NWoRapi',
    full_name = 'System Administrator',
    role = 'Administrator'; 

    SELECT username, role FROM users WHERE username = 'admin1';
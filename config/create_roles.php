   <?php
   require_once 'database.php';

   $database = new Database();
   $conn = $database->connect();

   $accounts = [
       ['manager1', 'manager123', 'Store Manager', 'Manager'],
       ['inventory1', 'inventory123', 'Inventory Staff', 'Inventory Staff']
   ];

   $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)");

   foreach ($accounts as $a) {
       $check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
       $check->bindValue(':username', $a[0]);
       $check->execute();
       if ($check->fetchColumn() > 0) {
           echo "Skipped (already exists): " . $a[0] . "<br>";
           continue;
       }
       $stmt->bindValue(':username', $a[0]);
       $stmt->bindValue(':password', password_hash($a[1], PASSWORD_DEFAULT));
       $stmt->bindValue(':full_name', $a[2]);
       $stmt->bindValue(':role', $a[3]);
       $stmt->execute();
       echo "Created: " . $a[0] . " (" . $a[3] . ")<br>";
   }
   <?php
   function requireRole($allowedRoles) {
       if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
           http_response_code(403);
           die("ACCESS DENIED: You do not have permission to access this page.");
       }
   }
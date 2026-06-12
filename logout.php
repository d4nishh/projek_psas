<?php
session_start();
session_unset(); // Kosongin semua memori session
session_destroy(); // Hancurkan session-nya

// Lempar balik ke halaman login
header("location: login.php");
exit();
?>
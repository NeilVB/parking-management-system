<?php
session_start();
session_unset();
session_destroy();

// Redirect back to public landing page
header("Location: ../index.php");
exit();

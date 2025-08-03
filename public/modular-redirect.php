<?php
// Direct access route for modular enrollment in case the main route has issues

// Get the URL to the modular enrollment page
$modularUrl = url('enrollment/modular');

// Redirect to the modular enrollment page
header('Location: ' . $modularUrl);
exit;
?>

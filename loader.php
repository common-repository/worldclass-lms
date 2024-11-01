<?php

if ( is_admin() ) {
	require_once WCIO_PLUGIN_DIR . '/admin/admin.php';
} else {
	require_once WCIO_PLUGIN_DIR . '/includes/client.php';
}
<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/session_handler.php';

db_session_start($pdo);
session_destroy();
redirect('/admin/login.php');

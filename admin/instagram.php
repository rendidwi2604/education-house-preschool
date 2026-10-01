<?php
require __DIR__ . '/includes/auth.php';

        $inner = <?php
require __DIR__ . '/includes/auth.php';
redirect('/admin/galeri.php#instagramAdmin');
exit;.Groups[1].Value
        # Jika path sudah mulai dengan / biarkan, kalau tidak tambah /admin/
        if ($inner -match '^/') {
            "redirect('$inner')"
        } else {
            "redirect('/admin/$inner')"
        }
    ;
exit;
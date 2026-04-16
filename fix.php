<?php
$files = [
    'resources/views/pages/vendor.blade.php',
    'resources/views/pages/edit-vendor.blade.php',
    'resources/views/pages/edit-profile-tim-operasional.blade.php',
    'resources/views/pages/edit-profile-admin.blade.php',
    'resources/views/pages/edit-paket.blade.php',
    'resources/views/pages/edit-biaya.blade.php',
    'resources/views/pages/create-vendor.blade.php',
    'resources/views/pages/create-profile-tim-operasional.blade.php',
    'resources/views/pages/create-profile-admin.blade.php',
    'resources/views/pages/create-paket.blade.php',
    'resources/views/pages/biaya.blade.php',
    'resources/views/pages/laporan.blade.php',
    'resources/views/pages/profile-tim-operasional.blade.php',
    'resources/views/pages/profile-admin.blade.php',
    'resources/views/dashboard/home.blade.php',
    'resources/views/upload-bukti/form.blade.php',
    'resources/views/upload-bukti/success.blade.php',
    'resources/views/upload-bukti/error.blade.php'
];

foreach ($files as $p) {
    if (file_exists($p)) {
        $c = file_get_contents($p);
        // Replace absolute matched class="container"
        $c = str_replace('class="container"', 'class="container-fluid px-0"', $c);
        // Replace container with margin top e.g. class="container mt-5"
        $c = preg_replace('/class="container\s+([^"]+)"/', 'class="container-fluid px-0 $1"', $c); 
        file_put_contents($p, $c);
    }
}
echo "Done replacing containers\n";

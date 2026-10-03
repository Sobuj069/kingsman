<?php
foreach(glob('database/seeders/*TableSeeder.php') as $file) {
    $c = file_get_contents($file);
    $c = str_replace('->delete();', '// ->delete();', $c);
    $c = str_replace('->insert(', '->insertOrIgnore(', $c);
    file_put_contents($file, $c);
}
echo "Done";

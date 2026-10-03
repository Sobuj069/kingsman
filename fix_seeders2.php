<?php
foreach(glob('database/seeders/*TableSeeder.php') as $file) {
    $c = file_get_contents($file);
    // Let's use regex to properly comment out lines that end with ->delete(); or // ->delete();
    // First, let's revert any DB::table('table')// ->delete(); to // DB::table('table')->delete();
    $c = preg_replace('/DB::table\([^)]+\)\s*\/\/\s*->delete\(\);/', '// $0', $c);
    $c = str_replace('// DB::table', '// DB::table', $c); // just in case
    // Actually, simpler:
    $lines = explode("\n", file_get_contents($file));
    foreach($lines as &$line) {
        if (strpos($line, '->delete();') !== false && strpos(trim($line), '//') !== 0) {
            $line = '// ' . ltrim($line);
        }
        if (strpos($line, '// ->delete();') !== false && strpos($line, 'DB::table') !== false) {
             // It means it's like DB::table('roles')// ->delete();
             // we change it to // DB::table('roles')->delete();
             $line = '// ' . str_replace('// ->delete();', '->delete();', trim($line));
        }
    }
    file_put_contents($file, implode("\n", $lines));
}
echo "Done fixing syntax";

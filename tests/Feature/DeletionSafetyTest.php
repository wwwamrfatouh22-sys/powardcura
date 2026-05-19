<?php

test('application delete paths use the guarded single-record deletion helper', function () {
    $roots = [
        base_path('app'),
        base_path('routes'),
    ];

    $violations = [];

    foreach ($roots as $root) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);

            if ($relativePath === 'app\Support\DeletionGuard.php') {
                continue;
            }

            $contents = file_get_contents($path) ?: '';

            foreach ([
                '/::query\(\)->delete\s*\(/',
                '/->whereIn\s*\([^;]+->delete\s*\(/s',
                '/DB::table\s*\([^;]+->delete\s*\(/s',
                '/->delete\s*\(/',
            ] as $pattern) {
                if (preg_match($pattern, $contents)) {
                    $violations[] = $relativePath;
                    break;
                }
            }
        }
    }

    expect($violations)->toBeEmpty();
});

<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProtectedFile
{
    public const DISK = 'local';
    public const LEGACY_PUBLIC_DISK = 'public';

    public static function storeJobCv(UploadedFile $file): string
    {
        return $file->store('applications/job-cv', self::DISK);
    }

    public static function storeTrainingCv(UploadedFile $file): string
    {
        return $file->store('applications/training-cv', self::DISK);
    }

    public static function storeMedicalResult(UploadedFile $file, string $type): string
    {
        $folder = $type === 'laboratory' ? 'lab' : 'radiology';

        return $file->store('medical-results/' . $folder, self::DISK);
    }

    public static function exists(?string $path): bool
    {
        $resolved = self::resolve($path);

        if ($resolved === null) {
            return false;
        }

        return Storage::disk($resolved['disk'])->exists($resolved['path']);
    }

    public static function download(?string $path, ?string $downloadName = null): StreamedResponse
    {
        $resolved = self::mustResolve($path);

        return Storage::disk($resolved['disk'])->download(
            $resolved['path'],
            $downloadName ?: basename($resolved['path'])
        );
    }

    public static function inline(?string $path, ?string $displayName = null): BinaryFileResponse
    {
        $resolved = self::mustResolve($path);
        $disk = Storage::disk($resolved['disk']);
        $headers = ['Content-Disposition' => 'inline; filename="' . ($displayName ?: basename($resolved['path'])) . '"'];

        if ($mimeType = $disk->mimeType($resolved['path'])) {
            $headers['Content-Type'] = $mimeType;
        }

        return response()->file($disk->path($resolved['path']), $headers);
    }

    public static function normalizedPath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($normalized, 'public/')) {
            return substr($normalized, 7);
        }

        if (str_starts_with($normalized, 'storage/')) {
            return substr($normalized, 8);
        }

        return $normalized;
    }

    private static function resolve(?string $path): ?array
    {
        $normalized = self::normalizedPath($path);

        if ($normalized === null) {
            return null;
        }

        foreach ([self::DISK, self::LEGACY_PUBLIC_DISK] as $disk) {
            if (Storage::disk($disk)->exists($normalized)) {
                return ['disk' => $disk, 'path' => $normalized];
            }
        }

        return null;
    }

    private static function mustResolve(?string $path): array
    {
        $resolved = self::resolve($path);

        abort_unless($resolved !== null, 404, 'Requested file was not found.');

        return $resolved;
    }
}

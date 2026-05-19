<?php

namespace App\Support;

class DepartmentCatalog
{
    public static function requiredPairs(): array
    {
        return [
            ['name_ar' => 'باطنة', 'name_en' => 'Internal Medicine'],
            ['name_ar' => 'جراحة عامة', 'name_en' => 'General Surgery'],
            ['name_ar' => 'جراحة عظام', 'name_en' => 'Orthopedics'],
            ['name_ar' => 'النساء والتوليد', 'name_en' => 'Obstetrics and Gynecology'],
            ['name_ar' => 'القلب والقسطرة', 'name_en' => 'Cardiology & Catheterization'],
            ['name_ar' => 'العناية المركزة', 'name_en' => 'Intensive Care Unit (ICU)'],
            ['name_ar' => 'مسالك بولية', 'name_en' => 'Urology'],
            ['name_ar' => 'كلى صناعي', 'name_en' => 'Dialysis / Nephrology'],
            ['name_ar' => 'أطفال', 'name_en' => 'Pediatrics'],
            ['name_ar' => 'رمد', 'name_en' => 'Ophthalmology'],
            ['name_ar' => 'أنف وأذن', 'name_en' => 'ENT (Ear, Nose, and Throat)'],
            ['name_ar' => 'جراحة مخ وأعصاب', 'name_en' => 'Neurosurgery'],
            ['name_ar' => 'أمراض عصبية ونفسية', 'name_en' => 'Neurology & Psychiatry'],
            ['name_ar' => 'صدرية', 'name_en' => 'Chest / Pulmonology'],
            ['name_ar' => 'جلدية', 'name_en' => 'Dermatology'],
            ['name_ar' => 'أطباء قسم الطوارئ', 'name_en' => 'Emergency Physicians'],
        ];
    }

    public static function legacyAllowed(): array
    {
        return [
            ['name_ar' => 'أشعة', 'name_en' => 'Radiology'],
        ];
    }

    public static function selectable(): array
    {
        return self::requiredPairs();
    }

    public static function requiredEnglish(): array
    {
        return array_column(self::requiredPairs(), 'name_en');
    }

    public static function pairFromInput(?string $englishName = null, ?string $arabicName = null): ?array
    {
        $normalized = self::normalize($englishName, $arabicName);

        if ($normalized === null) {
            return null;
        }

        foreach (array_merge(self::requiredPairs(), self::legacyAllowed()) as $pair) {
            if ($pair['name_en'] === $normalized) {
                return $pair;
            }
        }

        return [
            'name_ar' => trim($arabicName ?: $normalized),
            'name_en' => $normalized,
        ];
    }

    public static function normalize(?string $englishName = null, ?string $fallbackName = null): ?string
    {
        foreach ([$englishName, $fallbackName] as $candidate) {
            if (! is_string($candidate)) {
                continue;
            }

            $trimmed = preg_replace('/\s+/u', ' ', trim($candidate)) ?? '';

            if ($trimmed === '') {
                continue;
            }

            $key = self::key($trimmed);

            if ($key === '') {
                continue;
            }

            if (array_key_exists($key, self::aliases())) {
                return self::aliases()[$key];
            }

            if (! self::containsArabic($trimmed)) {
                return self::cleanEnglish($trimmed);
            }
        }

        return null;
    }

    public static function cleanEnglish(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        if ($value === '') {
            return '';
        }

        $lower = strtolower($value);

        return preg_replace_callback('/\b([a-z])/', static fn ($matches) => strtoupper($matches[1]), $lower) ?? $value;
    }

    public static function key(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['&', '/', '(', ')', '-', '_', ','], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';

        return trim($value);
    }

    public static function containsArabic(?string $value): bool
    {
        return is_string($value) && preg_match('/\p{Arabic}/u', $value) === 1;
    }

    public static function isSupported(string $name): bool
    {
        return in_array($name, array_merge(array_column(self::requiredPairs(), 'name_en'), array_column(self::legacyAllowed(), 'name_en')), true);
    }

    public static function aliases(): array
    {
        static $aliases;

        if ($aliases !== null) {
            return $aliases;
        }

        $map = [
            'Internal Medicine' => ['internal medicine', 'general medicine', 'general internal medicine', 'باطنة', 'الباطنة', 'طب عام'],
            'General Surgery' => ['general surgery', 'surgery', 'جراحة عامة'],
            'Orthopedics' => ['orthopedics', 'orthopaedics', 'orthopedic', 'جراحة عظام', 'عظام'],
            'Obstetrics and Gynecology' => ['obstetrics and gynecology', 'ob gyn', 'obgyn', 'gynecology', 'النساء والتوليد', 'نساء وتوليد'],
            'Cardiology & Catheterization' => ['cardiology', 'cardiology catheterization', 'cardiac catheterization', 'القلب والقسطرة', 'القلب'],
            'Intensive Care Unit (ICU)' => ['intensive care unit', 'icu', 'critical care', 'العناية المركزة'],
            'Urology' => ['urology', 'مسالك بولية'],
            'Dialysis / Nephrology' => ['dialysis', 'nephrology', 'kidney', 'كلى صناعي', 'الغسيل الكلوي', 'غسيل كلوي', 'الكلى'],
            'Pediatrics' => ['pediatrics', 'pediatric', 'children', 'أطفال', 'الاطفال'],
            'Ophthalmology' => ['ophthalmology', 'eye', 'eyes', 'رمد', 'عيون'],
            'ENT (Ear, Nose, and Throat)' => ['ent', 'ear nose and throat', 'otorhinolaryngology', 'أنف وأذن', 'أنف وأذن وحنجرة', 'انف واذن وحنجرة'],
            'Neurosurgery' => ['neurosurgery', 'brain surgery', 'جراحة مخ وأعصاب', 'جراحة أعصاب', 'جراحة اعصاب'],
            'Neurology & Psychiatry' => ['neurology', 'psychiatry', 'neurology psychiatry', 'أمراض عصبية ونفسية', 'أعصاب', 'اعصاب'],
            'Chest / Pulmonology' => ['chest', 'pulmonology', 'pulmonary', 'صدرية', 'صدر'],
            'Dermatology' => ['dermatology', 'جلدية'],
            'Emergency Physicians' => ['emergency physicians', 'emergency medicine', 'emergency', 'er', 'أطباء قسم الطوارئ', 'طوارئ', 'الطوارئ'],
            'Radiology' => ['radiology', 'أشعة', 'اشعة'],
        ];

        $aliases = [];

        foreach ($map as $canonical => $variants) {
            $aliases[self::key($canonical)] = $canonical;

            foreach ($variants as $variant) {
                $aliases[self::key($variant)] = $canonical;
            }
        }

        return $aliases;
    }
}
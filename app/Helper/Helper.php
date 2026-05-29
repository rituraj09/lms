<?php

namespace App\Helper;

class Helper
{

}
if (!function_exists('getActiveLanguages')) {
    /**
     * Returns active languages as ['code' => 'Label']
     * e.g. ['en' => 'English', 'as' => 'Assamese', 'bn' => 'Bengali']
     */
    function getActiveLanguages(): array
    {
        return cache()->remember('active_languages', 3600, function () {
            return \App\Models\Language::where('is_active', true)
                ->pluck('name', 'code')
                ->toArray();
        });
    }
}

<?php

namespace App\Services;

class Timezones {
    public static function get()
    {
        return [
            'America/New_York' => 'Eastern Time',
            'America/Chicago' => 'Central Time',
            'America/Denver' => 'Mountain Time',
            'America/Phoenix' => 'Mountain Time (no DST)',
            'America/Los_Angeles' => 'Pacific Time',
            'America/Anchorage' => 'Alaska Time',
            'America/Adak' => 'Hawaii-Aleutian',
            'Pacific/Honolulu' => 'Hawaii-Aleutian Time (no DST)',
        ];
    }
}

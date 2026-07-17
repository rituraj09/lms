<?php
// config/settings.php

return [
    'notification_defaults' => [
        'course_updates'      => true,
        'exam_reminders'      => true,
        'achievement_alerts'  => true,
        'leaderboard_changes' => false,
        'weekly_digest'       => true,
        'marketing_emails'    => false,
    ],

    'privacy_defaults' => [
        'profile_visibility'  => 'public', // public | private
        'show_on_leaderboard' => true,
        'two_factor_enabled'  => false,
    ],

    'avatar_disk' => 'public',
    'avatar_path' => 'avatars',
];

<?php
// config/gamification.php

return [
    // % of total_marks required to count as "passed"
    'pass_ratio' => 0.40,

    'points' => [
        'exam_pass_bonus' => 50, // flat bonus on top of raw score points
    ],

    'level_threshold' => 500, // points per level

    'cache_ttl_seconds' => 300, // 5 min cache for leaderboard listing
];

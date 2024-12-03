<?php

return [
    'max_attempts' => 5,
    'delay_between_attempts' => 10,
    'password_expiry' => 90,
    'password_complexity' => array (
  'min_length' => 8,
  'uppercase' => false,
  'lowercase' => false,
  'numbers' => false,
  'special_chars' => false,
),
    'password_history' => 5,
];

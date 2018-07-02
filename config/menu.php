<?php

return [
'menustructure' => [
                        0 => [
                                'text' => 'Login',
                                'path' => 'login',
                                'parentid' => 0,
                                'cssclass' => 'menulevel1',
                                'role' => 10000,
                                'level' => 1,
                                'show' => 'show',
                                'childrenmap' => []
                                ],
                        1000 => [
                                'text' => 'Customers',
                                'path' => 'customers?menupoint=1010',
                                'parentid' => 1000,
                                'cssclass' => 'menulevel1',
                                'role' => 1,
                                'level' => 1,
                                'show' => 'show',
                                'childrenmap' => [1010, 1020, 1030]
                        ],
                        1010 => [
                                'text' => 'Customer list',
                                'path' => 'customers',
                                'parentid' => 1000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'hide',
                                'childrenmap' => []
                        ],
                        1020 => [
                                'text' => 'Customer Edit',
                                'path' => 'customer/edit',
                                'parentid' => 1000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'select',
                                'childrenmap' => []
                        ],
                        1030 => [
                                'text' => 'Customer Details',
                                'path' => 'customer/show',
                                'parentid' => 1000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'select',
                                'childrenmap' => []
                        ],
                        2100 => [
                                'text' => 'House',
                                'path' => 'houses?menupoint=2110',
                                'parentid' => 2100,
                                'cssclass' => 'menulevel1',
                                'role' => 1000,
                                'level' => 1,
                                'show' => 'show',
                                'childrenmap' => [2110, 2120]
                        ],
                        2110 => [
                                'text' => 'House list',
                                'path' => 'houses',
                                'parentid' => 2100,
                                'cssclass' => 'menulevel2',
                                'role' => 1000,
                                'level' => 2,
                                'show' => 'hide',
                                'childrenmap' => []
                        ],
                        2120 => [
                                'text' => 'House Edit',
                                'path' => 'houses/edit/1',
                                'parentid' => 2100,
                                'cssclass' => 'menulevel2',
                                'role' => 1000,
                                'level' => 2,
                                'show' => 'select',
                                'childrenmap' => []
                        ],
                        3000 => [
                                'text' => 'User Admin.',
                                'path' => 'manage/index?menupoint=3010',
                                'parentid' => 3000,
                                'cssclass' => 'menulevel1',
                                'role' => 1,
                                'level' => 1,
                                'show' => 'show',
                                'childrenmap' => [3010, 3020, 3030]
                        ],
                        3010 => [
                                'text' => 'User list',
                                'path' => 'manage/index',
                                'parentid' => 3000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'hide',
                                'childrenmap' => []
                        ],
                        3020 => [
                                'text' => 'Same..',
                                'path' => 'manage/index',
                                'parentid' => 3000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'select',
                                'childrenmap' => []
                        ],
                        3030 => [
                                'text' => 'Changeme..',
                                'path' => 'manage/index',
                                'parentid' => 3000,
                                'cssclass' => 'menulevel2',
                                'role' => 1,
                                'level' => 2,
                                'show' => 'select',
                                'childrenmap' => []
                        ]
             ]
];
<?php
//Menu role 10000: Only show when not logged in
//menu role 10001: Only show when logged in.
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

    9000 => [
        'text' => 'My Account',
        'path' => 'customer/registration?menupoint=9010',
        'parentid' => 9000,
        'cssclass' => 'menulevel1',
        'role' => 10001,  //Only show to loggedin
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [9010,9020,9030,9040,9050,9060]
    ],
    9010 => [
        'text' => 'Registration',
        'path' => 'customer/registration',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9020 => [
        'text' => 'Bookings',
        'path' => 'customer/listbookings',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9030 => [
        'text' => 'Account',
        'path' => 'customer/listaccountposts',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9040 => [
        'text' => 'Mails',
        'path' => 'customer/listmails',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9050 => [
        'text' => 'Itenery',
        'path' => 'customer/edittime',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9060 => [
        'text' => 'Logout',
        'path' => 'logout',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],


    10000 => [
        'text' => 'House',
        'path' => 'home/showinfo/description?menupoint=10010',
        'parentid' => 10000,
        'cssclass' => 'menulevel1',
        'role' => 1000,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [10010,10020,10030,10040,10050,10060,10070,10080,10090,10100,10110,10120,10130,10140]
    ],
    10010 => [
        'text' => 'Description',
        'path' => 'home/showinfo/description',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10020 => [
        'text' => 'Bookings',
        'path' => 'home/checkbookings',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10030 => [
        'text' => 'Show route',
        'path' => 'home/showinfo/route',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10040 => [
        'text' => 'Conditions',
        'path' => 'home/showinfo/conditions',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10050 => [
        'text' => 'Plan',
        'path' => 'home/showinfo/plan',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10060 => [
        'text' => 'Gallery',
        'path' => 'home/showinfo/gallery',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10070 => [
        'text' => 'Find house',
        'path' => 'home/listhouses',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10080 => [
        'text' => 'Testimonials',
        'path' => 'home/listtestimonials',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10090 => [
        'text' => 'Google map',
        'path' => 'home/showmap',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10100 => [
        'text' => 'Shopping',
        'path' => 'home/showinfo/shopping',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10110 => [
        'text' => 'Car rental',
        'path' => 'home/showinfo/carrental',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10120 => [
        'text' => 'Nature',
        'path' => 'home/showinfo/nature',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10130 => [
        'text' => 'Weather',
        'path' => 'home/showinfo/weather',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    10140 => [
        'text' => 'Sports',
        'path' => 'home/showinfo/sports',
        'parentid' => 10000,
        'cssclass' => 'menulevel2',
        'role' => 1000,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],


    11000 => [
        'text' => 'Cust. Adm.',
        'path' => 'customer/index?menupoint=11010',
        'parentid' => 11000,
        'cssclass' => 'menulevel1',
        'role' => 1,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [11010, 11020, 11030]
    ],
    11010 => [
        'text' => 'Customer list',
        'path' => 'customer/index',
        'parentid' => 11000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    11020 => [
        'text' => 'Customer Edit',
        'path' => 'customer/edit',
        'parentid' => 11000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],
    11030 => [
        'text' => 'Customer Details',
        'path' => 'customer/show',
        'parentid' => 11000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],
    12000 => [
        'text' => 'House Adm.',
        'path' => 'house/index?menupoint=12110',
        'parentid' => 12000,
        'cssclass' => 'menulevel1',
        'role' => 100,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [12010, 12020, 12030, 12040, 12050]
    ],
    12010 => [
        'text' => 'Create periods',
        'path' => 'house/createperiods',
        'parentid' => 12100,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12020 => [
        'text' => 'List periods',
        'path' => 'house/listperiods',
        'parentid' => 12000,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12030 => [
        'text' => 'List houses - administrator',
        'path' => 'house/listhouses',
        'parentid' => 12100,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12040 => [
        'text' => 'Statistics',
        'path' => 'customer/statistics',
        'parentid' => 12100,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12050 => [
        'text' => 'Annual overview',
        'path' => 'contract/annualcontractoverview',
        'parentid' => 12100,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    13000 => [
        'text' => 'User Admin.',
        'path' => 'manage/index?menupoint=3010',
        'parentid' => 13000,
        'cssclass' => 'menulevel1',
        'role' => 1,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [13010, 13020, 13030]
    ],
    13010 => [
        'text' => 'User list',
        'path' => 'manage/index',
        'parentid' => 13000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    13020 => [
        'text' => 'Same..',
        'path' => 'manage/index',
        'parentid' => 13000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],
    13030 => [
        'text' => 'Changeme..',
        'path' => 'manage/index',
        'parentid' => 13000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],

    14000 => [
        'text' => 'Setup',
        'path' => 'manage/index?menupoint=3010',
        'parentid' => 14000,
        'cssclass' => 'menulevel1',
        'role' => 1,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [14010, 14020, 14030]
    ],
    14010 => [
        'text' => 'User list',
        'path' => 'manage/index',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14020 => [
        'text' => 'Same..',
        'path' => 'manage/index',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],
    14030 => [
        'text' => 'Changeme..',
        'path' => 'manage/index',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'select',
        'childrenmap' => []
    ],
]
];
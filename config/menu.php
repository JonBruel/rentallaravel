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
        'path' => 'myaccount/registration?menupoint=9010',
        'parentid' => 9000,
        'cssclass' => 'menulevel1',
        'role' => 10001,  //Only show to loggedin
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [9010,9020,9030,9040,9050,9060]
    ],
    9010 => [
        'text' => 'Registration',
        'path' => 'myaccount/registration',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9020 => [
        'text' => 'Bookings',
        'path' => 'myaccount/listbookings',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9030 => [
        'text' => 'Account',
        'path' => 'myaccount/listaccountposts',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9040 => [
        'text' => 'Mails',
        'path' => 'myaccount/listmails',
        'parentid' => 9000,
        'cssclass' => 'menulevel2',
        'role' => 10001,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    9050 => [
        'text' => 'Itenery',
        'path' => 'myaccount/edittime',
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
        'text' => 'Rental overview',
        'path' => 'contract/listcontractoverviewforowners',
        'parentid' => 11000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    11030 => [
        'text' => 'Arrivals',
        'path' => 'contract/listcontractoverview',
        'parentid' => 11000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
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
        'parentid' => 12000,
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
        'parentid' => 12000,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12040 => [
        'text' => 'Statistics',
        'path' => 'customer/statistics',
        'parentid' => 12000,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    12050 => [
        'text' => 'Annual overview',
        'path' => 'contract/annualcontractoverview',
        'parentid' => 12000,
        'cssclass' => 'menulevel2',
        'role' => 100,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14000 => [
        'text' => 'Setup',
        'path' => 'setup/listowners?menupoint=14010',
        'parentid' => 14000,
        'cssclass' => 'menulevel1',
        'role' => 10,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [14010, 14020, 14030, 14040, 14050, 14060, 14070, 14080]
    ],
    14010 => [
        'text' => 'List personnel',
        'path' => 'setup/listowners',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14020 => [
        'text' => 'Make batch',
        'path' => 'setup/makebatch1',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => [14021]
    ],
    14021 => [
        'text' => 'Batch details',
        'path' => 'setup/makebatch2',
        'parentid' => 14020,
        'cssclass' => 'menulevel3',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14030 => [
        'text' => 'List batch tasks',
        'path' => 'setup/listbatchtasks',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 10,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14040 => [
        'text' => 'Standard E-mails',
        'path' => 'setup/liststandardemails',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 10,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14050 => [
        'text' => 'Bounties',
        'path' => 'setup/listbounties',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 10,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14060 => [
        'text' => 'Batch status',
        'path' => 'setup/listqueue',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 10,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14070 => [
        'text' => 'First setup',
        'path' => 'setup/firstsetup',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 10,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    14080 => [
        'text' => 'Gallery',
        'path' => 'setup/editcaptions',
        'parentid' => 14000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    15000 => [
        'text' => 'System',
        'path' => 'setup/listerrorlogs?menupoint=15010',
        'parentid' => 15000,
        'cssclass' => 'menulevel1',
        'role' => 1,
        'level' => 1,
        'show' => 'show',
        'childrenmap' => [15010, 15020, 15030, 15040]
    ],
    15010 => [
        'text' => 'User list',
        'path' => 'setup/listerrorlogs',
        'parentid' => 15000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    15020 => [
        'text' => 'Configs',
        'path' => 'setup/listconfigs',
        'parentid' => 15000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    15030 => [
        'text' => 'Batch wizard',
        'path' => 'wizards/workflow',
        'parentid' => 15000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
    15040 => [
        'text' => 'PHP Info',
        'path' => 'setup/showphpinfo',
        'parentid' => 15000,
        'cssclass' => 'menulevel2',
        'role' => 1,
        'level' => 2,
        'show' => 'hide',
        'childrenmap' => []
    ],
]
];
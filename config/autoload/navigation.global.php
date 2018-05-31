<?php

return [
    'dot_navigation' => [
        //enable menu item active if any child is active
        'active_recursion' => true,

        'containers' => [
            'account_side_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Personal Information',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'account']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change password',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'change-password']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change email',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'change-email']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Delete account',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'remove-account']
                                ],
                                'red-button' => true,
                            ],
                        ],
                    ],
                ],
            ],

            'main_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Meals',
                                'uri' => '#',
                                'icon' => '',
                            ],
                            'pages' => [
                                [
                                    'options' => [
                                        'label' => 'View Meals',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'view'
                                            ],
                                        ],
                                        'icon' => 'fa fa-home'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'separator',
                                        'type' => 'separator',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Quick add breakfast',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'add-meal',
                                                'date' => 'today',
                                                'type' => 'breakfast',
                                            ]
                                        ],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Quick add lunch',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'add-meal',
                                                'date' => 'today',
                                                'type' => 'lunch',
                                            ]
                                        ],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Quick add dinner',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'add-meal',
                                                'date' => 'today',
                                                'type' => 'dinner',
                                            ]
                                        ],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Quick add snacks',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'add-meal',
                                                'date' => 'today',
                                                'type' => 'snacks',
                                            ]
                                        ],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'separator',
                                        'type' => 'separator',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Today\'s Statistics',
                                        'route' => [
                                            'route_name' => 'meals',
                                            'route_params' => [
                                                'action' => 'stats'
                                            ],
                                        ],
                                        'icon' => 'fa fa-home'
                                    ]
                                ],
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Search products',
                                'route' => [
                                    'route_name' => 'product',
                                    'route_params' => [
                                        'action' => 'search',
                                    ]
                                ],
                                'icon' => '',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Submit new product',
                                'route' => [
                                    'route_name' => 'product',
                                    'route_params' => [
                                        'action' => 'submit',
                                    ]
                                ],
                                'icon' => '',
                            ]
                        ],
                    ],
                ],
            ],

            'account_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Welcome, ',
                                'id' => 'account',
                                'uri' => '#',
                                'icon' => '',
                                'permission' => 'authenticated'
                            ],
                            'attributes' => [
                                'class' => 'navbar-colored-item user-menu-icon',
                            ],
                            'pages' => [
                                [
                                    'options' => [
                                        'label' => 'Settings',
                                        'route' => [
                                            'route_name' => 'user',
                                            'route_params' => ['action' => 'account']
                                        ],
                                        'icon' => '',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Sign Out',
                                        'route' => [
                                            'route_name' => 'logout',
                                        ],
                                        'icon' => ''
                                    ]
                                ]
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Login',
                                'route' => [
                                    'route_name' => 'login',
                                ],
                                'icon' => '',
                                'permission' => 'unauthenticated'
                            ],
                            'attributes' => [
                                'class' => 'navbar-colored-item user-menu-icon',
                            ]
                        ],
                    ],
                ],
            ],
        ],

        //register custom providers here
        'provider_manager' => []
    ]
];

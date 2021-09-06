<?php

return [
    //menus
    [
        'name' => 'menus',
        'value' => json_encode([
            [
                'position' => 'dashboard',
                'name' => 'Dashboard',
                'items' => [
                    [
                        'type' => 'route',
                        'order' => 1,
                        'condition' => 'admin',
                        'position' => 0,
                        'label' => 'Admin Area',
                        'action' => 'admin',
                    ],
                ],
            ],
            [
                'name' => 'footer',
                'position' => 'footer',
                'items' => [
                    [
                        'type' => 'route',
                        'position' => 1,
                        'label' => 'Developers',
                        'action' => '/api-docs',
                        'condition' => 'auth',
                    ],
                    [
                        'type' => 'route',
                        'position' => 2,
                        'label' => 'Privacy Policy',
                        'action' => '/pages/1/privacy-policy',
                    ],
                    [
                        'type' => 'route',
                        'position' => 3,
                        'label' => 'Terms of Service',
                        'action' => '/pages/2/terms-of-service',
                    ],
                    [
                        'type' => 'route',
                        'position' => 4,
                        'label' => 'Contact Us',
                        'action' => '/contact',
                    ],
                ],
            ],
            [
                'name' => 'Footer Social',
                'position' => 'footer-secondary',
                'items' => [
                    [
                        'type' => 'link',
                        'position' => 1,
                        'icon' => 'facebook-square',
                        'action' => 'https://facebook.com',
                    ],
                    [
                        'type' => 'link',
                        'position' => 2,
                        'icon' => 'twitter',
                        'action' => 'https://twitter.com',
                    ],
                    [
                        'type' => 'link',
                        'position' => 3,
                        'icon' => 'instagram',
                        'action' => 'https://instagram.com',
                    ],
                    [
                        'type' => 'link',
                        'position' => 4,
                        'icon' => 'youtube',
                        'action' => 'https://youtube.com',
                    ],
                ],
            ],
        ]),
    ],

    //branding
    ['name' => 'branding.site_name', 'value' => 'Architect'],

    //builder
    ['name' => 'builder.enable_subdomains', 'value' => false],
    ['name' => 'builder.enable_custom_domains', 'value' => true],
    [
        'name' => 'builder.googgle_fonts_api_key',
        'value' => 'AIzaSyDhc_8NKxXjtv69htFcUPe6A7oGSQ4om2o',
    ],
    [
        'name' => 'builder.template_categories',
        'value' => json_encode(['Landing Page', 'Blog', 'Portfolio']),
    ],
    ['name' => 'publish.allow_credential_change', 'value' => true],

    //landing
    [
        'name' => 'homepage.appearance',
        'value' => json_encode([
            'headerTitle' => 'Create a Website You’re Proud Of',
            'headerSubtitle' =>
                'Discover the platform that gives you the freedom to create, design, manage and develop your web presence exactly the way you want.',
            'headerImage' => 'client/assets/images/landing/landing-bg.svg',
            'headerImageOpacity' => 1,
            'headerOverlayColor1' => null,
            'headerOverlayColor2' => null,
            'footerTitle' => 'Build your website today',
            'footerSubtitle' => null,
            'footerImage' => 'client/assets/images/landing/landing-bg.svg',
            'actions' => [
                'cta1' => 'Start Now',
                'cta2' => 'Learn More',
            ],
            'primaryFeatures' => [
                [
                    'title' => 'Custom domains',
                    'subtitle' =>
                        'Attach your own custom domain or use on of the free architect subdomains.',
                    'image' => 'custom-domain.svg',
                ],
                [
                    'title' => 'Eye-catching website designs',
                    'subtitle' =>
                        'Our easy-to-use builder helps you create and launch a beautiful website\u2014fast.',
                    'image' => 'website-builder.svg',
                ],
                [
                    'title' => 'Grow your business with powerful tools',
                    'subtitle' =>
                        'Design and build your own high-quality websites. Whatever the type of site\u2014you can do it with Architect website builder.',
                    'image' => 'pen-tool.svg',
                ],
            ],
            'secondaryFeatures' => [
                [
                    'title' => 'Look like an expert right from the start.',
                    'subtitle' => 'AWARD-WINNING WEBSITE DESIGN',
                    'image' =>
                        'client/assets/images/landing/landing-feature-1.jpg',
                    'description' =>
                        'Our award-winning templates are the most beautiful way to present your ideas online. Stand out with a professional website, portfolio, or online store.',
                ],
                [
                    'title' => 'The Freedom to Create the Websites You Want',
                    'subtitle' => 'Complete Freedom',
                    'image' =>
                        'client/assets/images/landing/landing-feature-2.jpg',
                    'description' =>
                        'Start from scratch or choose from large catalog of templates to make your own website. With the world\u2019s most innovative drag and drop website builder, you can customize or change anything. With the Architect, you can create your own professional website that looks stunning.',
                ],
            ],
        ]),
    ],
];

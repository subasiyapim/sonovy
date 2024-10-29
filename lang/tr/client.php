<?php

return [
    'general' => [
        'optional' => '(Opsiyonel)',
        'read_more' => 'Devamını oku',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
    ],
    'notification' => [
        'deleted_success' => ':model Başarıyla silindi.',
    ],
    'home' => [
        'header_title' => 'Beautiful analytics to grow smarter',
        'header_description' => 'Powerful, self-serve product and growth analytics to help you convert, engage, and
        retain more users. Trusted by over 4,000 startups.',
        'header_button' => 'Ücretsiz Dene',
        'partners_count' => 'Join 4,000+ companies already growing',
        'features' => 'Feature',
        'features_title' => 'Analytics that feels like it’s from the future',
        'features_description' => 'Powerful, self-serve product and growth analytics to help you convert, engage, and
        retain more users. Trusted by over 4,000 startups.',
        'pricing' => 'Pricing',
        'pricing_title' => 'Plans that fit your scale',
        'pricing_description' => 'Simple, transparent pricing that grows with you. Try any plan free for 30 days.',
        'contact_us_title' => 'Aklına takılan başka bir şey varsa bize yazabilirsin.',
        'contact_us_description' => 'Ekiplerimiz size yardımcı olmaktan mutluluk duyacaktır.',
    ],
    'contact_us' => [
        'name' => 'Ad-Soyad',
        'email' => 'Email',
        'phone' => 'Telefon numarası',
        'message' => 'Mesaj'
    ],
    'support_center' => [
        'main_title' => 'Destek merkezi',
        'search_subtitle' => 'Aklına takılan sorular var mı? Senin için yardıma hazırız.',
        'search_placeholder' => 'Sana nasıl yardımcı olabiliriz?',
        'main_button_articles' => 'Başlarken',
        'main_button_faq' => 'SSS',
        'main_button_videos' => 'Video Galerisi',
        'main_button_contact' => 'Bize Ulaşın',
        'main_page_latest_articles_title' => 'En sık okunan yazılar',
        'main_page_latest_articles_subtitle' => 'Platform içerikleri, sektör haberleri ve daha fazlası için ekibimizin yazılarına göz atın',
        'faq_main_title' => 'Sıkça sorulan sorular',
        'faq_search_subtitle' => 'Aklına takılan sorular var mı? Senin için yardıma hazırız.',
        'faq_search_placeholder' => 'Sana nasıl yardımcı olabiliriz?',
        'video_main_title' => 'Video Eğitimler',
        'articles' => 'Makaleler',
        'videos' => 'Videolar',
    ],
    'login' => [
        'title' => 'Tekrar Hoşgeldiniz',
        'subtitle' => 'Yayınlarınızı yönetmek için giriş yapın',
        'login_btn' => 'Giriş yap',
        'register_btn' => 'Kayıt ol',
        'forgot_password' => 'Şifremi unuttum',
        'dont_have_account' => 'Hesabınız yok mu?',
        'register' => 'Hesap Oluştur',
        'fields' => [
            'email' => 'Email',
            'email_placeholder' => 'Email adresinizi girin',
            'password' => 'Şifre',
            'password_placeholder' => '**********',
        ]
    ],
    'register' => [
        'title' => 'Ücretsiz Hesap Oluştur',
        'subtitle' => 'Hızlıca yayın oluşturmak için ücretsiz hesabınızı oluşturun',
        'register_btn' => 'Kayıt ol',
        'login_btn' => 'Giriş yap',
        'already_have_account' => 'Zaten bir hesabınız var mı?',
        'login' => 'Giriş yap',
        'fields' => [
            'name' => 'Ad',
            'name_placeholder' => 'Adınızı girin',
            'surname' => 'Soyad',
            'surname_placeholder' => 'Soyadınızı girin',
            'email' => 'Email',
            'email_placeholder' => 'Email adresinizi girin',
            'password' => 'Şifre',
            'password_placeholder' => '**********',
            'phone' => 'Telefon',
            'phone_placeholder' => '212 555 55 55',
        ]
    ],
    'verify_email' => [
        'title' => 'Email Adresinizi Doğrulayın',
        'description' => ':email eposta adresinize 6 haneli doğrulama kodu gönderdik. Lütfen kodu giriniz.',
        'resend_verification_email' => 'Doğrulama e-postasını tekrar gönder',
        'submit' => 'Kodu Doğrula',
        'logout' => 'Çıkış yap',
        'check_email' => 'Kayıt sırasında verdiğiniz e-posta adresine yeni bir doğrulama bağlantısı gönderilmiştir.',
    ],
    'verify_phone' => [
        'title' => 'GSM No Doğrulama',
        'description' => ':phone nolu telefonunuza 6 haneli doğrulama kodu gönderdik. Lütfen kodu girniz',
        'submit' => 'Kodu Doğrula',
        'logout' => 'Çıkış yap',

    ],
    'forgot_password' => [
        'title' => 'Şifremi Sıfırla',
        'subtitle' => 'Şifrenizi sıfırlamak için e-posta adresinize 6 haneli sıfırlama kodu göndereceğiz.',
        'send_reset_link' => 'Kod gönder',
        'send_reset_link_loading' => 'Kod doğrulanıyor...',
        'back_btn' => 'Geri dön',
        'email_sent' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.',
        'email' => 'Email adresiniz',
        'email_placeholder' => 'hello@sonovy.com',
    ],
    'forgot_password_pin' => [
        'title' => 'Sıfırlama Kodunu Giriniz',
        'subtitle' => ':email mail adresinize 6 haneli doğrulama kodu gönderdik. Lütfen kodu giriniz.',
        'time' => ':time saniye içinde ',
        'resend_code' => 'Kodu tekrar gönder',
        'verify_code' => 'Kodu doğrula',
        'back_btn' => 'Geri dön',
        'validating_code' => 'Kod doğrulanıyor...',
        'code' => 'Kod',
        'code_incorrect' => 'Girdiğiniz kod yanlış. Lütfen tekrar deneyin.',
        'code_expired' => 'Kodun süresi doldu. Lütfen tekrar deneyin.',
    ]

];

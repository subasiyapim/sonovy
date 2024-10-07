<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    protected static array $mailTemplates = [
        [
            'code' => 'welcome',
            'name' => 'Welcome',
            'en' => [
                'subject' => 'Welcome to our website',
                'body' => 'Welcome to our website, {{name}}!',
            ],
            'tr' => [
                'subject' => 'Websitesine hoş geldiniz',
                'body' => 'Websitesine hoş geldiniz, {{name}}!',
            ],
        ],
        [
            'code' => 'reset-password',
            'name' => 'Reset Password',
            'en' => [
                'subject' => 'Reset your password',
                'body' => 'Click the link below to reset your password: {{link}}',
            ],
            'tr' => [
                'subject' => 'Şifrenizi sıfırlayın',
                'body' => 'Şifrenizi sıfırlamak için aşağıdaki linke tıklayın: {{link}}',
            ],
        ],
        [
            'code' => 'order-confirmation',
            'name' => 'Order Confirmation',
            'en' => [
                'subject' => 'Order Confirmation',
                'body' => 'Your order has been confirmed. Order number: {{order_number}}',
            ],
            'tr' => [
                'subject' => 'Sipariş Onayı',
                'body' => 'Siparişiniz onaylandı. Sipariş numarası: {{order_number}}',
            ],
        ],
        [
            'code' => 'email-verification',
            'name' => 'Email Verification',
            'en' => [
                'subject' => 'Email Verification',
                'body' => 'Click the link below to verify your email address: {{link}}',
            ],
            'tr' => [
                'subject' => 'E-posta Doğrulama',
                'body' => 'E-posta adresinizi doğrulamak için aşağıdaki linke tıklayın: {{link}}',
            ],
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$mailTemplates as $mailTemplate) {

            MailTemplate::create($mailTemplate);

        }
    }
}

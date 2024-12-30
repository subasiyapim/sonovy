<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Translations\PermissionTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private static array $permissions = [
        //Permissions
        [
            'code' => 'permission_list',
            'tr' => ['name' => 'İzin Listele',],
            'en' => ['name' => 'Permission List',],
        ],
        [
            'code' => 'permission_create',
            'tr' => ['name' => 'İzin Ekle',],
            'en' => ['name' => 'Permission Create',],
        ],
        [
            'code' => 'permission_edit',
            'tr' => ['name' => 'İzin Düzenle',],
            'en' => ['name' => 'Permission Edit',],
        ],
        [
            'code' => 'permission_delete',
            'tr' => ['name' => 'İzin Sil',],
            'en' => ['name' => 'Permission Delete',],
        ],
        [
            'code' => 'permission_show',
            'tr' => ['name' => 'İzin Görüntüle',],
            'en' => ['name' => 'Permission Show',],
        ],
//Roles
        [
            'code' => 'role_list',
            'tr' => ['name' => 'Rol Listele',],
            'en' => ['name' => 'Role List',],
        ],
        [
            'code' => 'role_create',
            'tr' => ['name' => 'Rol Ekle',],
            'en' => ['name' => 'Role Create',],
        ],
        [
            'code' => 'role_edit',
            'tr' => ['name' => 'Rol Düzenle',],
            'en' => ['name' => 'Role Edit',],
        ],
        [
            'code' => 'role_delete',
            'tr' => ['name' => 'Rol Sil',],
            'en' => ['name' => 'Role Delete',],
        ],
        [
            'code' => 'role_show',
            'tr' => ['name' => 'Rol Görüntüle',],
            'en' => ['name' => 'Role Show',],
        ],
//Users
        [
            'code' => 'user_list',
            'tr' => ['name' => 'Kullanıcı Listele',],
            'en' => ['name' => 'User List',],
        ],
        [
            'code' => 'user_create',
            'tr' => ['name' => 'Kullanıcı Ekle',],
            'en' => ['name' => 'User Create',],
        ],
        [
            'code' => 'user_edit',
            'tr' => ['name' => 'Kullanıcı Düzenle',],
            'en' => ['name' => 'User Edit',],
        ],
        [
            'code' => 'user_delete',
            'tr' => ['name' => 'Kullanıcı Sil',],
            'en' => ['name' => 'User Delete',],
        ],
        [
            'code' => 'user_show',
            'tr' => ['name' => 'Kullanıcı Görüntüle',],
            'en' => ['name' => 'User Show',],
        ],
        [
            'code' => 'user_competency',
            'tr' => ['name' => 'Kullanıcı Yeterlilik',],
            'en' => ['name' => 'User Competency',],
        ],
        [
            'code' => 'user_switch',
            'tr' => ['name' => 'Kullanıcının gözünden gör',],
            'en' => ['name' => 'See through the user\'s eyes',],
        ],
//Artists
        [
            'code' => 'artist_list',
            'tr' => ['name' => 'Sanatçı Listele',],
            'en' => ['name' => 'Artist List',],
        ],
        [
            'code' => 'artist_create',
            'tr' => ['name' => 'Sanatçı Ekle',],
            'en' => ['name' => 'Artist Create',],
        ],
        [
            'code' => 'artist_edit',
            'tr' => ['name' => 'Sanatçı Düzenle',],
            'en' => ['name' => 'Artist Edit',],
        ],
        [
            'code' => 'artist_delete',
            'tr' => ['name' => 'Sanatçı Sil',],
            'en' => ['name' => 'Artist Delete',],
        ],
        [
            'code' => 'artist_show',
            'tr' => ['name' => 'Sanatçı Görüntüle',],
            'en' => ['name' => 'Artist Show',],
        ],
//Artist Branches
        [
            'code' => 'artist_branch_list',
            'tr' => ['name' => 'Sanatçı Şubesi Listele',],
            'en' => ['name' => 'Artist Branch List',],
        ],
        [
            'code' => 'artist_branch_create',
            'tr' => ['name' => 'Sanatçı Şubesi Ekle',],
            'en' => ['name' => 'Artist Branch Create',],
        ],
        [
            'code' => 'artist_branch_edit',
            'tr' => ['name' => 'Sanatçı Şubesi Düzenle',],
            'en' => ['name' => 'Artist Branch Edit',],
        ],
        [
            'code' => 'artist_branch_delete',
            'tr' => ['name' => 'Sanatçı Şubesi Sil',],
            'en' => ['name' => 'Artist Branch Delete',],
        ],
        [
            'code' => 'artist_branch_show',
            'tr' => ['name' => 'Sanatçı Şubesi Görüntüle',],
            'en' => ['name' => 'Artist Branch Show',],
        ],
//Label
        [
            'code' => 'label_list',
            'tr' => ['name' => 'Etiket Listele',],
            'en' => ['name' => 'Label List',],
        ],
        [
            'code' => 'label_create',
            'tr' => ['name' => 'Etiket Ekle',],
            'en' => ['name' => 'Label Create',],
        ],
        [
            'code' => 'label_edit',
            'tr' => ['name' => 'Etiket Düzenle',],
            'en' => ['name' => 'Label Edit',],
        ],
        [
            'code' => 'label_delete',
            'tr' => ['name' => 'Etiket Sil',],
            'en' => ['name' => 'Label Delete',],
        ],
        [
            'code' => 'label_show',
            'tr' => ['name' => 'Etiket Görüntüle',],
            'en' => ['name' => 'Label Show',],
        ],
//Product
        [
            'code' => 'product_list',
            'tr' => ['name' => 'Yayın Listele',],
            'en' => ['name' => 'Product List',],
        ],
        [
            'code' => 'product_create',
            'tr' => ['name' => 'Yayın Ekle',],
            'en' => ['name' => 'Product Create',],
        ],
        [
            'code' => 'product_edit',
            'tr' => ['name' => 'Yayın Düzenle',],
            'en' => ['name' => 'Product Edit',],
        ],
        [
            'code' => 'product_delete',
            'tr' => ['name' => 'Yayın Sil',],
            'en' => ['name' => 'Product Delete',],
        ],
        [
            'code' => 'product_show',
            'tr' => ['name' => 'Yayın Görüntüle',],
            'en' => ['name' => 'Product Show',],
        ],
        [
            'code' => 'product_apply_list',
            'tr' => ['name' => 'Yayın Başvuru Listele',],
            'en' => ['name' => 'Product Apply List',],
        ],
        [
            'code' => 'product_approve',
            'tr' => ['name' => 'Yayın Onayla',],
            'en' => ['name' => 'Product Approve',],
        ],
        [
            'code' => 'product_apply_correction',
            'tr' => ['name' => 'Yayın Düzeltme Başvurusu',],
            'en' => ['name' => 'Product Apply Correction',],
        ],
        [
            'code' => 'product_change_status',
            'tr' => ['name' => 'Yayın Durumu Değiştir',],
            'en' => ['name' => 'Product Change Status',],
        ],
//Song
        [
            'code' => 'song_list',
            'tr' => ['name' => 'Şarkı Listele',],
            'en' => ['name' => 'Song List',],
        ],
        [
            'code' => 'song_create',
            'tr' => ['name' => 'Şarkı Ekle',],
            'en' => ['name' => 'Song Create',],
        ],
        [
            'code' => 'song_edit',
            'tr' => ['name' => 'Şarkı Düzenle',],
            'en' => ['name' => 'Song Edit',],
        ],
        [
            'code' => 'song_delete',
            'tr' => ['name' => 'Şarkı Sil',],
            'en' => ['name' => 'Song Delete',],
        ],
        [
            'code' => 'song_show',
            'tr' => ['name' => 'Şarkı Görüntüle',],
            'en' => ['name' => 'Song Show',],
        ],
        [
            'code' => 'song_change_status',
            'tr' => ['name' => 'Şarkı Durumu Değiştir',],
            'en' => ['name' => 'Song Change Status',],
        ],
//LanguageTranslation
        [
            'code' => 'language_list',
            'tr' => ['name' => 'Diller Listele',],
            'en' => ['name' => 'Languages List',],
        ],
        [
            'code' => 'language_create',
            'tr' => ['name' => 'Dil Ekle',],
            'en' => ['name' => 'Language Create',],
        ],
        [
            'code' => 'language_translations_list',
            'tr' => ['name' => 'Dil Çevirilerini Listele',],
            'en' => ['name' => 'Language Translations List',],
        ],
        [
            'code' => 'language_translation_create',
            'tr' => ['name' => 'Dil Çevirisi Ekle',],
            'en' => ['name' => 'Language Translation Create',],
        ],
        [
            'code' => 'language_translation_update',
            'tr' => ['name' => 'Dil Çevirisi Güncelle',],
            'en' => ['name' => 'Language Translation Update',],
        ],
//Author
        [
            'code' => 'author_list',
            'tr' => ['name' => 'Yazar Listele',],
            'en' => ['name' => 'Author List',],
        ],
        [
            'code' => 'author_create',
            'tr' => ['name' => 'Yazar Ekle',],
            'en' => ['name' => 'Author Create',],
        ],
        [
            'code' => 'author_edit',
            'tr' => ['name' => 'Yazar Düzenle',],
            'en' => ['name' => 'Author Edit',],
        ],
        [
            'code' => 'author_delete',
            'tr' => ['name' => 'Yazar Sil',],
            'en' => ['name' => 'Author Delete',],
        ],
        [
            'code' => 'author_show',
            'tr' => ['name' => 'Yazar Görüntüle',],
            'en' => ['name' => 'Author Show',],
        ],
//Setting
        [
            'code' => 'setting_list',
            'tr' => ['name' => 'Ayarlar Listele',],
            'en' => ['name' => 'Setting List',],
        ],
        [
            'code' => 'setting_edit',
            'tr' => ['name' => 'Ayarlar Düzenle',],
            'en' => ['name' => 'Setting Edit',],
        ],
        [
            'code' => 'setting_show',
            'tr' => ['name' => 'Ayarlar Görüntüle',],
            'en' => ['name' => 'Setting Show',],
        ],
//Feature
        [
            'code' => 'feature_list',
            'tr' => ['name' => 'Özellik Listele',],
            'en' => ['name' => 'Feature List',],
        ],
        [
            'code' => 'feature_create',
            'tr' => ['name' => 'Özellik Ekle',],
            'en' => ['name' => 'Feature Create',],
        ],
        [
            'code' => 'feature_edit',
            'tr' => ['name' => 'Özellik Düzenle',],
            'en' => ['name' => 'Feature Edit',],
        ],
        [
            'code' => 'feature_delete',
            'tr' => ['name' => 'Özellik Sil',],
            'en' => ['name' => 'Feature Delete',],
        ],
        [
            'code' => 'feature_show',
            'tr' => ['name' => 'Özellik Görüntüle',],
            'en' => ['name' => 'Feature Show',],
        ],
//Plan
        [
            'code' => 'plan_list',
            'tr' => ['name' => 'Plan Listele',],
            'en' => ['name' => 'Plan List',],
        ],
        [
            'code' => 'plan_create',
            'tr' => ['name' => 'Plan Ekle',],
            'en' => ['name' => 'Plan Create',],
        ],
        [
            'code' => 'plan_edit',
            'tr' => ['name' => 'Plan Düzenle',],
            'en' => ['name' => 'Plan Edit',],
        ],
        [
            'code' => 'plan_delete',
            'tr' => ['name' => 'Plan Sil',],
            'en' => ['name' => 'Plan Delete',],
        ],
        [
            'code' => 'plan_show',
            'tr' => ['name' => 'Plan Görüntüle',],
            'en' => ['name' => 'Plan Show',],
        ],
//Plan Item
        [
            'code' => 'plan_item_list',
            'tr' => ['name' => 'Plan Öğesi Listele',],
            'en' => ['name' => 'Plan Item List',],
        ],
        [
            'code' => 'plan_item_create',
            'tr' => ['name' => 'Plan Öğesi Ekle',],
            'en' => ['name' => 'Plan Item Create',],
        ],
        [
            'code' => 'plan_item_edit',
            'tr' => ['name' => 'Plan Öğesi Düzenle',],
            'en' => ['name' => 'Plan Item Edit',],
        ],
        [
            'code' => 'plan_item_delete',
            'tr' => ['name' => 'Plan Öğesi Sil',],
            'en' => ['name' => 'Plan Item Delete',],
        ],
        [
            'code' => 'plan_item_show',
            'tr' => ['name' => 'Plan Öğesi Görüntüle',],
            'en' => ['name' => 'Plan Item Show',],
        ],
//Country
        [
            'code' => 'country_list',
            'tr' => ['name' => 'Ülke Listele',],
            'en' => ['name' => 'Country List',],
        ],
        [
            'code' => 'country_create',
            'tr' => ['name' => 'Ülke Ekle',],
            'en' => ['name' => 'Country Create',],
        ],
        [
            'code' => 'country_edit',
            'tr' => ['name' => 'Ülke Düzenle',],
            'en' => ['name' => 'Country Edit',],
        ],
        [
            'code' => 'country_delete',
            'tr' => ['name' => 'Ülke Sil',],
            'en' => ['name' => 'Country Delete',],
        ],
        [
            'code' => 'country_show',
            'tr' => ['name' => 'Ülke Görüntüle',],
            'en' => ['name' => 'Country Show',],
        ],
//Copyright
        [
            'code' => 'copyright_management',
            'tr' => ['name' => 'Telif Hakkı Yönetimi',],
            'en' => ['name' => 'Copyright Management',],
        ],
        [
            'code' => 'copyright_list',
            'tr' => ['name' => 'Telif Hakkı Listele',],
            'en' => ['name' => 'Copyright List',],
        ],
        [
            'code' => 'copyright_create',
            'tr' => ['name' => 'Telif Hakkı Ekle',],
            'en' => ['name' => 'Copyright Create',],
        ],
        [
            'code' => 'copyright_edit',
            'tr' => ['name' => 'Telif Hakkı Düzenle',],
            'en' => ['name' => 'Copyright Edit',],
        ],
        [
            'code' => 'copyright_delete',
            'tr' => ['name' => 'Telif Hakkı Sil',],
            'en' => ['name' => 'Copyright Delete',],
        ],
        [
            'code' => 'copyright_show',
            'tr' => ['name' => 'Telif Hakkı Görüntüle',],
            'en' => ['name' => 'Copyright Show',],
        ],
        [
            'code' => 'copyright_demand',
            'tr' => ['name' => 'Telif Hakkı Talep',],
            'en' => ['name' => 'Copyright Demand',],
        ],
//Contract
        [
            'code' => 'contract_list',
            'tr' => ['name' => 'Sözleşme Listele',],
            'en' => ['name' => 'Contract List',],
        ],
        [
            'code' => 'contract_create',
            'tr' => ['name' => 'Sözleşme Ekle',],
            'en' => ['name' => 'Contract Create',],
        ],
        [
            'code' => 'contract_edit',
            'tr' => ['name' => 'Sözleşme Düzenle',],
            'en' => ['name' => 'Contract Edit',],
        ],
        [
            'code' => 'contract_delete',
            'tr' => ['name' => 'Sözleşme Sil',],
            'en' => ['name' => 'Contract Delete',],
        ],
        [
            'code' => 'contract_show',
            'tr' => ['name' => 'Sözleşme Görüntüle',],
            'en' => ['name' => 'Contract Show',],
        ],
//Excel Export
        [
            'code' => 'excel_export_products',
            'tr' => ['name' => 'Excel Yayınları Dışa Aktar',],
            'en' => ['name' => 'Excel Export Products',],
        ],
//Excel Import
        [
            'code' => 'excel_import_products',
            'tr' => ['name' => 'Excel Yayınları İçe Aktar',],
            'en' => ['name' => 'Excel Import Products',],
        ],
//Announcement
        [
            'code' => 'announcement_list',
            'tr' => ['name' => 'Duyuru Listele',],
            'en' => ['name' => 'Announcement List',],
        ],
        [
            'code' => 'announcement_create',
            'tr' => ['name' => 'Duyuru Ekle',],
            'en' => ['name' => 'Announcement Create',],
        ],
        [
            'code' => 'announcement_edit',
            'tr' => ['name' => 'Duyuru Düzenle',],
            'en' => ['name' => 'Announcement Edit',],
        ],
        [
            'code' => 'announcement_delete',
            'tr' => ['name' => 'Duyuru Sil',],
            'en' => ['name' => 'Announcement Delete',],
        ],
        [
            'code' => 'announcement_show',
            'tr' => ['name' => 'Duyuru Görüntüle',],
            'en' => ['name' => 'Announcement Show',],
        ],
//Announcement Templates
        [
            'code' => 'announcement_template_list',
            'tr' => ['name' => 'Duyuru Şablonu Listele',],
            'en' => ['name' => 'Announcement Template List',],
        ],
        [
            'code' => 'announcement_template_create',
            'tr' => ['name' => 'Duyuru Şablonu Ekle',],
            'en' => ['name' => 'Announcement Template Create',],
        ],
        [
            'code' => 'announcement_template_edit',
            'tr' => ['name' => 'Duyuru Şablonu Düzenle',],
            'en' => ['name' => 'Announcement Template Edit',],
        ],
        [
            'code' => 'announcement_template_delete',
            'tr' => ['name' => 'Duyuru Şablonu Sil',],
            'en' => ['name' => 'Announcement Template Delete',],
        ],
        [
            'code' => 'announcement_template_show',
            'tr' => ['name' => 'Duyuru Şablonu Görüntüle',],
            'en' => ['name' => 'Announcement Template Show',],
        ],
//Title
        [
            'code' => 'title_list',
            'tr' => ['name' => 'Başlık Listele',],
            'en' => ['name' => 'Title List',],
        ],
        [
            'code' => 'title_create',
            'tr' => ['name' => 'Başlık Ekle',],
            'en' => ['name' => 'Title Create',],
        ],
        [
            'code' => 'title_edit',
            'tr' => ['name' => 'Başlık Düzenle',],
            'en' => ['name' => 'Title Edit',],
        ],
        [
            'code' => 'title_delete',
            'tr' => ['name' => 'Başlık Sil',],
            'en' => ['name' => 'Title Delete',],
        ],
        [
            'code' => 'title_show',
            'tr' => ['name' => 'Başlık Görüntüle',],
            'en' => ['name' => 'Title Show',],
        ],
//Work
        [
            'code' => 'work_list',
            'tr' => ['name' => 'İş Listele',],
            'en' => ['name' => 'Work List',],
        ],
        [
            'code' => 'work_create',
            'tr' => ['name' => 'İş Ekle',],
            'en' => ['name' => 'Work Create',],
        ],
        [
            'code' => 'work_edit',
            'tr' => ['name' => 'İş Düzenle',],
            'en' => ['name' => 'Work Edit',],
        ],
        [
            'code' => 'work_delete',
            'tr' => ['name' => 'İş Sil',],
            'en' => ['name' => 'Work Delete',],
        ],
        [
            'code' => 'work_show',
            'tr' => ['name' => 'İş Görüntüle',],
            'en' => ['name' => 'Work Show',],
        ],
//Partner
        [
            'code' => 'partner_list',
            'tr' => ['name' => 'Ortak Listele',],
            'en' => ['name' => 'Partner List',],
        ],
        [
            'code' => 'partner_create',
            'tr' => ['name' => 'Ortak Ekle',],
            'en' => ['name' => 'Partner Create',],
        ],
        [
            'code' => 'partner_edit',
            'tr' => ['name' => 'Ortak Düzenle',],
            'en' => ['name' => 'Partner Edit',],
        ],
        [
            'code' => 'partner_delete',
            'tr' => ['name' => 'Ortak Sil',],
            'en' => ['name' => 'Partner Delete',],
        ],
        [
            'code' => 'partner_show',
            'tr' => ['name' => 'Ortak Görüntüle',],
            'en' => ['name' => 'Partner Show',],
        ],
//Site Feature
        [
            'code' => 'site_feature_list',
            'tr' => ['name' => 'Site Özelliği Listele',],
            'en' => ['name' => 'Site Feature List',],
        ],
        [
            'code' => 'site_feature_create',
            'tr' => ['name' => 'Site Özelliği Ekle',],
            'en' => ['name' => 'Site Feature Create',],
        ],
        [
            'code' => 'site_feature_edit',
            'tr' => ['name' => 'Site Özelliği Düzenle',],
            'en' => ['name' => 'Site Feature Edit',],
        ],
        [
            'code' => 'site_feature_delete',
            'tr' => ['name' => 'Site Özelliği Sil',],
            'en' => ['name' => 'Site Feature Delete',],
        ],
        [
            'code' => 'site_feature_show',
            'tr' => ['name' => 'Site Özelliği Görüntüle',],
            'en' => ['name' => 'Site Feature Show',],
        ],
//Testimonials
        [
            'code' => 'testimonial_list',
            'tr' => ['name' => 'Görüş Listele',],
            'en' => ['name' => 'Testimonials List',],
        ],
        [
            'code' => 'testimonial_create',
            'tr' => ['name' => 'Görüş Ekle',],
            'en' => ['name' => 'Testimonials Create',],
        ],
        [
            'code' => 'testimonial_edit',
            'tr' => ['name' => 'Görüş Düzenle',],
            'en' => ['name' => 'Testimonials Edit',],
        ],
        [
            'code' => 'testimonial_delete',
            'tr' => ['name' => 'Görüş Sil',],
            'en' => ['name' => 'Testimonials Delete',],
        ],
        [
            'code' => 'testimonial_show',
            'tr' => ['name' => 'Görüş Görüntüle',],
            'en' => ['name' => 'Testimonials Show',],
        ],
//HelpCenter
        [
            'code' => 'help_center_list',
            'tr' => ['name' => 'Yardım Merkezi Listele',],
            'en' => ['name' => 'HelpCenter List',],
        ],
        [
            'code' => 'help_center_create',
            'tr' => ['name' => 'Yardım Merkezi Ekle',],
            'en' => ['name' => 'HelpCenter Create',],
        ],
        [
            'code' => 'help_center_edit',
            'tr' => ['name' => 'Yardım Merkezi Düzenle',],
            'en' => ['name' => 'HelpCenter Edit',],
        ],
        [
            'code' => 'help_center_delete',
            'tr' => ['name' => 'Yardım Merkezi Sil',],
            'en' => ['name' => 'HelpCenter Delete',],
        ],
        [
            'code' => 'help_center_show',
            'tr' => ['name' => 'Yardım Merkezi Görüntüle',],
            'en' => ['name' => 'HelpCenter Show',],
        ],
//Integration
        [
            'code' => 'integration_list',
            'tr' => ['name' => 'Entegrasyon Listele',],
            'en' => ['name' => 'Integration List',],
        ],
        [
            'code' => 'integration_edit',
            'tr' => ['name' => 'Entegrasyon Düzenle',],
            'en' => ['name' => 'Integration Edit',],
        ],
//Platform
        [
            'code' => 'platform_list',
            'tr' => ['name' => 'Platform Listele',],
            'en' => ['name' => 'Platform List',],
        ],
        [
            'code' => 'platform_create',
            'tr' => ['name' => 'Platform Ekle',],
            'en' => ['name' => 'Platform Create',],
        ],
        [
            'code' => 'platform_edit',
            'tr' => ['name' => 'Platform Düzenle',],
            'en' => ['name' => 'Platform Edit',],
        ],
        [
            'code' => 'platform_delete',
            'tr' => ['name' => 'Platform Sil',],
            'en' => ['name' => 'Platform Delete',],
        ],
        [
            'code' => 'platform_show',
            'tr' => ['name' => 'Platform Görüntüle',],
            'en' => ['name' => 'Platform Show',],
        ],
        [
            'code' => 'platform_match',
            'tr' => ['name' => 'Platform Eşleştir',],
            'en' => ['name' => 'Platform Match',],
        ],
//DDEX
        [
            'code' => 'product_make_xml',
            'tr' => ['name' => 'DDEX XML Oluştur',],
            'en' => ['name' => 'DDEX XML Generate',],
        ],
        [
            'code' => 'product_download_xml',
            'tr' => ['name' => 'DDEX XML İndir',],
            'en' => ['name' => 'DDEX XML Download',],
        ],
//Finance&Earning
        [
            'code' => 'finance_earning_list',
            'tr' => ['name' => 'Finans & Kazanç Listele',],
            'en' => ['name' => 'Finance&Earning List',],
        ],
        [
            'code' => 'finance_earning_create',
            'tr' => ['name' => 'Finans & Kazanç Ekle',],
            'en' => ['name' => 'Finance&Earning Create',],
        ],
        [
            'code' => 'finance_earning_edit',
            'tr' => ['name' => 'Finans & Kazanç Düzenle',],
            'en' => ['name' => 'Finance&Earning Edit',],
        ],
        [
            'code' => 'finance_earning_delete',
            'tr' => ['name' => 'Finans & Kazanç Sil',],
            'en' => ['name' => 'Finance&Earning Delete',],
        ],
        [
            'code' => 'finance_earning_show',
            'tr' => ['name' => 'Finans & Kazanç Görüntüle',],
            'en' => ['name' => 'Finance&Earning Show',],
        ],
//Earning Report File
        [
            'code' => 'earning_report_file_list',
            'tr' => ['name' => 'Kazanç Rapor Dosyası Listele',],
            'en' => ['name' => 'Report File List',],
        ],
        [
            'code' => 'earning_report_file_create',
            'tr' => ['name' => 'Kazanç Rapor Dosyası Ekle',],
            'en' => ['name' => 'Report File Create',],
        ],
        [
            'code' => 'earning_report_file_edit',
            'tr' => ['name' => 'Kazanç Rapor Dosyası Düzenle',],
            'en' => ['name' => 'Report File Edit',],
        ],
        [
            'code' => 'earning_report_file_delete',
            'tr' => ['name' => 'Kazanç Rapor Dosyası Sil',],
            'en' => ['name' => 'Report File Delete',],
        ],
        [
            'code' => 'earning_report_file_show',
            'tr' => ['name' => 'Kazanç Rapor Dosyası Görüntüle',],
            'en' => ['name' => 'Report File Show',],
        ],
        [
            'code' => 'earning_report_file_download',
            'tr' => ['name' => 'Kazanç Rapor Dosyası İndir',],
            'en' => ['name' => 'Report File Download',],
        ],
        //Report
        [
            'code' => 'report_list',
            'tr' => ['name' => 'Kazanç Raporu Listele',],
            'en' => ['name' => 'Report List',],
        ],
        [
            'code' => 'report_create',
            'tr' => ['name' => 'Kazanç Raporu Ekle',],
            'en' => ['name' => 'Report Create',],
        ],
        [
            'code' => 'report_edit',
            'tr' => ['name' => 'Kazanç Raporu Düzenle',],
            'en' => ['name' => 'Report Edit',],
        ],
        [
            'code' => 'report_delete',
            'tr' => ['name' => 'Kazanç Raporu Sil',],
            'en' => ['name' => 'Report Delete',],
        ],
        [
            'code' => 'report_show',
            'tr' => ['name' => 'Kazanç Raporu Görüntüle',],
            'en' => ['name' => 'Report Show',],
        ],
        //Earning
        [
            'code' => 'earning_list',
            'tr' => ['name' => 'Kazanç Listele',],
            'en' => ['name' => 'Earning List',],
        ],
        [
            'code' => 'earning_create',
            'tr' => ['name' => 'Kazanç Ekle',],
            'en' => ['name' => 'Earning Create',],
        ],
        [
            'code' => 'earning_edit',
            'tr' => ['name' => 'Kazanç Düzenle',],
            'en' => ['name' => 'Earning Edit',],
        ],
        [
            'code' => 'earning_delete',
            'tr' => ['name' => 'Kazanç Sil',],
            'en' => ['name' => 'Earning Delete',],
        ],
        [
            'code' => 'earning_show',
            'tr' => ['name' => 'Kazanç Görüntüle',],
            'en' => ['name' => 'Earning Show',],
        ],
//Payment
        [
            'code' => 'payment_list',
            'tr' => ['name' => 'Ödeme Listele',],
            'en' => ['name' => 'Payment List',],
        ],
        [
            'code' => 'payment_create',
            'tr' => ['name' => 'Ödeme Ekle',],
            'en' => ['name' => 'Payment Create',],
        ],
        [
            'code' => 'payment_edit',
            'tr' => ['name' => 'Ödeme Düzenle',],
            'en' => ['name' => 'Payment Edit',],
        ],
        [
            'code' => 'payment_delete',
            'tr' => ['name' => 'Ödeme Sil',],
            'en' => ['name' => 'Payment Delete',],
        ],
        [
            'code' => 'payment_show',
            'tr' => ['name' => 'Ödeme Görüntüle',],
            'en' => ['name' => 'Payment Show',],
        ],
//Invoice
        [
            'code' => 'invoice_list',
            'tr' => ['name' => 'Fatura Listele',],
            'en' => ['name' => 'Invoice List',],
        ],
        [
            'code' => 'invoice_create',
            'tr' => ['name' => 'Fatura Ekle',],
            'en' => ['name' => 'Invoice Create',],
        ],
        [
            'code' => 'invoice_edit',
            'tr' => ['name' => 'Fatura Düzenle',],
            'en' => ['name' => 'Invoice Edit',],
        ],
        [
            'code' => 'invoice_delete',
            'tr' => ['name' => 'Fatura Sil',],
            'en' => ['name' => 'Invoice Delete',],
        ],
        [
            'code' => 'invoice_show',
            'tr' => ['name' => 'Fatura Görüntüle',],
            'en' => ['name' => 'Invoice Show',],
        ],
//Report
        [
            'code' => 'report_list',
            'tr' => ['name' => 'Rapor Listele',],
            'en' => ['name' => 'Report List',],
        ],
        [
            'code' => 'report_create',
            'tr' => ['name' => 'Rapor Ekle',],
            'en' => ['name' => 'Report Create',],
        ],
        [
            'code' => 'report_edit',
            'tr' => ['name' => 'Rapor Düzenle',],
            'en' => ['name' => 'Report Edit',],
        ],
        [
            'code' => 'report_delete',
            'tr' => ['name' => 'Rapor Sil',],
            'en' => ['name' => 'Report Delete',],
        ],
        [
            'code' => 'report_show',
            'tr' => ['name' => 'Rapor Görüntüle',],
            'en' => ['name' => 'Report Show',],
        ],
//Upc
        [
            'code' => 'upc_list',
            'tr' => ['name' => 'UPC Listele',],
            'en' => ['name' => 'Upc List',],
        ],
        [
            'code' => 'upc_create',
            'tr' => ['name' => 'UPC Ekle',],
            'en' => ['name' => 'Upc Create',],
        ],
        //Mail Template
        [
            'code' => 'mail_template_list',
            'tr' => ['name' => 'Mail Şablonu Listele',],
            'en' => ['name' => 'Mail Template List',],
        ],
        [
            'code' => 'mail_template_create',
            'tr' => ['name' => 'Mail Şablonu Ekle',],
            'en' => ['name' => 'Mail Template Create',],
        ],
        [
            'code' => 'mail_template_edit',
            'tr' => ['name' => 'Mail Şablonu Düzenle',],
            'en' => ['name' => 'Mail Template Edit',],
        ],
        [
            'code' => 'mail_template_delete',
            'tr' => ['name' => 'Mail Şablonu Sil',],
            'en' => ['name' => 'Mail Template Delete',],
        ],
        [
            'code' => 'mail_template_show',
            'tr' => ['name' => 'Mail Şablonu Görüntüle',],
            'en' => ['name' => 'Mail Template Show',],
        ],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$permissions as $permission) {

            $permissionEntry = Permission::updateOrCreate(
                ['code' => $permission['code']],
                ['code' => $permission['code']]
            );

            foreach (['tr', 'en'] as $locale) {
                PermissionTranslation::updateOrCreate(
                    ['permission_id' => $permissionEntry->id, 'locale' => $locale],
                    ['name' => $permission[$locale]['name']]
                );
            }
        }
    }
}

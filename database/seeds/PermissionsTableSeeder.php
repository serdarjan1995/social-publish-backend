<?php

use App\Model\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id' => 1,
                'name' => 'permission_create'
            ],
            [
                'id' => 2,
                'name' => 'permission_edit'
            ],
            [
                'id' => 3,
                'name' => 'permission_show'
            ],
            [
                'id' => 4,
                'name' => 'permission_delete'
            ],
            [
                'id' => 5,
                'name' => 'permission_access'
            ],
            [
                'id' => 6,
                'name' => 'role_create'
            ],
            [
                'id' => 7,
                'name' => 'role_edit'
            ],
            [
                'id' => 8,
                'name' => 'role_show'
            ],
            [
                'id' => 9,
                'name' => 'role_delete'
            ],
            [
                'id' => 10,
                'name' => 'role_access'
            ],
            [
                'id' => 11,
                'name' => 'user_create'
            ],
            [
                'id' => 12,
                'name' => 'user_edit'
            ],
            [
                'id' => 13,
                'name' => 'user_show'
            ],
            [
                'id' => 14,
                'name' => 'user_delete'
            ],
            [
                'id' => 15,
                'name' => 'user_access'
            ],
            [
                'id' => 16,
                'name' => 'user_profile_show'
            ],
            [
                'id' => 17,
                'name' => 'user_profile_edit'
            ],
            [
                'id' => 18,
                'name' => 'user_profile_password_edit'
            ],
            [
                'id' => 19,
                'name' => 'user_profile_access'
            ],
            [
                'id' => 20,
                'name' => 'social_media_list'
            ],
            [
                'id' => 21,
                'name' => 'social_media_create'
            ],
            [
                'id' => 22,
                'name' => 'social_media_show'
            ],
            [
                'id' => 23,
                'name' => 'social_media_update'
            ],
            [
                'id' => 24,
                'name' => 'social_media_destroy'
            ],
            [
                'id' => 25,
                'name' => 'social_media_access'
            ],
            [
                'id' => 26,
                'name' => 'social_media_api_set'
            ],
            [
                'id' => 27,
                'name' => 'social_media_api_show'
            ],
            [
                'id' => 28,
                'name' => 'social_media_api_update'
            ],
            [
                'id' => 29,
                'name' => 'social_media_api_destroy'
            ],
            [
                'id' => 30,
                'name' => 'file_managers_create'
            ],
            [
                'id' => 31,
                'name' => 'file_managers_show'
            ],
            [
                'id' => 32,
                'name' => 'file_managers_access'
            ],
            [
                'id' => 33,
                'name' => 'users_status'
            ],
            [
                'id' => 34,
                'name' => 'text_note_list'
            ],
            [
                'id' => 35,
                'name' => 'text_note_store'
            ],
            [
                'id' => 36,
                'name' => 'text_note_show'
            ],
            [
                'id' => 37,
                'name' => 'text_note_update'
            ],
            [
                'id' => 38,
                'name' => 'text_note_delete'
            ],
            [
                'id' => 39,
                'name' => 'text_note_access'
            ],
            [
                'id' => 40,
                'name' => 'post_social_create'
            ],
            [
                'id' => 41,
                'name' => 'post_social_update'
            ],
            [
                'id' => 42,
                'name' => 'post_social_delete'
            ],
            [
                'id' => 43,
                'name' => 'post_social_show'
            ],
            [
                'id' => 44,
                'name' => 'post_social_access'
            ],
            [
                'id' => 45,
                'name' => 'post_social_list'
            ],
            [
                'id' => 46,
                'name' => 'post_social_list_access'
            ],
            [
                'id' => 47,
                'name' => 'payment_create'
            ],
            [
                'id' => 48,
                'name' => 'payment_all_user_show'
            ],
            [
                'id' => 49,
                'name' => 'payment_user_show'
            ],
            [
                'id' => 50,
                'name' => 'payment_user_access'
            ],
            [
                'id' => 51,
                'name' => 'payment_admin_access'
            ],
            [
                'id' => 52,
                'name' => 'post_watermark_show_create'
            ],
            [
                'id' => 53,
                'name' => 'account_show'
            ],
            [
                'id' => 54,
                'name' => 'account_add'
            ],
            [
                'id' => 55,
                'name' => 'account_edit'
            ],
            [
                'id' => 56,
                'name' => 'account_delete'
            ],
            [
                'id' => 57,
                'name' => 'social_account_access'
            ],
            [
                'id' => 58,
                'name' => 'proxy_management'
            ],
            [
                'id' => 59,
                'name' => 'proxy_management_access'
            ],
            [
                'id' => 60,
                'name' => 'plan_create'
            ],
            [
                'id' => 61,
                'name' => 'plan_delete'
            ],
            [
                'id' => 62,
                'name' => 'plan_edit'
            ],
            [
                'id' => 63,
                'name' => 'plan_access'
            ],
            [
                'id' => 64,
                'name' => 'publish_access'
            ],
            [
                'id' => 65,
                'name' => 'calendar_access'
            ],
            [
                'id' => 66,
                'name' => 'ticket_access'
            ],
            [
                'id' => 67,
                'name' => 'chat_access'
            ],
            [
                'id' => 68,
                'name' => 'dashboard_trademark_access'
            ],
            [
                'id' => 69,
                'name' => 'dashboard_bills_access'
            ],
            [
                'id' => 70,
                'name' => 'dashboard_customers_access'
            ],
            [
                'id' => 71,
                'name' => 'dashboard_sale_reports_access'
            ],
            [
                'id' => 72,
                'name' => 'dashboard_collecting_reports_access'
            ],
            [
                'id' => 73,
                'name' => 'dashboard_income_statements_access'
            ],
            [
                'id' => 74,
                'name' => 'dashboard_expense_list_access'
            ],
            [
                'id' => 75,
                'name' => 'dashboard_suppliers_access'
            ],
            [
                'id' => 76,
                'name' => 'dashboard_employees_access'
            ],
            [
                'id' => 77,
                'name' => 'dashboard_expense_reports_access'
            ],
            [
                'id' => 78,
                'name' => 'dashboard_social_post_access'
            ],
            [
                'id' => 79,
                'name' => 'dashboard_social_live_stream_access'
            ],
            [
                'id' => 80,
                'name' => 'dashboard_social_direct_message_access'
            ],
            [
                'id' => 81,
                'name' => 'dashboard_operational_access'
            ]
            ,
            [
                'id' => 82,
                'name' => 'dashboard_analytical_access'
            ]
            ,
            [
                'id' => 83,
                'name' => 'dashboard_admin_access'
            ]
            ,
            [
                'id' => 84,
                'name' => 'dashboard_media_access'
            ]
            ,
            [
                'id' => 85,
                'name' => 'dashboard_shipment_access'
            ]
            ,
            [
                'id' => 86,
                'name' => 'dashboard_traffic_access'
            ]
            ,
            [
                'id' => 87,
                'name' => 'dashboard_caption_access'
            ]
            ,
            [
                'id' => 88,
                'name' => 'dashboard_watermark_access'
            ]
            ,
            [
                'id' => 89,
                'name' => 'dashboard_bookmark_access'
            ],
            [
                'id' => 90,
                'name' => 'social_callback_access'
            ],
            [
                'id' => 91,
                'name' => 'user_management_access'
            ],
            [
                'id' => 92,
                'name' => 'admin_settings_general_access'
            ],
            [
                'id' => 93,
                'name' => 'admin_settings_social_access'
            ],
            [
                'id' => 94,
                'name' => 'admin_cron_jobs_access'
            ],
            [
                'id' => 95,
                'name' => 'user_settings_access'
            ],
            [
                'id' => 96,
                'name' => 'user_make_payment_access'
            ],
            [
                'id' => 97,
                'name' => 'social_post_access'
            ],
            [
                'id' => 98,
                'name' => 'social_live_access'
            ],
            [
                'id' => 99,
                'name' => 'social_message_access'
            ],
            [
                'id' => 100,
                'name' => 'facebook_social_access'
            ],
            [
                'id' => 101,
                'name' => 'google_social_access'
            ],
            [
                'id' => 102,
                'name' => 'instagram_social_access'
            ],
            [
                'id' => 103,
                'name' => 'linkedin_social_access'
            ],
            [
                'id' => 104,
                'name' => 'ok_social_access'
            ],
            [
                'id' => 105,
                'name' => 'pinterest_social_access'
            ],
            [
                'id' => 106,
                'name' => 'reddit_social_access'
            ],
            [
                'id' => 107,
                'name' => 'tumblr_social_access'
            ],
            [
                'id' => 108,
                'name' => 'twitter_social_access'
            ],
            [
                'id' => 109,
                'name' => 'telegram_social_access'
            ],
            [
                'id' => 110,
                'name' => 'vk_social_access'
            ],
            [
                'id' => 111,
                'name' => 'whatsapp_social_access'
            ],
            [
                'id' => 112,
                'name' => 'youtube_social_access'
            ],
            [
                'id' => 113,
                'name' => 'ticket_user_access'
            ],
            [
                'id' => 114,
                'name' => 'ticket_admin_access'
            ],
            [
                'id' => 115,
                'name' => 'ticket_moder_access'
            ],
            [
                'id' => 116,
                'name' => 'admin_settings_access'
            ],
            [
                'id' => 117,
                'name' => 'users_access'
            ],
            [
                'id' => 118,
                'name' => 'agency_user_create'
            ],
            [
                'id' => 119,
                'name' => 'agency_user_show'
            ],
            [
                'id' => 120,
                'name' => 'agency_user_edit'
            ],
            [
                'id' => 121,
                'name' => 'agency_user_delete'
            ],
        ];

        Permission::insert($permissions);
    }
}

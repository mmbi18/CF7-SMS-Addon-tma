<?php
/*
Plugin Name: CF7 SMS Addon 
Description: Adds an SMS API settings tab to Contact Form 7 for sending SMS. MSGway راه پیام msgway.com
Version: 1.0
Author: mohammad bagheri
*/

// افزودن تب جدید برای تنظیمات API
function cf7_sms_addon_add_tab($panels) {
    $panels['sms-panel'] = array(
        'title' => __('SMS Settings', 'contact-form-7'),
        'callback' => 'cf7_sms_addon_settings'
    );
    return $panels;
}
add_filter('wpcf7_editor_panels', 'cf7_sms_addon_add_tab');

function cf7_sms_addon_settings($post) {
    $sms_api_key = get_post_meta($post->id(), '_sms_api_key', true);
    $sms_template_id = get_post_meta($post->id(), '_sms_template_id', true);
    $sms_param1 = get_post_meta($post->id(), '_sms_param1', true);
    $sms_param2 = get_post_meta($post->id(), '_sms_param2', true);
    $sms_param3 = get_post_meta($post->id(), '_sms_param3', true);
    ?>
    <div class="metabox-holder">
        <h2><?php _e('SMS API Settings', 'contact-form-7'); ?></h2>
        <form method="post">
            <label for="sms_api_key"><?php _e('API Key', 'contact-form-7'); ?></label>
            <input type="text" id="sms_api_key" name="sms_api_key" value="<?php echo esc_attr($sms_api_key); ?>">
            <br>
            <label for="sms_template_id"><?php _e('Template ID', 'contact-form-7'); ?></label>
            <input type="text" id="sms_template_id" name="sms_template_id" value="<?php echo esc_attr($sms_template_id); ?>">
            <br>
            <label for="sms_param1"><?php _e('Parameter 1 Field Name', 'contact-form-7'); ?></label>
            <input type="text" id="sms_param1" name="sms_param1" value="<?php echo esc_attr($sms_param1); ?>">
            <br>
            <label for="sms_param2"><?php _e('Parameter 2 Field Name', 'contact-form-7'); ?></label>
            <input type="text" id="sms_param2" name="sms_param2" value="<?php echo esc_attr($sms_param2); ?>">
            <br>
            <label for="sms_param3"><?php _e('Parameter 3 Field Name', 'contact-form-7'); ?></label>
            <input type="text" id="sms_param3" name="sms_param3" value="<?php echo esc_attr($sms_param3); ?>">
            <br>
            <input type="submit" name="save_sms_settings" value="<?php _e('Save', 'contact-form-7'); ?>" class="button-primary">
        </form>
    </div>
    <?php
}

// ذخیره تنظیمات API
function cf7_sms_addon_save_settings($contact_form) {
    if (isset($_POST['save_sms_settings'])) {
        update_post_meta($contact_form->id(), '_sms_api_key', sanitize_text_field($_POST['sms_api_key']));
        update_post_meta($contact_form->id(), '_sms_template_id', sanitize_text_field($_POST['sms_template_id']));
        update_post_meta($contact_form->id(), '_sms_param1', sanitize_text_field($_POST['sms_param1']));
        update_post_meta($contact_form->id(), '_sms_param2', sanitize_text_field($_POST['sms_param2']));
        update_post_meta($contact_form->id(), '_sms_param3', sanitize_text_field($_POST['sms_param3']));
    }
}
add_action('wpcf7_after_save', 'cf7_sms_addon_save_settings');
function sms_send_shortcodetma() {
    
    s_fast2MSGway('aa0a3f35a4ca1a07e9da6c2276a1009c', '09981009827', '09981009827', 't', 't', 12965, 1, 1);
    
}
//add_shortcode('tmasms', 'sms_send_shortcodetma');
// ارسال پیامک پس از ارسال فرم
function cf7_sms_addon_send_sms($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        
        $sms_api_key = get_post_meta($contact_form->id(), '_sms_api_key', true);
        $sms_template_id = get_post_meta($contact_form->id(), '_sms_template_id', true);
        $sms_param1 = get_post_meta($contact_form->id(), '_sms_param1', true);
        $sms_param2 = get_post_meta($contact_form->id(), '_sms_param2', true);
        $sms_param3 = get_post_meta($contact_form->id(), '_sms_param3', true);

        $mobile = sanitize_text_field($posted_data[$sms_param1]);  // نام فیلد تلفن
        $para1 = sanitize_text_field($posted_data[$sms_param1]);    // نام فیلد پارامتر 1
        $para2 = sanitize_text_field($posted_data[$sms_param2]);    // نام فیلد پارامتر 2
        $para3 = sanitize_text_field($posted_data[$sms_param3]);    // نام فیلد پارامتر 3
        
        if (!empty($sms_api_key) && !empty($mobile)) {
            s_fast2MSGway($sms_api_key, $mobile, $para1, '/t?uid=' . $para2, $para3, (int)$sms_template_id, 1, 1);
        }
       // echo $mobile;
    }
}
add_action('wpcf7_mail_sent', 'cf7_sms_addon_send_sms');

// تابع ارسال پیامک
function s_fast2MSGway($Token, $m, $para1, $para2, $para3, $tmpID1rah, $providerOrder, $provider) {
    $apiKey = $Token;

    $params = [
        "mobile" => "+98" . $m,
        "method" => "sms",
        "provider" => $provider,
        "providerOrder" => $providerOrder,
        "templateID" => $tmpID1rah,
        "params" => [
            $para1,
            $para2,
            $para3
        ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.msgway.com/send',
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($params),
        CURLOPT_HTTPHEADER => array(
            'apiKey: ' . $apiKey,
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

<?php

return [
    'exception_message' => 'விதிவிலக்கு செய்தி: :message',
    'exception_trace' => 'விதிவிலக்கு கண்காணிப்பு: :trace',
    'exception_message_title' => 'விதிவிலக்கு செய்தி',
    'exception_trace_title' => 'விதிவிலக்கு கண்காணிப்பு',

    'backup_failed_subject' => ':application_name இன் பின்சரிவு தோல்வியடைந்தது',
    'backup_failed_body' => 'முக்கியமான தகவல்: :application_name இன் பின்சரிவை எடுக்கும் போது தோல்வியடைந்தது',

    'backup_successful_subject' => ':application_name இன் பின்சரிவு வெற்றிகரமானது',
    'backup_successful_subject_title' => 'பின்சரிவு வெற்றிகரமானது!',
    'backup_successful_body' => 'நல்ல செய்தி, :application_name இன் பின்சரிவு :disk_name இல் வெற்றிகரமாக சேமிக்கப்பட்டது.',

    'cleanup_failed_subject' => ':application_name இன் பின்சரிவு துப்புரவு தோல்வியடைந்தது.',
    'cleanup_failed_body' => ':application_name இன் பின்சரிவை துப்புரவு செய்யும் போது சிக்கல் ஏற்பட்டது.',

    'cleanup_successful_subject' => ':application_name இன் பின்சரிவு துப்புரவு வெற்றிகரமானது',
    'cleanup_successful_subject_title' => 'பின்சரிவு துப்புரவு வெற்றிகரமானது!',
    'cleanup_successful_body' => ':application_name இன் பின்சரிவு :disk_name டிஸ்க்கில் சேமிக்கப்பட்டது, அதன் துப்புரவு வெற்றிகரமானது.',

    'healthy_backup_found_subject' => ':disk_name டிஸ்க்கில் சேமிக்கப்பட்ட :application_name இன் பின்சரிவு ஆரோக்கியமானது',
    'healthy_backup_found_subject_title' => ':application_name இன் அனைத்து பின்சரிவுகளும் ஆரோக்கியமானவை',
    'healthy_backup_found_body' => 'அற்புதம்! :application_name இன் அனைத்து பின்சரிவுகளும் ஆரோக்கியமானவை.',

    'unhealthy_backup_found_subject' => 'முக்கியமான தகவல்: :application_name இன் பின்சரிவு ஆரோக்கியமற்றது',
    'unhealthy_backup_found_subject_title' => 'முக்கியமான தகவல்: :application_name இன் பின்சரிவு :problem காரணமாக ஆரோக்கியமற்றது',
    'unhealthy_backup_found_body' => ':disk_name டிஸ்க்கில் சேமிக்கப்பட்ட :application_name இன் பின்சரிவு ஆரோக்கியமற்றது',
    'unhealthy_backup_found_not_reachable' => ':error காரணமாக பின்சரிவு இலக்கை அடைய முடியாது.',
    'unhealthy_backup_found_empty' => 'இந்த பயன்பாட்டுக்கு எந்த பின்சரிவும் இல்லை.',
    'unhealthy_backup_found_old' => 'சமீபத்தில் :date அன்று எடுக்கப்பட்ட பின்சரிவு மிகவும் பழையது.',
    'unhealthy_backup_found_unknown' => 'மன்னிக்கவும், சரியான காரணத்தை தீர்மானிக்க முடியாது.',
    'unhealthy_backup_found_full' => 'அனைத்து பின்சரிவுகளும் மிக அதிக இடத்தைப் பயன்படுத்துகின்றன. தற்போது அனைத்து பின்சரிவுகளும் :disk_usage இடத்தைப் பயன்படுத்துகின்றன, இது :disk_limit அனுமதி வரம்பை விட அதிகமானது.',

    'no_backups_info' => 'இன்னும் எந்த பின்சரிவும் உருவாக்கப்படவில்லை',
    'application_name' => 'பயன்பாட்டின் பெயர்',
    'backup_name' => 'பின்சரிவு பெயர்',
    'disk' => 'டிஸ்க்',
    'newest_backup_size' => 'புதிய பின்சரிவு அளவு',
    'number_of_backups' => 'பின்சரிவுகளின் எண்ணிக்கை',
    'total_storage_used' => 'பயன்படுத்தப்பட்ட மொத்த சேமிப்பு',
    'newest_backup_date' => 'புதிய பின்சரிவு தேதி',
    'oldest_backup_date' => 'பழைய பின்சரிவு தேதி',
];
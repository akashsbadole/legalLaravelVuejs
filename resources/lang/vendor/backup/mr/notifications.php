<?php

return [
    'exception_message' => 'अपवाद संदेश: :message',
    'exception_trace' => 'अपवाद निशान: :trace',
    'exception_message_title' => 'अपवादी संदेश',
    'exception_trace_title' => 'अपवाद निशान',

    'backup_failed_subject' => ':application_name चा बॅकअप अयशस्वी झाला',
    'backup_failed_body' => 'महत्त्वाची सूचना: :application_name चा बॅकअप घेताना अयशस्वी झाले',

    'backup_successful_subject' => ':application_name चा बॅकअप यशस्वी झाला',
    'backup_successful_subject_title' => 'बॅकअप यशस्वी!',
    'backup_successful_body' => 'आनंदाची बात, :application_name चा बॅकअप :disk_name वर यशस्वीरित्या संग्रहित झाला.',

    'cleanup_failed_subject' => ':application_name च्या बॅकअपची सफाई अयशस्वी झाली.',
    'cleanup_failed_body' => ':application_name च्या बॅकअपची सफाई करताना काही अडचण आली.',

    'cleanup_successful_subject' => ':application_name च्या बॅकअपची सफाई यशस्वी झाली',
    'cleanup_successful_subject_title' => 'बॅकअपची सफाई यशस्वी!',
    'cleanup_successful_body' => ':application_name चा बॅकअप जो :disk_name नावाच्या डिस्कवर संग्रहित आहे, त्याची सफाई यशस्वी झाली.',

    'healthy_backup_found_subject' => ':disk_name नावाच्या डिस्कवर संग्रहित :application_name चा बॅकअप निरोगी आहे',
    'healthy_backup_found_subject_title' => ':application_name चे सर्व बॅकअप निरोगी आहेत',
    'healthy_backup_found_body' => 'खूप छान! :application_name चे सर्व बॅकअप निरोगी आहेत.',

    'unhealthy_backup_found_subject' => 'महत्त्वाची सूचना: :application_name चा बॅकअप अस्वस्थ आहे',
    'unhealthy_backup_found_subject_title' => 'महत्त्वाची सूचना: :application_name चा बॅकअप :problem मुळे अस्वस्थ आहे',
    'unhealthy_backup_found_body' => ':disk_name नावाच्या डिस्कवर संग्रहित :application_name चा बॅकअप अस्वस्थ आहे',
    'unhealthy_backup_found_not_reachable' => ':error मुळे बॅकअपच्या गंतव्यस्थानापर्यंत पोहोचू शकत नाही.',
    'unhealthy_backup_found_empty' => 'या अॅप्लिकेशनचा कोणताही बॅकअप नाही.',
    'unhealthy_backup_found_old' => 'अलीकडेच :date रोजी घेतलेला बॅकअप खूप जुना आहे.',
    'unhealthy_backup_found_unknown' => 'क्षमस्व, योग्य कारण निश्चित करू शकत नाही.',
    'unhealthy_backup_found_full' => 'सर्व बॅकअप खूप जास्त जागा वापरत आहेत. सध्या सर्व बॅकअप :disk_usage जागा वापरत आहेत, जी :disk_limit परवानगी मर्यादेपेक्षा जास्त आहे.',

    'no_backups_info' => 'अद्याप कोणताही बॅकअप तयार केला गेला नाही',
    'application_name' => 'अर्जाचे नाव',
    'backup_name' => 'बॅकअप नाव',
    'disk' => 'डिस्क',
    'newest_backup_size' => 'नवीनतम बॅकअप आकार',
    'number_of_backups' => 'बॅकअपची संख्या',
    'total_storage_used' => 'वापरलेली एकूण साठवण',
    'newest_backup_date' => 'नवीनतम बॅकअप तारीख',
    'oldest_backup_date' => 'सर्वात जुना बॅकअप तारीख',
];
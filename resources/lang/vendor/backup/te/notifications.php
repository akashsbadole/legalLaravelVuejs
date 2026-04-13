<?php

return [
    'exception_message' => 'మినహాయింపు సందేశం: :message',
    'exception_trace' => 'మినహాయింపు ట్రేస్: :trace',
    'exception_message_title' => 'మినహాయింపు సందేశం',
    'exception_trace_title' => 'మినహాయింపు ట్రేస్',

    'backup_failed_subject' => ':application_name యొక్క బ్యాకప్ విఫలమైంది',
    'backup_failed_body' => 'ముఖ్యమైన సమాచారం: :application_name యొక్క బ్యాకప్ తీసుకోవడంలో విఫలమైంది',

    'backup_successful_subject' => ':application_name యొక్క బ్యాకప్ విజయవంతమైంది',
    'backup_successful_subject_title' => 'బ్యాకప్ విజయవంతమైంది!',
    'backup_successful_body' => 'శుభవార్త, :application_name యొక్క బ్యాకప్ :disk_nameలో విజయవంతంగా నిల్వ చేయబడింది.',

    'cleanup_failed_subject' => ':application_name యొక్క బ్యాకప్ క్లీనప్ విఫలమైంది.',
    'cleanup_failed_body' => ':application_name యొక్క బ్యాకప్ క్లీనప్ చేస్తున్నప్పుడు సమస్య వచ్చింది.',

    'cleanup_successful_subject' => ':application_name యొక్క బ్యాకప్ క్లీనప్ విజయవంతమైంది',
    'cleanup_successful_subject_title' => 'బ్యాకప్ క్లీనప్ విజయవంతమైంది!',
    'cleanup_successful_body' => ':application_name యొక్క బ్యాకప్ :disk_name డిస్క్‌లో నిల్వ చేయబడినది, దాని క్లీనప్ విజయవంతమైంది.',

    'healthy_backup_found_subject' => ':disk_name డిస్క్‌లో నిల్వ చేయబడిన :application_name యొక్క బ్యాకప్ ఆరోగ్యంగా ఉంది',
    'healthy_backup_found_subject_title' => ':application_name యొక్క అన్ని బ్యాకప్‌లు ఆరోగ్యంగా ఉన్నాయి',
    'healthy_backup_found_body' => 'అద్భుతం! :application_name యొక్క అన్ని బ్యాకప్‌లు ఆరోగ్యంగా ఉన్నాయి.',

    'unhealthy_backup_found_subject' => 'ముఖ్యమైన సమాచారం: :application_name యొక్క బ్యాకప్ అనారోగ్యంగా ఉంది',
    'unhealthy_backup_found_subject_title' => 'ముఖ్యమైన సమాచారం: :application_name యొక్క బ్యాకప్ :problem కారణంగా అనారోగ్యంగా ఉంది',
    'unhealthy_backup_found_body' => ':disk_name డిస్క్‌లో నిల్వ చేయబడిన :application_name యొక్క బ్యాకప్ అనారోగ్యంగా ఉంది',
    'unhealthy_backup_found_not_reachable' => ':error కారణంగా బ్యాకప్ గమ్యస్థానానికి చేరుకోలేము.',
    'unhealthy_backup_found_empty' => 'ఈ అప్లికేషన్‌కు ఎటువంటి బ్యాకప్ లేదు.',
    'unhealthy_backup_found_old' => 'ఇటీవల :date న చేసిన బ్యాకప్ చాలా పాతది.',
    'unhealthy_backup_found_unknown' => 'క్షమించండి, సరైన కారణాన్ని నిర్ణయించలేము.',
    'unhealthy_backup_found_full' => 'అన్ని బ్యాకప్‌లు చాలా ఎక్కువ స్థలాన్ని ఉపయోగిస్తున్నాయి. ప్రస్తుతం అన్ని బ్యాకప్‌లు :disk_usage స్థలాన్ని ఉపయోగిస్తున్నాయి, అది :disk_limit అనుమతి పరిమితి కంటే ఎక్కువ.',

    'no_backups_info' => 'ఇంకా ఎటువంటి బ్యాకప్‌లు సృష్టించబడలేదు',
    'application_name' => 'అప్లికేషన్ పేరు',
    'backup_name' => 'బ్యాకప్ పేరు',
    'disk' => 'డిస్క్',
    'newest_backup_size' => 'కొత్త బ్యాకప్ పరిమాణం',
    'number_of_backups' => 'బ్యాకప్‌ల సంఖ్య',
    'total_storage_used' => 'ఉపయోగించిన మొత్తం నిల్వ',
    'newest_backup_date' => 'కొత్త బ్యాకప్ తేదీ',
    'oldest_backup_date' => 'పాత బ్యాకప్ తేదీ',
];
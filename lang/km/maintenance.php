<?php

return [
    // Common Buttons
    'submit' => 'ដាក់ស្នើ',
    'accept' => 'ទទួលយក',
    'decline' => 'បដិសេធ',
    'save' => 'រក្សាទុក',
    'cancel' => 'បោះបង់',
    'edit' => 'កែប្រែ',
    'delete' => 'លុប',

    // Status Terms
    'status' => [
        'pending' => 'កំពុងរង់ចាំ',
        'in_progress' => 'កំពុងដំណើរការ',
        'completed' => 'បានបញ្ចប់',
        'rejected' => 'បានបដិសេធ'
    ],

    // Priority Levels
    'priority' => [
        'label' => 'អាទិភាព',
        'low' => 'អាទិភាពទាប',
        'medium' => 'អាទិភាពមធ្យម',
        'high' => 'អាទិភាពខ្ពស់',
        'urgent' => 'បន្ទាន់'
    ],

    // Form Labels
    'form' => [
        'title' => 'ចំណងជើង',
        'description' => 'ការពិពណ៌នា',
        'status' => 'ស្ថានភាព',
        'date' => 'កាលបរិច្ឆេទ',
        'property' => 'អចលនទ្រព្យ',
        'room' => 'បន្ទប់',
        'photos' => 'រូបថត',
        'upload_photos' => 'បញ្ចូលរូបថត',
        'add_note' => 'បន្ថែមកំណត់ចំណាំ',
        'landlord_notes' => 'កំណត់ចំណាំម្ចាស់ផ្ទះ'
    ],

    // Page Titles
    'titles' => [
        'maintenance_request' => 'សំណើថែទាំ',
        'new_request' => 'សំណើថែទាំថ្មី',
        'edit_request' => 'កែប្រែសំណើថែទាំ',
        'request_details' => 'ព័ត៌មានលម្អិតនៃសំណើ'
    ],

    // Messages
    'messages' => [
        'request_submitted' => 'សំណើថែទាំត្រូវបានដាក់ជូនដោយជោគជ័យ',
        'request_updated' => 'សំណើថែទាំត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ',
        'request_deleted' => 'សំណើថែទាំត្រូវបានលុបដោយជោគជ័យ',
        'status_updated' => 'ស្ថានភាពត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ',
        'photo_uploaded' => 'រូបថតត្រូវបានបញ្ចូលដោយជោគជ័យ',
        'note_added' => 'កំណត់ចំណាំត្រូវបានបន្ថែមដោយជោគជ័យ'
    ],

    // Errors
    'errors' => [
        'required_fields' => 'សូមបំពេញគ្រប់កន្លែងដែលត្រូវការ',
        'invalid_status' => 'ស្ថានភាពដែលបានជ្រើសរើសមិនត្រឹមត្រូវ',
        'invalid_priority' => 'អាទិភាពដែលបានជ្រើសរើសមិនត្រឹមត្រូវ',
        'photo_upload_failed' => 'ការបញ្ចូលរូបថតបានបរាជ័យ',
        'not_authorized' => 'អ្នកមិនមានសិទ្ធិធ្វើសកម្មភាពនេះទេ'
    ],

    // Dashboard
    'dashboard' => [
        'total_requests' => 'សំណើសរុប',
        'pending_requests' => 'សំណើកំពុងរង់ចាំ',
        'completed_requests' => 'សំណើបានបញ្ចប់',
        'urgent_requests' => 'សំណើបន្ទាន់',
        'recent_requests' => 'សំណើថ្មីៗ',
        'average_completion_time' => 'រយៈពេលបញ្ចប់ជាមធ្យម'
    ]
]; 
<?php

return [
    // Common Buttons
    'submit' => 'Submit',
    'accept' => 'Accept',
    'decline' => 'Decline',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'edit' => 'Edit',
    'delete' => 'Delete',

    // Status Terms
    'status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'rejected' => 'Rejected'
    ],

    // Priority Levels
    'priority' => [
        'label' => 'Priority',
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ],

    // Form Labels
    'form' => [
        'title' => 'Title',
        'description' => 'Description',
        'status' => 'Status',
        'date' => 'Date',
        'property' => 'Property',
        'room' => 'Unit',
        'photos' => 'Photos',
        'upload_photos' => 'Upload Photos',
        'add_note' => 'Add Note',
        'landlord_notes' => 'Landlord Notes'
    ],

    // Page Titles
    'titles' => [
        'maintenance_request' => 'Maintenance Request',
        'maintenance_requests' => 'Maintenance Requests',
        'new_request' => 'New Maintenance Request',
        'edit_request' => 'Edit Maintenance Request',
        'request_details' => 'Request Details'
    ],

    // Descriptions
    'descriptions' => [
        'landlord_list' => 'View and manage maintenance requests for your properties.',
        'tenant_list' => 'Submit and track maintenance requests for your units.',
        'manage_request' => 'Update the status and add notes for this maintenance request.',
    ],

    // Filters
    'filters' => [
        'search_requests' => 'Search requests...',
        'all_statuses' => 'All Statuses',
        'all_priorities' => 'All Priorities',
    ],

    // Table Columns
    'columns' => [
        'title' => 'Title',
        'property' => 'Property',
        'unit' => 'Unit',
        'priority' => 'Priority',
        'status' => 'Status',
        'date' => 'Date',
        'actions' => 'Actions',
    ],

    // Actions
    'actions' => [
        'new_request' => 'New Request',
        'accept' => 'Accept',
        'reject' => 'Reject',
        'complete' => 'Complete',
        'manage' => 'Manage',
        'view' => 'View',
        'view_details' => 'View Details',
        'edit' => 'Edit',
        'accept_request' => 'Accept Request',
        'reject_request' => 'Reject Request',
        'update_status' => 'Update Status',
        'back_to_list' => 'Back to List',
        'save_changes' => 'Save Changes',
    ],

    // Sections
    'sections' => [
        'property_details' => 'Property Details',
        'request_information' => 'Request Information',
        'quick_actions' => 'Quick Actions',
        'notes' => 'Notes',
        'submitted' => 'Submitted',
    ],

    // Select Options
    'select' => [
        'select_property' => '-- Select Property --',
        'select_room' => '-- Select Room --',
        'room_label' => 'Room',
    ],

    // Placeholders
    'placeholders' => [
        'title' => 'Enter a brief title for your maintenance request',
        'description' => 'Describe the maintenance issue in detail',
        'request_notes' => 'Add your notes about this maintenance request',
        'maintenance_work_notes' => 'Add any notes about the maintenance work...',
    ],

    // Photo Labels
    'photos' => [
        'upload_after' => 'Upload photos after maintenance work',
        'upload_issue' => 'Upload photos of the maintenance issue',
        'maintenance_photo' => 'Maintenance Photo',
        'temporary_photo' => 'Temporary Photo',
        'image_not_found' => 'Image not found',
        'photo' => 'photo',
        'by' => 'By',
        'type' => [
            'before' => 'Before',
            'after' => 'After',
        ],
        'uploaded_by' => [
            'tenant' => 'Tenant',
            'landlord' => 'Landlord',
        ],
    ],

    // Auto Notes
    'note_templates' => [
        'accepted_on' => 'Request accepted on :datetime.',
        'rejected_on' => 'Request rejected on :datetime.',
        'completed_on' => 'Request marked as completed on :datetime.',
    ],

    // Messages
    'messages' => [
        'request_submitted' => 'Maintenance request submitted successfully',
        'request_updated' => 'Maintenance request updated successfully',
        'request_deleted' => 'Maintenance request deleted successfully',
        'status_updated' => 'Maintenance request status updated successfully.',
        'photo_uploaded' => 'Photo uploaded successfully',
        'note_added' => 'Note added successfully',
        'request_accepted' => 'Maintenance request accepted successfully.',
        'request_rejected' => 'Maintenance request rejected.',
        'request_completed' => 'Maintenance request marked as completed.',
        'request_created' => 'Maintenance request created successfully.',
        'no_requests_found' => 'No maintenance requests found.',
    ],

    // Errors
    'errors' => [
        'required_fields' => 'Please fill in all required fields',
        'invalid_status' => 'Invalid status selected',
        'invalid_priority' => 'Invalid priority selected',
        'photo_upload_failed' => 'Failed to upload photo',
        'not_authorized' => 'You are not authorized to perform this action',
        'landlord_cannot_create' => 'Landlords cannot create maintenance requests.',
        'request_not_found' => 'Maintenance request not found.',
        'cannot_edit_request' => 'You cannot edit this maintenance request.',
    ],

    // Dashboard
    'dashboard' => [
        'total_requests' => 'Total Requests',
        'pending_requests' => 'Pending Requests',
        'completed_requests' => 'Completed Requests',
        'urgent_requests' => 'Urgent Requests',
        'recent_requests' => 'Recent Requests',
        'average_completion_time' => 'Average Completion Time'
    ]
];

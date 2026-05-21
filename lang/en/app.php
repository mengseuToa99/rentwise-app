<?php

return [
    // Navigation
    'dashboard' => 'Dashboard',
    'properties' => 'Properties',
    'maintenance' => 'Maintenance',
    'chat' => 'Chat',
    'profile' => 'Profile',
    'logout' => 'Log Out',

    // User Management
    'users' => 'Users',
    'roles' => 'Roles',
    'permissions' => 'Permissions',

    // Property Management
    'property_management' => 'Property Management',
    'units' => 'Units',
    'rentals' => 'Rentals',
    'tenant_info' => 'Tenant Info',

    // Finance
    'finance' => 'Finance',
    'invoices' => 'Invoices',
    'utilities' => 'Utilities',
    'utility_usage' => 'Utility Usage',

    // Tenant Section
    'my_rentals' => 'My Rentals',
    'my_invoices' => 'My Invoices',
    'my_property' => 'My Property',

    // System
    'system' => 'System',
    'system_settings' => 'System Settings',
    'system_logs' => 'System Logs',
    'platform' => 'Platform',

    // Common Actions
    'create' => 'Create',
    'edit' => 'Edit',
    'update' => 'Update',
    'delete' => 'Delete',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'confirm' => 'Confirm',

    // Messages
    'success' => 'Success',
    'error' => 'Error',
    'warning' => 'Warning',
    'info' => 'Information',
    'loading' => 'Loading...',
    'no_results' => 'No results found',
    'are_you_sure' => 'Are you sure?',
    'changes_saved' => 'Changes saved successfully',

    // Auth
    'login' => 'Login',
    'register' => 'Register',
    'forgot_password' => 'Forgot Password',
    'remember_me' => 'Remember Me',
    'password' => 'Password',
    'email' => 'Email',

    // UI Controls
    'theme_toggle' => 'Toggle theme',
    'switch_to_khmer' => 'Switch to Khmer',
    'switch_to_english' => 'Switch to English',
    'english' => 'EN',
    'khmer' => 'ខ្មែរ',

    // Simple Mode
    'simple_mode' => [
        'title' => 'Simple Mode',
        'subtitle' => 'Pick what you want to do.',
        'exit' => 'Exit',
        'back' => 'Back',
        'saving' => 'Saving…',
        'next' => 'Next →',
        'prev' => '← Back',

        // Home sections
        'section_make_invoice' => 'Make Invoice',
        'section_property_setup' => 'Property Setup',
        'section_daily_tasks' => 'Daily Tasks',

        // Home tiles
        'new_invoice' => 'New Invoice',
        'new_invoice_desc' => 'One room, fast.',
        'many_rooms' => 'Many Rooms',
        'many_rooms_desc' => 'Pick 3, 10, or 20 rooms at once.',
        'add_room' => 'Add Room',
        'add_room_desc' => 'New unit in a property.',
        'add_tenant' => 'Add Tenant',
        'add_tenant_desc' => 'Move tenant into a room.',
        'meter_readings' => 'Meter Readings',
        'meter_readings_desc' => 'View or export usage history.',
        'check_invoices' => 'Check Invoices',
        'check_invoices_desc' => 'See pending and paid invoices.',
        'full_menu_hint' => 'Need the full menu? Tap :exit at the top.',

        // Tenant tiles
        'section_for_you' => 'For You',
        'tile_my_invoices' => 'My Invoices',
        'tile_my_invoices_desc' => 'View and pay your invoices.',
        'tile_my_property' => 'My Property',
        'tile_my_property_desc' => 'See your unit and lease info.',
        'tile_maintenance' => 'Maintenance',
        'tile_maintenance_desc' => 'Report or track a repair.',
        'tile_chat' => 'Chat',
        'tile_chat_desc' => 'Message your landlord.',

        // Add room
        'which_property' => 'Which property?',
        'pick_property' => 'Pick a property…',
        'floor' => 'Floor',
        'floor_n' => 'Floor :n',
        'room_number' => 'Room number',
        'room_number_placeholder' => 'e.g. 101',
        'room_type' => 'Room type',
        'studio' => 'Studio',
        'one_bedroom' => '1 Bedroom',
        'two_bedroom' => '2 Bedroom',
        'three_bedroom' => '3 Bedroom',
        'monthly_rent' => 'Monthly rent',
        'save_room' => 'Save Room',

        // Add tenant — steps
        'step_person' => 'Person',
        'step_room' => 'Room',
        'step_confirm' => 'Confirm',

        // Add tenant — search
        'find_tenant' => 'Find tenant',
        'find_tenant_placeholder' => 'Search by name, phone or email',
        'find_tenant_hint' => 'Type at least 2 letters to search.',
        'no_tenant_match' => 'No tenant matches ":term".',
        'tenant' => 'Tenant',
        'change' => 'Change',

        // Add tenant — room step
        'no_empty_rooms' => 'No empty rooms. Add a room first from Simple Mode home.',
        'rent' => 'Rent',
        'room' => 'Room',

        // Add tenant — confirm
        'start_date' => 'Start date',
        'move_tenant_in' => 'Move tenant in',
    ],

    // Quick Invoice (single room)
    'quick_invoice' => [
        'title' => 'New Invoice',
        'back' => 'Back',
        'who_for' => 'Who is this for?',
        'change' => 'Change',
        'recent' => 'Recent',
        'show_all_tenants' => 'Show all tenants',
        'search_placeholder' => 'Search name, room…',
        'no_tenants_found' => 'No tenants found.',
        'meter_readings' => 'Meter readings',
        'no_utilities' => 'No utilities set up for this property.',
        'last' => 'Last: :value',
        'rate' => 'Rate',
        'new_reading_placeholder' => 'New reading',
        'same_as_last' => 'Same as last (no usage)',
        'used_amount' => 'Used :amount :unit',
        'total' => 'Total',
        'create_invoice' => 'Create invoice',
        'saving' => 'Saving…',
        'decrease' => 'Decrease',
        'increase' => 'Increase',
    ],

    // Batch Invoices (many rooms)
    'batch_invoice' => [
        'title' => 'Batch invoices',
        'pick_rooms' => 'Pick the rooms you want to invoice',
        'enter_readings_count' => 'Enter readings · :count :unit',
        'rooms_one' => 'room',
        'rooms_many' => 'rooms',
        'step_select' => 'Select',
        'step_fill' => 'Fill & create',
        'search_placeholder' => 'Search tenant, property, or room…',
        'count_of_total' => ':shown of :total',
        'select_all' => 'Select all',
        'clear' => 'Clear',
        'no_tenants_match' => 'No tenants match.',
        'default_due' => 'Default due:',
        'plus_7d' => '+7d',
        'plus_15d' => '+15d',
        'month_end' => 'Month end',
        'applies_to_all' => 'Applies to all rooms',
        'col_tenant_room' => 'Tenant · Room',
        'col_due' => 'Due',
        'col_total' => 'Total',
        'prev' => 'prev :value',
        'remove_confirm' => 'Remove this room from the batch?',
        'remove' => 'Remove',
        'same_as_previous' => 'Same as previous (:value)',
        'ready_of' => ' of :total ready',
        'ready_of_short' => '/:total ready',
        'back' => 'Back',
        'create_n_invoices' => 'Create :count invoice|Create :count invoices',
        'create_label' => 'Create',
        'invoice_word' => 'invoice',
        'plural_s' => 's',
        'pick_property_first' => 'Pick a property',
        'pick_property_hint' => 'Choose which property to invoice rooms for.',
        'rooms_count' => ':count rooms',
        'change_property' => 'Change property',
        'creating' => 'Creating…',
        'selected_count' => ':count selected',
        'default_due_value' => 'Default due :date',
        'continue' => 'Continue',
        'back_header' => 'Back',
        'due_label' => 'Due',
    ],

    // Utility Usage History
    'utility_usage_history' => [
        'title' => 'Utility Usage History',
        'subtitle' => 'View and analyze utility consumption across your properties',
        'back' => '← Back',
        'property' => 'Property',
        'utility_type' => 'Utility Type',
        'year' => 'Year',
        'month' => 'Month',
        'all_properties' => 'All Properties',
        'all_utilities' => 'All Utilities',
        'col_date' => 'Date',
        'col_property' => 'Property',
        'col_room' => 'Room',
        'col_utility' => 'Utility',
        'col_previous_reading' => 'Previous Reading',
        'col_new_reading' => 'New Reading',
        'col_usage' => 'Usage',
        'col_charge' => 'Charge',
        'room_n' => 'Room :n',
        'n_units' => ':n units',
        'no_records' => 'No utility usage records found for the selected filters.',
    ],
];

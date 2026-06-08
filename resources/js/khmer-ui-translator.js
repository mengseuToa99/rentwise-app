const htmlLang = document?.documentElement?.lang || 'en';
const appLocale = window.__APP_LOCALE__ || htmlLang || 'en';

if (!String(appLocale).toLowerCase().startsWith('km')) {
    // English mode: keep original UI.
} else {
    const phraseMap = {
        'Admin Dashboard': 'ផ្ទាំងគ្រប់គ្រងអ្នកគ្រប់គ្រង',
        'User Management': 'ការគ្រប់គ្រងអ្នកប្រើប្រាស់',
        'Add New User': 'បន្ថែមអ្នកប្រើថ្មី',
        'Search users...': 'ស្វែងរកអ្នកប្រើប្រាស់...',
        'Add Property': 'បន្ថែមអចលនទ្រព្យ',
        'My Properties': 'អចលនទ្រព្យរបស់ខ្ញុំ',
        'Search properties...': 'ស្វែងរកអចលនទ្រព្យ...',
        'No properties found': 'រកមិនឃើញអចលនទ្រព្យ',
        'Create your first property': 'បង្កើតអចលនទ្រព្យដំបូងរបស់អ្នក',
        'Property Stats': 'ស្ថិតិអចលនទ្រព្យ',
        'View Details': 'មើលលម្អិត',
        'Create New User': 'បង្កើតអ្នកប្រើថ្មី',
        'Edit User': 'កែប្រែអ្នកប្រើ',
        'Add a new user to the system': 'បន្ថែមអ្នកប្រើថ្មីទៅប្រព័ន្ធ',
        'Update user information': 'ធ្វើបច្ចុប្បន្នភាពព័ត៌មានអ្នកប្រើ',
        'Personal Information': 'ព័ត៌មានផ្ទាល់ខ្លួន',
        'Account Information': 'ព័ត៌មានគណនី',
        'Account Status': 'ស្ថានភាពគណនី',
        'Email Address': 'អាសយដ្ឋានអ៊ីមែល',
        'Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one special character.': 'ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច ៨ តួអក្សរ និងមានអក្សរធំ លេខ និងសញ្ញាពិសេសយ៉ាងតិចមួយ។',
        'Are you sure you want to delete this user?': 'តើអ្នកប្រាកដថាចង់លុបអ្នកប្រើនេះមែនទេ?',
        'No users found. Try a different search or add a new user.': 'រកមិនឃើញអ្នកប្រើ។ សូមសាកស្វែងរកផ្សេង ឬបន្ថែមអ្នកប្រើថ្មី។',
        'This Week': 'សប្ដាហ៍នេះ',
        'This Month': 'ខែនេះ',
        'This Year': 'ឆ្នាំនេះ',
        'Current Password': 'ពាក្យសម្ងាត់បច្ចុប្បន្ន',
        'New Password': 'ពាក្យសម្ងាត់ថ្មី',
        'Confirm New Password': 'បញ្ជាក់ពាក្យសម្ងាត់ថ្មី',
        'Save Changes': 'រក្សាទុកការផ្លាស់ប្តូរ',
        'No roles assigned': 'មិនមានតួនាទីត្រូវបានកំណត់',
        'Back to Login': 'ត្រឡប់ទៅចូលគណនី',
        'Reset Password': 'កំណត់ពាក្យសម្ងាត់ឡើងវិញ',
        'Create account': 'បង្កើតគណនី',
        'Create an account': 'បង្កើតគណនី',
        'Already have an account?': 'មានគណនីរួចហើយ?',
        'Forgot password': 'ភ្លេចពាក្យសម្ងាត់',
        'Log out': 'ចាកចេញ',
        'Log Out': 'ចាកចេញ',
        'Search': 'ស្វែងរក',
        'Settings': 'ការកំណត់',
        'Appearance': 'រូបរាង',
        'Light': 'ភ្លឺ',
        'Dark': 'ងងឹត',
        'System': 'ប្រព័ន្ធ',
        'Update password': 'ធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់',
        'Update your name and email address': 'ធ្វើបច្ចុប្បន្នភាពឈ្មោះ និងអាសយដ្ឋានអ៊ីមែលរបស់អ្នក',
        'Delete account': 'លុបគណនី',
        'Accept Request': 'ទទួលយកសំណើ',
        'Decline Request': 'បដិសេធសំណើ',
        'No data available': 'មិនមានទិន្នន័យ',
        'No results found': 'មិនមានលទ្ធផល',
        'All Statuses': 'ស្ថានភាពទាំងអស់',
        'All Priorities': 'អាទិភាពទាំងអស់',
        'All Properties': 'អចលនទ្រព្យទាំងអស់',
        'All Units': 'បន្ទប់ទាំងអស់',
        'All Utilities': 'សេវាកម្មទាំងអស់',
        'Search requests...': 'ស្វែងរកសំណើ...',
        'Search properties...': 'ស្វែងរកអចលនទ្រព្យ...',
        'Search rentals...': 'ស្វែងរកការជួល...',
        'Search invoices...': 'ស្វែងរកវិក្កយបត្រ...',
        'Search tenants...': 'ស្វែងរកអ្នកជួល...',
        'Search users...': 'ស្វែងរកអ្នកប្រើប្រាស់...',
        'Search utilities...': 'ស្វែងរកសេវាកម្ម...',
        'Search roles...': 'ស្វែងរកតួនាទី...',
        'Search logs...': 'ស្វែងរកកំណត់ហេតុ...',
        'Search by name, number...': 'ស្វែងរកតាមឈ្មោះ លេខ...',
        'Search pricing groups...': 'ស្វែងរកក្រុមតម្លៃ...',
        'Search by name or description...': 'ស្វែងរកតាមឈ្មោះ ឬការពិពណ៌នា...',
        'No utilities found': 'មិនមានសេវាកម្មទេ',
        'No utility usage records found for the selected filters.': 'មិនមានកំណត់ត្រាប្រើប្រាស់សេវាកម្មសម្រាប់តម្រងដែលបានជ្រើសទេ។',
        'No rental history found.': 'មិនមានប្រវត្តិជួលទេ។',
        'No invoice history found.': 'មិនមានប្រវត្តិវិក្កយបត្រទេ។',
        'No payment history found.': 'មិនមានប្រវត្តិបង់ប្រាក់ទេ។',
        'No tenants found.': 'មិនមានអ្នកជួលទេ។',
        'No active lease found': 'មិនមានកិច្ចសន្យាជួលសកម្មទេ',
        'No properties found': 'មិនមានអចលនទ្រព្យទេ',
        'No units found': 'មិនមានបន្ទប់ទេ',
        'No units found for this property': 'មិនមានបន្ទប់សម្រាប់អចលនទ្រព្យនេះទេ',
        'No roles found. Try a different search or add a new role.': 'មិនមានតួនាទីទេ។ សូមសាកស្វែងរកផ្សេង ឬបន្ថែមតួនាទីថ្មី។',
        'No permissions found. Try a different search or create a new permission.': 'មិនមានសិទ្ធិទេ។ សូមសាកស្វែងរកផ្សេង ឬបង្កើតសិទ្ធិថ្មី។',
        'No logs found': 'មិនមានកំណត់ហេតុទេ',
        'No lease agreements found.': 'មិនមានកិច្ចព្រមព្រៀងជួលទេ។',
        'No rentals found': 'មិនមានការជួលទេ',
        'No invoices found': 'មិនមានវិក្កយបត្រទេ',
        'No invoices found matching your criteria.': 'មិនមានវិក្កយបត្រដែលត្រូវនឹងលក្ខខណ្ឌរបស់អ្នកទេ។',
        'No pricing groups found for this property.': 'មិនមានក្រុមតម្លៃសម្រាប់អចលនទ្រព្យនេះទេ។',
        'View and manage maintenance requests for your properties.': 'មើល និងគ្រប់គ្រងសំណើថែទាំសម្រាប់អចលនទ្រព្យរបស់អ្នក។',
        'Submit and track maintenance requests for your units.': 'ដាក់ស្នើ និងតាមដានសំណើថែទាំសម្រាប់បន្ទប់របស់អ្នក។',
        'Maintenance Requests': 'សំណើថែទាំ',

        // Units list page
        'Manage all your rental units': 'គ្រប់គ្រងបន្ទប់ជួលរបស់អ្នកទាំងអស់',
        'Add Unit': 'បន្ថែមបន្ទប់',
        'Availability': 'ភាពទំនេរ',
        'All Units': 'បន្ទប់ទាំងអស់',
        'Rows per page:': 'ជួរក្នុងមួយទំព័រ៖',
        '10 per page': '១០ ក្នុងមួយទំព័រ',
        '25 per page': '២៥ ក្នុងមួយទំព័រ',
        '50 per page': '៥០ ក្នុងមួយទំព័រ',
        '100 per page': '១០០ ក្នុងមួយទំព័រ',
        'Show All': 'បង្ហាញទាំងអស់',
        'Room #': 'បន្ទប់លេខ',
        'Add your first unit to start managing your rental inventory.': 'បន្ថែមបន្ទប់ដំបូងរបស់អ្នកដើម្បីចាប់ផ្តើមគ្រប់គ្រងបញ្ជីបន្ទប់ជួលរបស់អ្នក។',
        'Create your first unit': 'បង្កើតបន្ទប់ដំបូងរបស់អ្នក',
        'Delete unit': 'លុបបន្ទប់',
        'Are you sure you want to delete this unit? This action cannot be undone.': 'តើអ្នកប្រាកដថាចង់លុបបន្ទប់នេះមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ។',
        'Are you sure you want to delete this unit?': 'តើអ្នកប្រាកដថាចង់លុបបន្ទប់នេះមែនទេ?',

        // Units create page
        'Add New Unit': 'បន្ថែមបន្ទប់ថ្មី',
        'Back to Units': 'ត្រឡប់ទៅបន្ទប់',
        'Back to Property': 'ត្រឡប់ទៅអចលនទ្រព្យ',
        'Property Information': 'ព័ត៌មានអចលនទ្រព្យ',
        'Select a property': 'ជ្រើសរើសអចលនទ្រព្យ',
        'Pricing Group': 'ក្រុមតម្លៃ',
        'Select a pricing group': 'ជ្រើសរើសក្រុមតម្លៃ',
        'Selecting a pricing group will automatically fill the room type and rent amount.': 'ការជ្រើសរើសក្រុមតម្លៃនឹងបំពេញប្រភេទបន្ទប់ និងថ្លៃជួលដោយស្វ័យប្រវត្តិ។',
        'Unit Details': 'ព័ត៌មានលម្អិតបន្ទប់',
        'Unit/Room Name': 'ឈ្មោះបន្ទប់',
        'Unit/Room Number': 'លេខបន្ទប់',
        'Floor Number': 'លេខជាន់',
        'Unit Type': 'ប្រភេទបន្ទប់',
        'Select a type': 'ជ្រើសរើសប្រភេទ',
        'Single Room': 'បន្ទប់ធម្មតា',
        '1 Bedroom': 'បន្ទប់គេង ១',
        '2 Bedroom': 'បន្ទប់គេង ២',
        '3 Bedroom': 'បន្ទប់គេង ៣',
        '4+ Bedroom': 'បន្ទប់គេង ៤+',
        'Unit Size': 'ទំហំបន្ទប់',
        'Enter size in square feet or square meters': 'បញ្ចូលទំហំជាហ្វីតការ៉េ ឬម៉ែត្រការ៉េ',
        'Unit Description': 'ការពិពណ៌នាបន្ទប់',
        'Enter details about this unit, amenities, features, etc.': 'បញ្ចូលព័ត៌មានលម្អិតអំពីបន្ទប់នេះ គ្រឿងបរិក្ខារ លក្ខណៈពិសេស ។ល។',
        'e.g. Master Bedroom, Studio A': 'ឧ. បន្ទប់គេងធំ, ស្ទូឌីយ៉ូ A',
        'e.g. 101, A1, 202B': 'ឧ. ១០១, A1, 202B',
        'e.g. 500 sq ft': 'ឧ. 500 ហ្វីតការ៉េ',
        'Rental Terms': 'លក្ខខណ្ឌជួល',
        'Monthly Rent': 'ថ្លៃជួលប្រចាំខែ',
        'Payment Due Date': 'កាលបរិច្ឆេទត្រូវបង់',
        'The day of the month when rent payment is due': 'ថ្ងៃនៃខែដែលត្រូវបង់ថ្លៃជួល',
        'Available for rent': 'អាចជួលបាន',
        'If checked, this unit will be listed as available for new tenants': 'ប្រសិនបើធីក បន្ទប់នេះនឹងត្រូវបង្ហាញថាអាចជួលបានសម្រាប់អ្នកជួលថ្មី',

        // Property detail page
        'Edit Property': 'កែប្រែអចលនទ្រព្យ',
        'Current property status': 'ស្ថានភាពអចលនទ្រព្យបច្ចុប្បន្ន',
        'Occupancy Rate': 'អត្រាការស្នាក់នៅ',
        'Property Size': 'ទំហំអចលនទ្រព្យ',
        'Total floors in the building': 'ចំនួនជាន់សរុបក្នុងអគារ',
        'No description provided': 'មិនមានការពិពណ៌នា',
        'Quick Info': 'ព័ត៌មានរហ័ស',
        'Property Type': 'ប្រភេទអចលនទ្រព្យ',
        'Year Built': 'ឆ្នាំសាងសង់',
        'Address Details': 'ព័ត៌មានលម្អិតអាសយដ្ឋាន',
        'House/Building Number': 'ផ្ទះលេខ/អគារលេខ',
        'Commune/Sangkat': 'សង្កាត់/ឃុំ',
        'District/Khan': 'ខណ្ឌ/ស្រុក',
        'Pricing Groups': 'ក្រុមតម្លៃ',
        'Property Not Found': 'រកមិនឃើញអចលនទ្រព្យ',
        "The property you're looking for may not exist or you don't have permission to view it.": 'អចលនទ្រព្យដែលអ្នកកំពុងស្វែងរកប្រហែលជាមិនមាន ឬអ្នកគ្មានសិទ្ធិមើលវាទេ។',
        'Back to Properties': 'ត្រឡប់ទៅអចលនទ្រព្យ',
        'Add your first unit': 'បន្ថែមបន្ទប់ដំបូងរបស់អ្នក'
    };

    const wordMap = {
        add: 'បន្ថែម',
        new: 'ថ្មី',
        create: 'បង្កើត',
        edit: 'កែប្រែ',
        delete: 'លុប',
        update: 'ធ្វើបច្ចុប្បន្នភាព',
        save: 'រក្សាទុក',
        cancel: 'បោះបង់',
        confirm: 'បញ្ជាក់',
        back: 'ត្រឡប់',
        dashboard: 'ផ្ទាំងគ្រប់គ្រង',
        admin: 'អ្នកគ្រប់គ្រង',
        landlord: 'ម្ចាស់ផ្ទះ',
        tenant: 'អ្នកជួល',
        tenants: 'អ្នកជួល',
        user: 'អ្នកប្រើ',
        users: 'អ្នកប្រើប្រាស់',
        management: 'ការគ្រប់គ្រង',
        role: 'តួនាទី',
        roles: 'តួនាទី',
        permission: 'សិទ្ធិ',
        permissions: 'សិទ្ធិ',
        property: 'អចលនទ្រព្យ',
        properties: 'អចលនទ្រព្យ',
        unit: 'បន្ទប់',
        units: 'បន្ទប់',
        rental: 'ការជួល',
        rentals: 'ការជួល',
        maintenance: 'ការថែទាំ',
        request: 'សំណើ',
        requests: 'សំណើ',
        invoice: 'វិក្កយបត្រ',
        invoices: 'វិក្កយបត្រ',
        utility: 'សេវាកម្ម',
        utilities: 'សេវាកម្ម',
        finance: 'ហិរញ្ញវត្ថុ',
        amount: 'ចំនួន',
        due: 'ដល់កំណត់',
        paid: 'បានបង់',
        payment: 'ការទូទាត់',
        payments: 'ការទូទាត់',
        status: 'ស្ថានភាព',
        active: 'សកម្ម',
        inactive: 'អសកម្ម',
        suspended: 'ផ្អាក',
        pending: 'កំពុងរង់ចាំ',
        approved: 'បានអនុម័ត',
        rejected: 'បានបដិសេធ',
        completed: 'បានបញ្ចប់',
        progress: 'ដំណើរការ',
        high: 'ខ្ពស់',
        medium: 'មធ្យម',
        low: 'ទាប',
        urgent: 'បន្ទាន់',
        name: 'ឈ្មោះ',
        first: 'នាមខ្លួន',
        last: 'នាមត្រកូល',
        username: 'ឈ្មោះអ្នកប្រើ',
        email: 'អ៊ីមែល',
        phone: 'ទូរស័ព្ទ',
        number: 'លេខ',
        password: 'ពាក្យសម្ងាត់',
        address: 'អាសយដ្ឋាន',
        city: 'ទីក្រុង',
        district: 'ខណ្ឌ',
        commune: 'សង្កាត់',
        room: 'បន្ទប់',
        title: 'ចំណងជើង',
        description: 'ការពិពណ៌នា',
        priority: 'អាទិភាព',
        date: 'កាលបរិច្ឆេទ',
        start: 'ចាប់ផ្តើម',
        end: 'បញ្ចប់',
        total: 'សរុប',
        available: 'ទំនេរ',
        occupied: 'មានអ្នកស្នាក់នៅ',
        vacant: 'ទំនេរ',
        action: 'សកម្មភាព',
        actions: 'សកម្មភាព',
        search: 'ស្វែងរក',
        filter: 'តម្រង',
        all: 'ទាំងអស់',
        group: 'ក្រុម',
        groups: 'ក្រុម',
        chat: 'ជជែក',
        profile: 'ប្រវត្តិរូប',
        settings: 'ការកំណត់',
        system: 'ប្រព័ន្ធ',
        platform: 'វេទិកា',
        theme: 'រូបរាង',
        light: 'ភ្លឺ',
        dark: 'ងងឹត',
        export: 'នាំចេញ',
        logs: 'កំណត់ហេតុ',
        history: 'ប្រវត្តិ',
        usage: 'ការប្រើប្រាស់',
        note: 'កំណត់ចំណាំ',
        notes: 'កំណត់ចំណាំ',
        submit: 'ដាក់ស្នើ',
        send: 'ផ្ញើ',
        reset: 'កំណត់ឡើងវិញ',
        login: 'ចូល',
        logout: 'ចាកចេញ',
        register: 'ចុះឈ្មោះ',
        account: 'គណនី',
        information: 'ព័ត៌មាន',
        current: 'បច្ចុប្បន្ន',
        month: 'ខែ',
        year: 'ឆ្នាំ',
        week: 'សប្ដាហ៍',
        today: 'ថ្ងៃនេះ',
        this: 'នេះ',
        my: 'របស់ខ្ញុំ',
        view: 'មើល',
        details: 'លម្អិត',
        type: 'ប្រភេទ',
        size: 'ទំហំ',
        rent: 'ថ្លៃជួល',
        floor: 'ជាន់',
        floors: 'ជាន់',
        bedroom: 'បន្ទប់គេង',
        studio: 'ស្ទូឌីយ៉ូ',
        street: 'ផ្លូវ',
        village: 'ភូមិ',
        commune: 'សង្កាត់',
        built: 'សាងសង់',
        occupancy: 'ការស្នាក់នៅ',
        rate: 'អត្រា'
    };

    const translatableAttrs = ['placeholder', 'title', 'aria-label', 'alt', 'value', 'wire:confirm'];

    const canTranslateValueAttr = (el) => {
        const tag = (el.tagName || '').toLowerCase();
        // Do NOT translate <option> values — only their visible text is translated.
        // Translating the value would submit Khmer text and corrupt form data / wire:model.
        if (tag !== 'input') return false;
        const type = (el.getAttribute('type') || '').toLowerCase();
        return ['button', 'submit', 'reset'].includes(type);
    };

    const normalize = (value) => value.replace(/\s+/g, ' ').trim();

    const translateWords = (text) => {
        return text.replace(/\b([A-Za-z][A-Za-z'-]*)\b/g, (match) => {
            const translated = wordMap[match.toLowerCase()];
            return translated || match;
        });
    };

    const translateText = (text) => {
        if (!text || !/[A-Za-z]/.test(text)) return text;

        const normalized = normalize(text);
        if (phraseMap[normalized]) {
            const leading = text.match(/^\s*/)?.[0] || '';
            const trailing = text.match(/\s*$/)?.[0] || '';
            return `${leading}${phraseMap[normalized]}${trailing}`;
        }

        let translated = text;

        const partialPhrases = Object.keys(phraseMap).sort((a, b) => b.length - a.length);
        for (const phrase of partialPhrases) {
            if (translated.includes(phrase)) {
                translated = translated.split(phrase).join(phraseMap[phrase]);
            }
        }

        return translateWords(translated);
    };

    const shouldSkipNode = (node) => {
        if (!node || !node.parentElement) return true;
        const tag = node.parentElement.tagName;
        return tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT' || tag === 'CODE' || tag === 'PRE';
    };

    const translateTextNodes = (root = document.body) => {
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        let node;
        while ((node = walker.nextNode())) {
            if (shouldSkipNode(node)) continue;
            const original = node.nodeValue;
            const translated = translateText(original);
            if (translated !== original) {
                node.nodeValue = translated;
            }
        }
    };

    const translateAttributes = (root = document.body) => {
        const selector = translatableAttrs.map((attr) => `[${CSS.escape(attr)}]`).join(',');
        root.querySelectorAll(selector).forEach((el) => {
            translatableAttrs.forEach((attr) => {
                if (!el.hasAttribute(attr)) return;
                if (attr === 'value' && !canTranslateValueAttr(el)) return;
                const original = el.getAttribute(attr);
                const translated = translateText(original || '');
                if (translated !== original) {
                    el.setAttribute(attr, translated);
                }
            });
        });
    };

    const translateUi = (root = document.body) => {
        translateTextNodes(root);
        translateAttributes(root);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => translateUi());
    } else {
        translateUi();
    }

    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        translateUi(node);
                    } else if (node.nodeType === Node.TEXT_NODE) {
                        const original = node.nodeValue;
                        const translated = translateText(original);
                        if (translated !== original) {
                            node.nodeValue = translated;
                        }
                    }
                });
            }

            if (mutation.type === 'attributes' && mutation.target instanceof Element) {
                const attr = mutation.attributeName;
                if (attr && translatableAttrs.includes(attr)) {
                    if (attr === 'value' && !canTranslateValueAttr(mutation.target)) continue;
                    const original = mutation.target.getAttribute(attr) || '';
                    const translated = translateText(original);
                    if (translated !== original) {
                        mutation.target.setAttribute(attr, translated);
                    }
                }
            }
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: translatableAttrs
    });
}

# Rentwise App UI Components

This README provides instructions on how to set up and use the shadcn-like UI components in the Rentwise-app Laravel project with Livewire and Tailwind CSS.

## Setup

1. Make sure you have Tailwind CSS installed and configured:

```bash
npm install -D tailwindcss postcss autoprefixer @tailwindcss/forms
npx tailwindcss init -p
```

2. Update your `tailwind.config.js` to include the necessary paths and configurations. The file is already configured in this project.

3. Make sure your CSS imports the Tailwind directives and custom CSS variables:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

4. Install dependencies:

```bash
composer require livewire/livewire
npm install
```

5. Compile assets:

```bash
npm run dev
```

## Component Architecture

The UI components follow a shadcn-like architecture, providing reusable Blade components that can be composed to build more complex interfaces. For a detailed explanation of the component structure, see [COMPONENT_STRUCTURE.md](./COMPONENT_STRUCTURE.md).

## Available Components

### Base UI Components

These are located in `resources/views/components/ui/`:

- `button.blade.php` - Button component with variants
- `card.blade.php` - Card container with header, body, and footer components
- `checkbox.blade.php` - Checkbox input
- `form-group.blade.php` - Form group for wrapping inputs with labels and error messages
- `input.blade.php` - Text input field
- `label.blade.php` - Form label
- `select.blade.php` - Select dropdown
- `textarea.blade.php` - Textarea input

### Usage Examples

#### Button Component

```blade
<x-ui.button type="submit">Save</x-ui.button>
<x-ui.button variant="outline">Cancel</x-ui.button>
<x-ui.button variant="destructive">Delete</x-ui.button>
<x-ui.button disabled>Disabled</x-ui.button>
<x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="lg">Large</x-ui.button>
```

#### Form Components

```blade
<form wire:submit="save">
    <x-ui.form-group label="Name" for="name" :error="$errors->first('name')">
        <x-ui.input wire:model="name" id="name" :error="$errors->has('name')" />
    </x-ui.form-group>
    
    <x-ui.form-group label="Description" for="description" :error="$errors->first('description')">
        <x-ui.textarea wire:model="description" id="description" :error="$errors->has('description')" rows="4" />
    </x-ui.form-group>
    
    <x-ui.form-group label="Category" for="category" :error="$errors->first('category')">
        <x-ui.select 
            wire:model="category" 
            id="category" 
            :error="$errors->has('category')"
            :options="[
                'residential' => 'Residential',
                'commercial' => 'Commercial',
                'industrial' => 'Industrial'
            ]"
            placeholder="Select a category"
        />
    </x-ui.form-group>
    
    <div class="flex items-center mb-4">
        <x-ui.checkbox wire:model="active" id="active" />
        <x-ui.label for="active" class="ml-2">Active</x-ui.label>
    </div>
    
    <x-ui.button type="submit">Save</x-ui.button>
</form>
```

#### Card Component

```blade
<x-ui.card>
    <x-ui.card-header>
        <x-ui.card-title>Properties</x-ui.card-title>
        <x-ui.card-description>Manage your properties.</x-ui.card-description>
    </x-ui.card-header>
    
    <x-ui.card-body>
        <!-- Card content here -->
    </x-ui.card-body>
    
    <x-ui.card-footer>
        <x-ui.button>Save</x-ui.button>
    </x-ui.card-footer>
</x-ui.card>
```

## Extending Components

To add new components to the library:

1. Create a new Blade component in `resources/views/components/ui/`
2. Define the appropriate props and slots
3. Implement the component with appropriate Tailwind CSS classes

## Best Practices

1. Use props for component configuration
2. Use slots for content flexibility
3. Use attribute merging for customization
4. Follow shadcn-like naming conventions
5. Group components by feature or page in Livewire components
6. Use `wire:model` for form inputs when used with Livewire
7. Use `wire:loading` for loading states
8. Handle validation errors appropriately

## Troubleshooting

If your components are not rendering correctly:

1. Make sure Tailwind CSS is compiled correctly
2. Check that component paths are correct
3. Verify that props are passed correctly
4. Clear Laravel view cache: `php artisan view:clear` 
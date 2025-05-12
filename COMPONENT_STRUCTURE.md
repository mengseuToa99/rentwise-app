# Rentwise-App Component Structure

This document outlines the component architecture for the Rentwise-App, which is built using Laravel with Livewire and Tailwind CSS.

## UI Component Architecture

The UI components are organized to provide a shadcn-like experience using Blade components and Livewire.

### Directory Structure

```
resources/
├── views/
│   ├── components/
│   │   ├── ui/               # Base UI components (shadcn-like)
│   │   │   ├── button.blade.php
│   │   │   ├── card.blade.php
│   │   │   ├── card-body.blade.php
│   │   │   ├── card-footer.blade.php
│   │   │   ├── card-header.blade.php
│   │   │   ├── card-title.blade.php
│   │   │   ├── card-description.blade.php
│   │   │   ├── form-group.blade.php
│   │   │   ├── input.blade.php
│   │   │   └── label.blade.php
│   │   ├── layouts/          # Layout templates
│   │   └── partials/         # Reusable page sections
│   └── livewire/             # Livewire components (organized by feature)
│       ├── auth/             # Authentication components
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── ...
│       ├── properties/       # Property management components
│       ├── units/            # Unit management components
│       ├── rentals/          # Rental management components
│       ├── invoices/         # Invoice management components
│       ├── settings/         # Settings components
│       └── ...
└── css/
    └── app.css              # CSS with shadcn-like variables
```

## Component Types

1. **UI Components**: Base components that replicate shadcn style and functionality
   - Located in `resources/views/components/ui/`
   - Examples: button, input, card, form elements

2. **Feature Components**: Livewire components organized by feature/domain
   - Located in `resources/views/livewire/[feature-name]/`
   - Examples: auth/login, properties/property-list

3. **Layout Components**: Reusable layout templates
   - Located in `resources/views/components/layouts/`
   - Examples: app, auth, dashboard

## Best Practices

1. **Component Composition**:
   - Build complex UIs by composing smaller, reusable components
   - Use slot functionality to make components flexible

2. **Props and Attributes**:
   - Use props for component configuration
   - Merge attributes to allow customization when needed

3. **Form Handling**:
   - Use Livewire's real-time validation
   - Group form elements using form-group components

4. **Styling**:
   - Use Tailwind CSS utility classes
   - Follow shadcn-like naming conventions
   - Leverage CSS variables for theming

5. **State Management**:
   - Use Livewire properties for component state
   - Emit events for cross-component communication

## Example Usage

```blade
<x-ui.card>
    <x-ui.card-header>
        <x-ui.card-title>Properties</x-ui.card-title>
        <x-ui.card-description>Manage your properties.</x-ui.card-description>
    </x-ui.card-header>
    
    <x-ui.card-body>
        <form wire:submit="save">
            <x-ui.form-group label="Property Name" for="name" :error="$errors->first('name')">
                <x-ui.input wire:model="name" id="name" :error="$errors->has('name')" />
            </x-ui.form-group>
            
            <x-ui.button type="submit">Save Property</x-ui.button>
        </form>
    </x-ui.card-body>
</x-ui.card>
```

## Expanding the Component Library

To add new components:

1. Create a new Blade component in `resources/views/components/ui/`
2. Define props and slots as needed
3. Implement the component with appropriate Tailwind CSS classes
4. Document the component usage 
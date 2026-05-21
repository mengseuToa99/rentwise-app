<?php

namespace App\Models;

/**
 * Backwards-compat alias for the polymorphic Image model.
 * The old `property_images` table was replaced with the polymorphic `images` table —
 * existing code that did `Property::propertyImages()` keeps working.
 */
class PropertyImage extends Image
{
}

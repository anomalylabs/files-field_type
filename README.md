# Files Field Type

*anomaly.field_type.files*

#### A multiple files upload field type.

The files field type provides a multiple file uploader input with relationship management.

## Features

- Multiple file uploads
- Integration with Files Module
- Relationship-based file management (many-to-many)
- Drag and drop file uploads
- File browsing and selection
- Support for various display modes
- No direct database column (uses pivot table)
- File metadata and preview support
- Configurable upload restrictions

## Configuration

### Basic Configuration

```php
protected $fields = [
    'attachments' => [
        'type' => 'anomaly.field_type.files'
    ]
];
```

### With Folder Restriction

```php
'documents' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'folders' => ['documents', 'uploads']
    ]
]
```

### With File Type Restrictions

```php
'images' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'extensions' => ['jpg', 'jpeg', 'png', 'gif']
    ]
]
```

### With Max Files Limit

```php
'gallery' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'max' => 10
    ]
]
```

### With Display Mode

```php
'attachments' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'mode' => 'compact' // or 'default'
    ]
]
```

## Usage Examples

### Basic Multiple File Upload

```php
$stream->create([
    'attachments' => [1, 2, 3] // File IDs
]);
```

### With File Upload in Form

```php
protected $fields = [
    'attachments' => [
        'type'  => 'anomaly.field_type.files',
        'rules' => [
            'required'
        ]
    ]
];
```

## Accessing Values

### In Twig Templates

```twig
{# Display all attached files #}
{% for file in entry.attachments %}
    <a href="{{ file.path }}">{{ file.name }}</a>
{% endfor %}

{# Check if files exist #}
{% if entry.attachments.count() > 0 %}
    <p>{{ entry.attachments.count() }} file(s) attached</p>
{% endif %}

{# Display image thumbnails #}
{% for file in entry.images %}
    <img src="{{ file.make().fit(200, 200).path() }}" alt="{{ file.name }}">
{% endfor %}

{# Get first file #}
{% if entry.attachments.first() %}
    {{ entry.attachments.first().name }}
{% endif %}
```

### In PHP

```php
$entry = $model->find(1);

// Get all files (Collection)
$files = $entry->attachments;

// Count files
$count = $entry->attachments->count();

// Loop through files
foreach ($entry->attachments as $file) {
    echo $file->name;
    echo $file->path();
    echo $file->size;
}

// Get file IDs
$fileIds = $entry->attachments->pluck('id')->toArray();

// Add files
$entry->attachments()->attach([4, 5, 6]);

// Remove files
$entry->attachments()->detach([1, 2]);

// Sync files (replace all)
$entry->attachments()->sync([7, 8, 9]);
```

## Setting Values

### In Forms

```php
$form = $builder->make('example.module.test');
$form->on('saving', function(FormBuilder $builder) {
    $entry = $builder->getFormEntry();
    
    // Set file IDs
    $entry->attachments = [1, 2, 3];
});
```

### Direct Assignment

```php
// Sync files (replaces all existing)
$entry->attachments()->sync([1, 2, 3]);

// Add files (keeps existing)
$entry->attachments()->attach([4, 5]);

// Remove files
$entry->attachments()->detach([1]);
```

## Database Structure

The files field type uses a pivot table:
- **Pivot Table**: `[stream]_attachments` (many-to-many relationship)
- **Columns**: 
  - `entry_id` - The parent entry ID
  - `related_id` - The file ID from files_files table
  - `sort_order` - Optional ordering

## Validation

### Required Files

```php
'attachments' => [
    'type'  => 'anomaly.field_type.files',
    'rules' => [
        'required'
    ]
]
```

### Minimum Files Required

```php
'gallery' => [
    'type'  => 'anomaly.field_type.files',
    'rules' => [
        'required',
        'min:3' // At least 3 files
    ]
]
```

### Maximum Files Limit

```php
'attachments' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'max' => 5
    ],
    'rules' => [
        'max:5'
    ]
]
```

## Common Use Cases

### Document Attachments

```php
'documents' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'folders'    => ['documents'],
        'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx']
    ]
]
```

### Image Gallery

```php
'gallery' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'folders'    => ['galleries'],
        'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'max'        => 20
    ]
]
```

### Product Images

```php
'product_images' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'folders'    => ['products'],
        'extensions' => ['jpg', 'jpeg', 'png'],
        'max'        => 10
    ],
    'rules' => [
        'required',
        'min:1'
    ]
]
```

### Media Downloads

```php
'downloads' => [
    'type'   => 'anomaly.field_type.files',
    'config' => [
        'folders'    => ['downloads'],
        'extensions' => ['pdf', 'zip', 'mp3', 'mp4']
    ]
]
```

## Best Practices

1. **Restrict Folders**: Limit file selection to specific folders for organization
2. **Validate Extensions**: Always restrict allowed file types for security
3. **Set Max Limits**: Prevent abuse by limiting maximum files
4. **Use Relationships**: Leverage the relationship methods (attach, detach, sync)
5. **Clean Up Orphans**: Implement cleanup for unused files
6. **Optimize Images**: Use image manipulation for thumbnails and previews
7. **Consider Storage**: Monitor disk space usage for large file collections

## Requirements

- Streams Platform ^1.10
- PyroCMS 3.10+
- Files Module

## License

The Files Field Type is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Authors

PyroCMS, Inc. - [https://pyrocms.com](https://pyrocms.com)
Ryan Thompson - [ryan@pyrocms.com](mailto:ryan@pyrocms.com)

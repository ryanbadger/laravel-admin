<?php

namespace RyanBadger\LaravelAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_name', 
        'file_path', 
        'type', 
        'size', 
        'order', 
        'mediaable_id', 
        'mediaable_type'
    ];

    public function mediaable()
    {
        return $this->morphTo();
    }

    /**
     * Get the fields for the CMS.
     */
    public function cmsFields()
    {
        return [
            'preview' => [
                'type' => 'media', // Custom field type, assume your CMS can handle rendering an image
                'label' => 'Preview',
                'editable' => false,
                'show_in_list' => true
            ],
            'file_name' => [
                'type' => 'text',
                'label' => 'File Name',
                'editable' => false, // Typically not editable once uploaded
                'show_in_list' => true
            ],
            'file_path' => [
                'type' => 'text',
                'label' => 'File Path',
                'editable' => false,
                'show_in_list' => true
            ],
            'type' => [
                'type' => 'text',
                'label' => 'Type',
                'editable' => false,
                'show_in_list' => true
            ],
            'size' => [
                'type' => 'number',
                'label' => 'Size (bytes)',
                'editable' => false,
                'show_in_list' => true
            ],
            'order' => [
                'type' => 'number',
                'label' => 'Order',
                'editable' => true,
                'show_in_list' => true
            ]
        ];
    }


    /**
     * Helper to get the URL for the media file preview.
     */
    public function getUrl()
    {
        return Storage::url($this->file_path);
    }

    public function isImage()
    {
        // Get the file extension
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        
        // List of common image extensions
        $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        
        // Check if the extension is in the list of image extensions
        return in_array(strtolower($extension), $imageExtensions);
    }

}

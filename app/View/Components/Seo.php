<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seo extends Component
{
    public function __construct(
        public string $title = '',
        public string $description = '',
        public string $keywords = '',
        public string $image = '',
        public string $url = '',
        public string $type = 'website',
        public bool $titleAppend = true,
        public string $canonical = ''
    ) {}

    public function render()
    {
        $fullTitle = $this->title;
        
        if ($this->titleAppend && $this->title) {
            $fullTitle = $this->title . ' | ' . config('app.name', 'Legal CRM');
        } elseif ($this->title) {
            $fullTitle = $this->title;
        } else {
            $fullTitle = config('app.name', 'Legal CRM');
        }

        $imageUrl = $this->image;
        if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
            $imageUrl = asset($this->image);
        }

        $canonicalUrl = $this->canonical ?: $this->url;

        return 
'<title>' . e($fullTitle) . '</title>
<meta name="description" content="' . e($this->description) . '">
<meta name="keywords" content="' . e($this->keywords) . '">
<meta property="og:title" content="' . e($fullTitle) . '">
<meta property="og:description" content="' . e($this->description) . '">
<meta property="og:type" content="' . e($this->type) . '">
<meta property="og:url" content="' . e($this->url) . '">' . 
($imageUrl ? "\n<meta property=\"og:image\" content=\"" . e($imageUrl) . "\">" : '') .
'
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="' . e($fullTitle) . '">
<meta name="twitter:description" content="' . e($this->description) . '">' .
($imageUrl ? "\n<meta name=\"twitter:image\" content=\"" . e($imageUrl) . "\">" : '') .
($canonicalUrl ? "\n<link rel=\"canonical\" href=\"" . e($canonicalUrl) . "\">" : '');
    }
}
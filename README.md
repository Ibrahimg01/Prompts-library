# Premium Prompts Library Plugin

A comprehensive WordPress Multisite plugin for managing and displaying a beautiful library of AI prompts with chatbot integration.

## 🎯 Features

### For Super Admin (Network Admin)
- **Complete Prompt Management**: Add, edit, delete, and organize prompts
- **Categorization System**: Create unlimited categories with custom badge colors
- **Tagging System**: Add flexible tags to prompts for better organization
- **Multisite Publishing**: Choose which subsites can access each prompt
- **Ordering**: Drag and drop to reorder prompts (via menu_order)
- **Dashboard**: View statistics and quick actions
- **Settings**: Control prompts per page and cards per row

### For Tenant Admins (Subsite Admins)
- **Beautiful Library Interface**: Modern, user-friendly design with purple theme
- **Embedded Chatbot**: AI chatbot integration in the header section
- **Advanced Filtering**: Search by keyword, filter by category and tags
- **Card-Based Layout**: Responsive grid (2, 3, or 4 columns)
- **Quick Actions**: 
  - "View Prompt" - Opens elegant modal with full details
  - "Use Prompt" - Directly inserts prompt into chatbot input
- **Copy Functionality**: Copy prompts to clipboard
- **Pagination**: Easy navigation through large prompt collections
- **Responsive Design**: Works perfectly on all devices

## 📋 Requirements

- WordPress 5.8 or higher
- WordPress Multisite enabled
- PHP 7.4 or higher
- AI Chatbot plugin (for chatbot integration) - e.g., AI Engine by Meow Apps

## 🚀 Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin zip file
2. Go to Network Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Choose the zip file and click "Install Now"
5. Network Activate the plugin

### Method 2: Manual Installation

1. Extract the zip file
2. Upload the `prompts-library` folder to `/wp-content/plugins/`
3. Go to Network Admin → Plugins
4. Network Activate "Premium Prompts Library"

### Method 3: Via FTP

1. Extract the plugin files
2. Upload via FTP to `/wp-content/plugins/prompts-library/`
3. Network Activate via WordPress Network Admin

## ⚙️ Configuration

### Initial Setup (Super Admin)

1. **Access Network Admin**
   - Go to Network Admin → Prompts Library
   - You'll see a dashboard with statistics

2. **Create Categories**
   - Go to Network Admin → Prompts Library → Categories
   - Add categories (e.g., "Education", "Marketing", "Business")
   - Choose a badge color for each category (displays on cards)

3. **Create Tags** (Optional)
   - Go to Network Admin → Prompts Library → Tags
   - Add tags for granular filtering (e.g., "SEO", "Content", "Analysis")

4. **Add Your First Prompt**
   - Go to Network Admin → Prompts Library → Add New
   - **Title**: Enter the prompt name (e.g., "Comprehensive Writing Assistant")
   - **Short Description**: Brief 1-2 sentence description (shows on card)
   - **Prompt Text**: The actual prompt that will be used
   - **Categories & Tags**: Select relevant ones
   - **Publish to Sites**: Check which subsites should see this prompt
   - Click "Publish"

5. **Configure Settings**
   - Go to Network Admin → Prompts Library → Settings
   - Set **Prompts Per Page** (default: 9)
   - Set **Cards Per Row** (2, 3, or 4 columns)
   - Save Changes

### Chatbot Integration

The plugin includes a chatbot placeholder in the header:

```php
[mwai_chatbot id="chatbot-njj2fe"]
```

**To customize:**

1. If using a different chatbot plugin, edit:
   - File: `includes/class-frontend.php`
   - Line: ~166 (look for "Embedded Chatbot" section)
   - Replace the shortcode with your chatbot's shortcode

2. If using AI Engine or similar:
   - Create your chatbot in the plugin
   - Copy the shortcode
   - Replace `[mwai_chatbot id="chatbot-njj2fe"]` with your shortcode

## 📖 Usage Guide

### For Super Admin

#### Adding a New Prompt

1. Navigate to Network Admin → Prompts Library → Add New
2. Fill in:
   - **Title**: Clear, descriptive name
   - **Short Description**: 1-2 sentences for the card preview
   - **Prompt Text**: The full prompt (can be multiple paragraphs)
3. Select categories and add tags
4. In "Publish to Sites" sidebar, check the sites that should access this prompt
5. (Optional) Set Menu Order for custom sorting
6. Click Publish

#### Managing Prompts

- **Edit**: Click on any prompt title in the list
- **Quick Edit**: Hover over prompt and click "Quick Edit"
- **Bulk Actions**: Select multiple prompts for bulk operations
- **Filter**: Use the category and tag dropdowns to filter the list
- **Search**: Use the search box to find prompts by title or content

#### Publishing to Sites

Each prompt has a "Publish to Sites" meta box where you can:
- Check/uncheck sites that should display the prompt
- Update anytime - changes are immediate
- View published site count in the prompts list

#### Organizing Categories

- Add colors to categories for visual distinction
- Colors display as badges on cards and in modals
- Use contrasting colors for better readability
- Suggested colors: #8b5cf6 (purple), #f65c4b (red), #0D0D2B (dark blue)

### For Tenant Admin (Subsite Users)

#### Viewing the Library

1. Go to Admin Dashboard → Prompts Library
2. You'll see:
   - Purple header with chatbot
   - Search bar and filters
   - Grid of prompt cards

#### Searching and Filtering

1. **Search**: Type keywords in the search bar
2. **Filter by Category**: Select from category dropdown
3. **Filter by Tag**: Select from tag dropdown
4. **Apply Filters**: Click "Apply Filters" button
5. **Clear**: Click "Clear Filters" to reset

#### Using Prompts

**Option 1: Direct Use**
1. Find the prompt you want
2. Click "Use Prompt" button
3. The prompt is automatically inserted into the chatbot input field
4. Edit if needed and send to AI

**Option 2: View First**
1. Click "View Prompt" to open the modal
2. Review the full prompt details
3. Click "Use Prompt" in the modal footer
4. Prompt is inserted into chatbot

**Option 3: Copy**
1. Click "View Prompt"
2. Click "Copy Prompt"
3. Paste anywhere you need it

#### Understanding Cards

Each card shows:
- **Category Badge**: Colored label at the top
- **Title**: Prompt name
- **Description**: Brief overview
- **Tags**: Related topics/keywords
- **Actions**: View and Use buttons

#### Modal Features

The prompt modal displays:
- **Header**: Category badge and close button
- **Title**: Full prompt name
- **Description**: Detailed explanation
- **Prompt Container**: Gray box with the actual prompt text
- **Footer Actions**: Copy and Use buttons

## 🎨 Customization

### Changing Colors

Edit `assets/css/frontend.css`:

```css
/* Purple primary color */
.prompts-library-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

/* Red accent color */
.btn-primary {
    background: #f65c4b;
}

/* Dark color */
.btn-secondary {
    color: #0D0D2B;
}
```

### Modifying Layout

In Network Admin → Prompts Library → Settings:
- Change **Cards Per Row** (2, 3, or 4)
- Change **Prompts Per Page** (any number)

### Custom CSS

Add to your theme's `style.css` or Customizer:

```css
/* Example: Change card hover effect */
.prompt-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(139, 92, 246, 0.2);
}

/* Example: Larger buttons */
.btn {
    padding: 15px 25px;
    font-size: 16px;
}
```

## 🔧 Developer Notes

### File Structure

```
prompts-library/
├── prompts-library.php           # Main plugin file
├── includes/
│   ├── class-post-type.php      # Custom post type registration
│   ├── class-taxonomy.php       # Taxonomies (categories/tags)
│   ├── class-admin-menu.php     # Admin menu setup
│   ├── class-frontend.php       # Frontend display
│   ├── class-ajax-handler.php   # AJAX functionality
│   └── class-settings.php       # Settings management
├── assets/
│   ├── css/
│   │   └── frontend.css         # Styles
│   └── js/
│       └── frontend.js          # JavaScript
└── README.md                    # This file
```

### Hooks and Filters

The plugin follows WordPress coding standards and includes hooks for extensibility:

```php
// Example: Modify prompts query
add_filter('prompts_library_query_args', function($args) {
    // Modify $args as needed
    return $args;
});

// Example: Add custom field to prompt
add_action('prompts_library_after_prompt_meta', function($prompt_id) {
    // Add custom content
});
```

### Database

The plugin uses:
- **Post Type**: `prompt`
- **Taxonomies**: `prompt_category`, `prompt_tag`
- **Post Meta**:
  - `_prompt_text` - The actual prompt
  - `_prompt_description` - Short description
  - `_published_sites` - Array of site IDs
- **Term Meta**:
  - `category_color` - Hex color for category badge
- **Site Options**:
  - `prompts_library_settings` - Plugin settings array

### JavaScript Integration

The plugin looks for chatbot input fields using multiple selectors:

```javascript
const selectors = [
    'textarea[name="mwai_chat_input"]',
    '.mwai-input textarea',
    '#mwai-chat-input',
    'textarea.mwai-input',
    '.chatbot-input textarea',
    'input[type="text"].mwai-input'
];
```

To add support for a different chatbot, add your selector to this array in `assets/js/frontend.js`.

## 🐛 Troubleshooting

### Prompts Not Showing on Subsite

**Issue**: Tenant admin sees empty library

**Solutions**:
1. Check that prompts are published (not drafts)
2. Verify the subsite is checked in "Publish to Sites"
3. Go to Network Admin → Prompts Library → edit the prompt
4. Ensure site is selected in the sidebar

### "Use Prompt" Not Working

**Issue**: Button doesn't insert prompt into chatbot

**Solutions**:
1. Verify chatbot plugin is active on the subsite
2. Check chatbot shortcode in `class-frontend.php` matches your chatbot
3. Open browser console (F12) to check for JavaScript errors
4. Try the "Copy Prompt" button as an alternative

### Category Colors Not Showing

**Issue**: All category badges are purple

**Solution**:
1. Go to Network Admin → Prompts Library → Categories
2. Edit each category
3. Select a color using the color picker
4. Save

### Pagination Not Working

**Issue**: All prompts show on one page

**Solution**:
1. Go to Network Admin → Prompts Library → Settings
2. Set "Prompts Per Page" to your desired number
3. Save Changes

### Styles Look Broken

**Issue**: Layout is incorrect or colors are off

**Solutions**:
1. Clear browser cache (Ctrl+Shift+Del)
2. Clear WordPress cache if using a caching plugin
3. Deactivate other plugins that might conflict
4. Check browser console for CSS loading errors

## 🔒 Security

The plugin follows WordPress security best practices:

- **Nonce Verification**: All AJAX requests are verified
- **Capability Checks**: Proper permission checks throughout
- **Data Sanitization**: All inputs are sanitized
- **Data Escaping**: All outputs are escaped
- **SQL Injection Prevention**: Uses WordPress database methods
- **XSS Protection**: Prevents cross-site scripting

## 📝 Changelog

### Version 1.0.0
- Initial release
- Custom post type for prompts
- Categories and tags taxonomies
- Multisite support with selective publishing
- Beautiful frontend interface
- Modal popup system
- Chatbot integration
- AJAX-powered interactions
- Responsive design
- Settings page
- Search and filtering
- Pagination

## 💡 Tips and Best Practices

1. **Organize Categories**: Use broad categories (Education, Marketing, Business)
2. **Use Descriptive Tags**: Add specific tags for better filtering
3. **Write Clear Descriptions**: Help users understand what each prompt does
4. **Test Prompts**: Try each prompt before publishing to sites
5. **Regular Updates**: Keep your prompt library fresh and relevant
6. **Monitor Usage**: Check which prompts are most popular
7. **Consistent Formatting**: Use similar structure for prompt text
8. **Color Coding**: Use consistent colors for related categories

## 🤝 Support

For support, feature requests, or bug reports:

1. Check this README first
2. Review the Troubleshooting section
3. Check WordPress and PHP error logs
4. Contact: support@learnwithhasan.com
5. Visit: https://learnwithhasan.com

## 📄 License

This plugin is licensed under GPL v2 or later.

Copyright (C) 2025 Learn With Hasan

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

## 🎓 Credits

Developed by Learn With Hasan
Website: https://learnwithhasan.com

---

**Thank you for using Premium Prompts Library!** 🚀

If you find this plugin helpful, please consider:
- Rating it 5 stars ⭐⭐⭐⭐⭐
- Sharing with colleagues
- Providing feedback for improvements

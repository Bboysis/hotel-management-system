 <?php
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Available languages with flags
$available_languages = [
    'en' => '🇬🇧 English',
    'am' => '🇪🇹 አማርኛ',
    'fr' => '🇫🇷 Français',
    'pt' => '🇵🇹 Português'
];

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

 
// Load language file
function load_language($lang_code) {
    global $available_languages;
    $lang_file = __DIR__ . '/languages/' . $lang_code . '.php';
    if (file_exists($lang_file) && isset($available_languages[$lang_code])) {
        require_once $lang_file;
        return isset($lang) ? $lang : array();
    }
    return array();
}

// Get translation
function __($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}

// Language switcher HTML
function language_switcher() {
    global $available_languages;
    $current = $_SESSION['lang'] ?? 'en';
    
    $html = '<select onchange="window.location.href=\'?lang=\' + this.value" style="
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid rgba(212, 175, 55, 0.5);
        background: rgba(26, 26, 46, 0.9);
        color: white;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
        min-width: 110px;
        height: 34px;
        margin: 0;
    ">';
    foreach ($available_languages as $code => $name) {
        $selected = ($code == $current) ? 'selected' : '';
        $html .= '<option value="' . $code . '" ' . $selected . ' style="background: #1a1a2e; color: white;">' . $name . '</option>';
    }
    $html .= '</select>';
    return $html;
}

 

// Set language from URL
if (isset($_GET['lang']) && isset($available_languages[$_GET['lang']])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . str_replace('?lang=' . $_GET['lang'], '', $_SERVER['REQUEST_URI']));
    exit();
}

// Load current language
$lang = load_language($_SESSION['lang'] ?? 'en');

// If no language loaded, create empty array
if (!isset($lang) || !is_array($lang)) {
    $lang = array();
}
?>
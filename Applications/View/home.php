<?php
/**
 * Header Templates
 */
use \MalangPhp\Site\Conf\App;

if (!isset($this) || !$this instanceof \Slim\Views\PhpRenderer) {
    return;
}

/**
 * Example Set Title
 */
$this->addAttribute('title', 'Home Page');

/**
 * Require Header
 */
require_once __DIR__ . '/header.php';
?>
<div style="max-width: 90%;margin: 0 auto;width: 940px;padding: 10px;border:1px solid #ddd;background: #f1f1f1">
    <h2>Welcome To Home Page</h2>
    <div style="display: block;border-bottom: 1px solid #ddd;margin-bottom: 1em"></div>
    <table width="100%">
        <tr>
            <th width="200" align="left" valign="top">Route File</th>
            <td><code><?php echo App::getInstance()->getArrayLoadedConfig()['Routes'];?></code></td>
        </tr>
        <tr>
            <th width="200" align="left" valign="top">Current Environment</th>
            <td><code><?php echo App::getInstance()->getEnvironment();?></code></td>
        </tr>
        <tr>
            <th width="200" align="left" valign="top">Current Configuration</th>
            <td><pre><?php print_r(App::getInstance()->get('config')->data());?></pre></td>
        </tr>
    </table>
</div>
<?php
/**
 * Require Footer
 */
require_once __DIR__ . '/footer.php';

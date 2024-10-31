<?php
/**
 * @package: purpleads-plugin
 */

/**
 * Plugin Name: PurpleAds
 * Description: PurpleAds is an ad network that helps website owners monetize their website easily and effectively.
 * Version: 1.0.2
 * Author: PurpleAds LTD
 * Author URI: https://purpleads.io
 * License: GPLv3 or later
 * Text Domain: purpleads-plugin
 */

if (!defined('ABSPATH')) {
    die;
}

/**
 * constants
 */
class PAConstants
{
    public static function options_group()
    {
        return 'purpleads_options';
    }

    public static function purpleads_site_token()
    {
        return 'purpleads_site_token';
    }

    public static function option_meta_tag()
    {
        return 'purpleads_meta_tag';
    }

    public static function purpleads_banner()
    {
        return 'purple_banner';
    }
    public static function purpleads_floating()
    {
        return 'purple_floating';
    }
    public static function purpleads_video()
    {
        return 'purple_video';
    }

    public static function version()
    {
        return '1.0.2';
    }
}

/* hooks */
add_action('admin_menu', 'purpleads_admin_menu'); //1.5.0
add_action('admin_init', 'purpleads_admin_init'); //2.5.0
add_action('admin_enqueue_scripts', 'purpleads_enqueue_admin_styles');
add_action('wp_head', 'purpleads_inject_codes'); //1.2.0


function purpleads_admin_menu()
{
    add_menu_page('PurpleAds Settings', 'PurpleAds', 'manage_options', 'purpleads', 'purpleads_admin_menu_page_html', 'dashicons-purpleads');
}

function purpleads_admin_init()
{
    register_setting(PAConstants::options_group(), PAConstants::purpleads_site_token());
    register_setting(PAConstants::options_group(), PAConstants::option_meta_tag());
    register_setting(PAConstants::options_group(), PAConstants::purpleads_banner());
    register_setting(PAConstants::options_group(), PAConstants::purpleads_floating());
    register_setting(PAConstants::options_group(), PAConstants::purpleads_video());
}

function purpleads_enqueue_admin_styles()
{
    wp_enqueue_style('purpleads_admin_style', plugin_dir_url(__FILE__).'style.css');
    wp_register_style('dashicons-purpleads', plugin_dir_url(__FILE__).'/assets/css/dashicons-purpleads.css');
    wp_enqueue_style('dashicons-purpleads');
}

function purpleads_inject_codes()
{
    $siteToken = purpleads_get_site_token();
    $metaTag = purpleads_get_meta_tag();
    $isBanner = get_purpleads_banner();
    $isFloating = get_purpleads_floating();
    $isVideo = get_purpleads_video();

    if($metaTag) {

        ?>
        <meta name="purpleads-verification" content="<?php echo esc_attr($metaTag); ?>" />
        <?php
    }

    if($siteToken) {
        $bannerUrlSource = "https://cdn.prplads.com/agent.js?publisherId={$siteToken}";
        $floatingUrlSource = "https://cdn.prplads.com/load.js?publisherId={$siteToken}";
        $videoUrlSource = "https://cdn.prplads.com/video-agent.js?publisherId={$siteToken}";
        if($isBanner) {
            ?>
            <script src=<?php echo esc_url($bannerUrlSource); ?> data-pa-tag async></script>
            <?php
        }
        if($isFloating) {
            ?>
            <script src=<?php echo esc_url($floatingUrlSource); ?> id='purpleads-client'></script>
            <?php
        }
        if($isVideo) {
            ?>
            <script src=<?php echo esc_url($videoUrlSource); ?> async></script>
            <?php
        }
    }
}

function get_purpleads_banner()
{
    return get_option(PAConstants::purpleads_banner());
}

function get_purpleads_floating()
{
    return get_option(PAConstants::purpleads_floating());
}

function get_purpleads_video()
{
    return get_option(PAConstants::purpleads_video());
}

function purpleads_get_site_token()
{
    $siteToken = get_option(PAConstants::purpleads_site_token());
    return $siteToken;
}

function purpleads_get_meta_tag()
{
    $metaTag = get_option(PAConstants::option_meta_tag());
    return $metaTag;
}

function purpleads_admin_menu_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $siteToken = purpleads_get_site_token();
    $metaTag = purpleads_get_meta_tag()

    ?>

    <div class="wrap" id="ps-settings">
        <a href="https://purpleads.io">
            <img class="top-logo" src="<?php echo plugin_dir_url(__FILE__).'assets/logo.png'; ?>">

            <form action="options.php" method="post">
                <?php
                settings_fields(PAConstants::options_group());
                do_settings_sections(PAConstants::options_group());
                ?>

                <div class="ps-settings-container">
        </a>
        <div class="account-link">If you don't have an account - <a href="https://publishers.purpleads.io/#/signup" target="_blank">signup here!</a></div>
        <div class="section" style="margin-bottom: 10px; padding-top:5px; padding-bottom: 5px; font-weight: 500">
            <p style="">NOTE: If you’re using a cache/security plugins e.g. <b>WP-Rocket, LiteSpeed, Wordfence</b> <br/>make sure to refresh/clear your cache and whitelist PurpleAds domains (purpleads.io, prplads.com).</p>
        </div>
        <div class="section">
            <div class="label">Your site's verification meta tag:</div>

            <div class="verification-container">
                <div class="v-input-place"><?php echo esc_attr('<meta name="purpleads-verification" content='); ?>"
                    <input class="v-input" type="text"  placeholder="Optional" name="<?php echo PAConstants::option_meta_tag(); ?>" value="<?php echo esc_attr($metaTag);?>" />
                    "/>
                </div>
            </div>
            <div class="label" style="margin-top: 18px">Your site token:</div>
            <input type="text" class="input-token" placeholder="Required" name="<?php echo PAConstants::purpleads_site_token(); ?>" value="<?php echo esc_attr($siteToken); ?>" />
            <div class="m-t"><a href="https://help.purpleads.io/en/articles/6966938-where-do-i-find-my-site-token" target="_blank">Where is my site token?</a></div>
            <div class="m-t-2 " style="overflow: auto;">
                <div class="d-inline-block ">
                    <div class="title">
                        <div>
                            <img class="resimg" src="<?php echo plugin_dir_url(__FILE__).'assets/resbanner.png'; ?>">
                        </div>
                        <div style="display: flex; flex-direction: column;margin-left: 15px">
                            <div style="font-weight: bold; font-size: 17px; margin-bottom: 5px">Automatic Sticky Ads</div>
                            <div style="margin: 0">Easily add additional ad placements to your page. See
                                <a href="https://help.purpleads.io/en/articles/5384792-automatic-sticky-ads">this guide</a>
                                for more information.
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; margin-right: 20px">
                        <input
                                type="checkbox"
                                class="onoffswitch-checkbox"
                                id="banner"
                                name="<?php echo PAConstants::purpleads_banner(); ?>" <?php if(get_purpleads_banner()) { echo "checked";
                        } ?>
                        >
                        <label class="onoffswitch-label" for="banner"></label>
                    </div>
                </div>
                <div class="d-inline-block ">
                    <div class="title">
                        <div>
                            <img class="resimg" src="<?php echo plugin_dir_url(__FILE__).'assets/floating.png'; ?>">
                        </div>
                        <div style="display: flex; flex-direction: column; margin-left: 15px">
                            <div style="font-weight: bold; font-size: 17px;margin-bottom: 5px">Floating</div>
                            <div >A non-intrusive floating banner with an image and text.
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center;margin-right: 20px" class="" >
                        <input
                                type="checkbox"
                                class="onoffswitch-checkbox"
                                id="floating"
                                name="<?php echo PAConstants::purpleads_floating(); ?>" <?php if(get_purpleads_floating()) { echo "checked";
                        } ?>
                        >
                        <label class="onoffswitch-label" for="floating"></label>
                    </div>
                </div>
                <div class="d-inline-block">
                    <div class="title">
                        <div>
                            <img class="resimg" src="<?php echo plugin_dir_url(__FILE__).'assets/video.png'; ?>">
                        </div>
                        <div style="display: flex; flex-direction: column;margin-left: 15px">
                            <div style="font-weight: bold; font-size: 17px; margin-bottom: 5px" >Video</div>
                            <div style="margin: 0"> A floating video ad unit <b>(depends on website's approved partners)</b>.

                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; margin-right: 20px">
                        <input
                                type="checkbox"
                                class="onoffswitch-checkbox"
                                id="video"
                                name="<?php echo PAConstants::purpleads_video(); ?>" <?php if(get_purpleads_video()) { echo "checked";
                        } ?>
                        >
                        <label class="onoffswitch-label" for="video"></label>
                    </div>
                </div>
            </div>
            <div class="submit_btn">
                <div>
                    <?php submit_button('Save'); ?>
                </div>
                <div>
                    <a class="dash-link" target="_blank"  href="https://publishers.purpleads.io/">
                        Go to dashboard
                    </a>
                </div>
            </div>
        </div>
        <div class="res-banner">
            <div style="font-size: 30px; font-weight: bold">Responsive Banner Instructions</div>
            <p>Adding PurpleAds responsive banners requires having a container for the ad to be embedded in, for example in the side bar, inside the content, sticky top, etc.

                If you can add the ad code into your theme yourself, that's great, if not we suggest using a plugin like <a href="https://wordpress.org/plugins/advanced-ads/">Advanced Ads</a> to easily manage placements in your website.

                Create a placement, and paste the responsive banner code:</p>
            <ol>
                <li>Copy and paste the ad unit code in between &lt;div&gt;&lt;/div&gt; tags where you want an ad to appear. Do this for each individual ad unit, on every page.</li>
                <li>See the
                    <a href='https://help.purpleads.io/en/articles/5182203-supported-banner-sizes#_ga=2.176822433.50755084.1674984777-2007092094.1670849353' target='_blank' class='id-link'>supported sizes</a>
                    and
                    <a href='https://help.purpleads.io/en/articles/5178946-add-purpleads-responsive-display-banners-to-your-website#_ga=2.172759207.50755084.1674984777-2007092094.1670849353' target='_blank' class='id-link'> how to install this code.</a>

                </li>
                <li>[Optional] Update your
                    <a href='https://publishers.purpleads.io/#/adstxt' target='_blank' class='id-link'>Ads.txt lines</a>
                    for better demand and performance.
                </li>
            </ol>
            <p>The smart snippet will find the best sizes according to the container, and will inject the most suitable ad:</p>
            <pre>&lt;div&gt;&lt;script src='https://cdn.prplads.com/agent.js?publisherId=<?php echo esc_attr($siteToken); ?> data-pa-tag async&gt; &lt;/script&gt;&lt;/div&gt;</pre>
            <p>You can limit the size by adding a class or style attribute to your container, like:</p>
            <pre>&lt;div <span id="code-diff">style="width:300px;height:250px"</span>&gt;&lt;script src='https://cdn.prplads.com/agent.js?publisherId=<?php echo esc_attr($siteToken); ?> data-pa-tag async&gt; &lt;/script&gt;&lt;/div&gt;</pre>
            <p>Change the 300px width and 250px height according to the maximum size you prefer, keep in mind our list of supported sizes.</p>
        </div>
    </div>
    </form>
    </div>

    <?php
}

?>

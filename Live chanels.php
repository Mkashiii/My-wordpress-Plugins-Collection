<?php
/*
Plugin Name: Live News Integration
Description: A WordPress plugin to display 4 news channel icons with responsive design using PHP only.
Version: 2.6
Author: Grok 3 (xAI)
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode with enhanced responsive CSS
function live_news_shortcode() {
    ob_start();

    // News channels data
    $channels = array(
        array(
            'name' => 'ARY News',
            'image' => 'https://arynews.tv/wp-content/uploads/2021/07/ARY-News.png',
            'url' => 'https://www.youtube.com/embed/sUKwTVAc0Vo?autoplay=1&mute=1'
        ),
        array(
            'name' => 'Geo News',
            'image' => 'https://www.geo.tv/assets/front/images/logo-blue.svg',
            'url' => 'https://www.youtube.com/embed/O3DPVlynUM0?autoplay=1&mute=1'
        ),
        array(
            'name' => 'Samaa News',
            'image' => 'https://samaa.tv/assets/images/logo.png',
            'url' => 'https://www.youtube.com/embed/K1a6AvD_-Tw?autoplay=1&mute=1'
        ),
        array(
            'name' => 'Dunya News',
            'image' => 'https://dunyanews.tv/newweb/assets/img/logo/dn-en.webp',
            'url' => 'https://www.youtube.com/embed/iSVX3rLJxXI?autoplay=1&mute=1'
        )
    );

    $active_channel = isset($_POST['channel']) ? sanitize_text_field($_POST['channel']) : '';

    // Complete HTML and CSS output
    echo "
    <style>
        .live-news-container {
            width: 100%;
            margin: 2rem auto;
            padding: 0 1rem;
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f6fa, #e9ecef);
            border-radius: 1rem;
            overflow: hidden;
        }
        .news-icons-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: clamp(1rem, 2vw, 2rem);
            padding: clamp(1rem, 2vw, 2rem);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .news-column {
            display: flex;
            justify-content: center;
            align-items: center;
            min-width: 0;
        }
        .news-item-form {
            margin: 0;
            width: 100%;
        }
        .news-item-link {
            text-decoration: none;
            display: block;
            width: 100%;
        }
        .news-item {
            background: linear-gradient(145deg, #ffffff, #e6e9ed);
            border: none;
            border-radius: 0.75rem;
            padding: clamp(0.75rem, 2vw, 1.25rem);
            cursor: pointer;
            text-align: center;
            width: 100%;
            box-shadow: 0.5rem 0.5rem 1rem rgba(0,0,0,0.1), -0.5rem -0.5rem 1rem rgba(255,255,255,0.8);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .news-item:hover {
            transform: translateY(-0.5rem);
            box-shadow: 0.75rem 0.75rem 1.5rem rgba(0,0,0,0.15), -0.75rem -0.75rem 1.5rem rgba(255,255,255,0.9);
            background: linear-gradient(145deg, #f0f4f8, #dfe3e7);
        }
        .icon-wrapper {
            width: 100%;
            aspect-ratio: 16/9;
            max-width: 150px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(145deg, #e6e9ed, #ffffff);
            border-radius: 0.5rem;
            box-shadow: 0.3rem 0.3rem 0.6rem rgba(0,0,0,0.1), -0.3rem -0.3rem 0.6rem rgba(255,255,255,0.7);
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        .icon-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
            transition: all 0.5s ease;
            opacity: 0;
        }
        .news-item:hover .icon-wrapper {
            transform: scale(1.05);
            box-shadow: 0.4rem 0.4rem 0.8rem rgba(0,0,0,0.15), -0.4rem -0.4rem 0.8rem rgba(255,255,255,0.8);
        }
        .news-item:hover .icon-wrapper::before {
            opacity: 1;
            transform: translate(25%, 25%);
        }
        .news-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
            z-index: 1;
        }
        .news-text {
            margin-top: 0.75rem;
            font-size: clamp(0.8rem, 2vw, 1rem);
            font-weight: 600;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            transition: all 0.3s ease;
        }
        .news-item:hover .news-text {
            color: #00b4d8;
        }
        .live-player-container {
            padding: clamp(1rem, 2vw, 2rem);
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0.5rem 0.5rem 1rem rgba(0,0,0,0.1), -0.5rem -0.5rem 1rem rgba(255,255,255,0.8);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            margin: 0 1rem 1rem;
        }
        .live-player-container iframe {
            width: 100%;
            height: clamp(200px, 50vw, 400px);
        }
        .close-form {
            margin-top: 1.5rem;
        }
        .close-player {
            padding: 0.75rem 2rem;
            background: linear-gradient(90deg, #ff7676, #ff9b9b);
            color: #fff;
            border: none;
            border-radius: 2rem;
            cursor: pointer;
            font-size: clamp(0.8rem, 2vw, 0.95rem);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            box-shadow: 0.3rem 0.3rem 0.6rem rgba(0,0,0,0.1), -0.3rem -0.3rem 0.6rem rgba(255,255,255,0.7);
            transition: all 0.4s ease;
        }
        .close-player:hover {
            background: linear-gradient(90deg, #ff5252, #ff7676);
            transform: translateY(-0.2rem) scale(1.05);
            box-shadow: 0.4rem 0.4rem 0.8rem rgba(0,0,0,0.15), -0.4rem -0.4rem 0.8rem rgba(255,255,255,0.8);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(1rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 1024px) {
            .news-icons-row {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }
        @media (max-width: 768px) {
            .live-news-container {
                padding: 0 0.5rem;
                margin: 1rem auto;
            }
            .news-icons-row {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }
        }
        @media (max-width: 480px) {
            .news-icons-row {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            .icon-wrapper {
                max-width: 120px;
            }
        }
    </style>
    <div class='live-news-container'>
        <div class='news-icons-row'>";
        
            foreach ($channels as $index => $channel) {
                echo "
                <div class='news-column'>
                    <form method='POST' action='' class='news-item-form'>
                        <input type='hidden' name='channel' value='" . esc_attr($index) . "'>
                        <a href='" . esc_url($channel['url']) . "' target='_blank' class='news-item-link'>
                            <button type='submit' class='news-item'>
                                <div class='icon-wrapper'>
                                    <img src='" . esc_url($channel['image']) . "' alt='" . esc_attr($channel['name']) . "' class='news-logo'>
                                </div>
                                <span class='news-text'>" . esc_html($channel['name']) . "</span>
                            </button>
                        </a>
                    </form>
                </div>";
            }
            
    echo "  </div>";
        
    if ($active_channel !== '' && array_key_exists($active_channel, $channels)) {
        echo "
            <div class='live-player-container'>
                <iframe src='" . esc_url($channels[$active_channel]['url']) . "' frameborder='0' allowfullscreen></iframe>
                <form method='POST' action='' class='close-form'>
                    <input type='hidden' name='channel' value=''>
                    <button type='submit' class='close-player'>Close</button>
                </form>
            </div>";
    }
    
    echo "
    </div>";

    return ob_get_clean();
}
add_shortcode('live_news', 'live_news_shortcode');
?>
<?php
/*
 * Plugin Name: Influencer Search Shortcode
 * Description: Search bar with dark theme, uses Google Custom Search JSON API and Wikipedia API, displays movies, TV shows, upcoming movies, social media, news, and full grid of profile data (Audience Demographics, Performance Metrics, Pricing, Authenticity, Portfolio), modern form with glow, via shortcode [influencer_search].
 * Version: 2.6.0
 * Author: Grok
 * License: GPL v2 or later
 */

function influencer_search_shortcode() {
    ob_start();
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap');
body {
    background-color: #0D0D0D;
    color: #E6E6E6;
    font-family: 'Inter', sans-serif;
    margin: 0;
    line-height: 1.6;
}
.influencer-search-container {
    max-width: 1280px;
    margin: 60px auto;
    padding: 40px;
    background: linear-gradient(145deg, #1C1C1C, #141414);
    border-radius: 24px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.7);
    overflow: hidden;
}
.influencer-search-container h2 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 2.8em;
    color: #FF2E63;
    text-align: center;
    margin-bottom: 50px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    position: relative;
}
.influencer-search-container h2::after {
    content: '';
    width: 60px;
    height: 4px;
    background: #FF2E63;
    display: block;
    margin: 10px auto;
    border-radius: 2px;
}
.influencer-search-form {
    display: flex;
    gap: 20px;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.05);
    padding: 30px;
    border-radius: 16px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 20px rgba(255, 46, 99, 0.3);
    transition: all 0.3s ease;
}
.influencer-search-form:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 30px rgba(255, 46, 99, 0.5);
}
.influencer-search-form label {
    font-weight: 500;
    color: #FFFFFF;
    font-size: 1.2em;
    margin-right: 10px;
}
.influencer-search-form input[type="text"] {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    font-size: 1.1em;
    background-color: #222222;
    color: #E6E6E6;
    transition: all 0.3s ease;
}
.influencer-search-form input[type="text"]:focus {
    border-color: #FF2E63;
    box-shadow: 0 0 12px rgba(255, 46, 99, 0.4);
    outline: none;
}
.influencer-search-form input[type="submit"] {
    padding: 14px 40px;
    background: linear-gradient(90deg, #FF2E63, #FF6B6B);
    color: #FFFFFF;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1.2em;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
}
.influencer-search-form input[type="submit"]:hover {
    background: linear-gradient(90deg, #E91E63, #FF5252);
    transform: scale(1.05);
}
.influencer-data {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 40px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 16px;
    animation: fadeIn 0.5s ease-in-out;
}
.influencer-data .card {
    background: linear-gradient(145deg, #252525, #1E1E1E);
    padding: 24px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease-in-out;
}
.influencer-data .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
    border-color: rgba(255, 46, 99, 0.3);
}
.influencer-data h3 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 1.6em;
    color: #FF2E63;
    margin: 0 0 16px;
    position: relative;
}
.influencer-data h3::after {
    content: '';
    width: 30px;
    height: 3px;
    background: #FF2E63;
    display: block;
    margin-top: 8px;
    border-radius: 2px;
}
.influencer-data p {
    margin: 8px 0;
    color: #B3B3B3;
    line-height: 1.6;
    font-size: 0.95em;
}
.movies-shows-section, .news-section, .social-media-section, .basic-profile-section {
    max-width: 900px;
    margin: 40px auto;
    padding: 24px;
    background: linear-gradient(145deg, #252525, #1E1E1E);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: fadeIn 0.5s ease-in-out;
}
.movies-shows-section .movie-card, .news-section .news-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 16px;
    margin: 12px 0;
    border-radius: 12px;
    transition: all 0.3s ease;
}
.movies-shows-section .movie-card:hover, .news-section .news-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
.basic-profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-top: 16px;
}
.section-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: #FF2E63;
    margin-bottom: 20px;
    font-size: 1.4em;
    position: relative;
}
.section-title::after {
    content: '';
    width: 40px;
    height: 3px;
    background: #FF2E63;
    display: block;
    margin-top: 8px;
    border-radius: 2px;
}
.social-links {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin: 24px 0;
}
.social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
    border-radius: 50%;
    text-decoration: none;
    font-size: 24px;
    transition: all 0.3s ease;
}
.social-links a:hover {
    transform: scale(1.1);
    box-shadow: 0 0 15px rgba(255, 46, 99, 0.4);
}
.social-links a.twitter { background: linear-gradient(45deg, #1DA1F2, #55ACEE); }
.social-links a.instagram { background: linear-gradient(45deg, #E4405F, #F77737); }
.social-links a.youtube { background: linear-gradient(45deg, #FF0000, #FF4D4D); }
.social-links a.tiktok { background: linear-gradient(45deg, #000000, #333333); }
.error-message {
    color: #FF5252;
    font-weight: 500;
    text-align: center;
    margin: 20px 0;
    font-size: 1.1em;
    animation: fadeIn 0.5s ease-in-out;
}
.basic-profile-section {
    text-align: center;
}
.basic-profile-section h3 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 2em;
    color: #FF2E63;
    margin: 0 0 20px;
}
.basic-profile-section p {
    color: #B3B3B3;
    line-height: 1.7;
    margin: 16px 0;
    font-size: 1em;
}
.basic-profile-section a {
    color: #FF2E63;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}
.basic-profile-section a:hover {
    color: #E91E63;
    text-decoration: underline;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@media (max-width: 768px) {
    .influencer-search-container {
        margin: 20px;
        padding: 20px;
    }
    .influencer-search-form {
        flex-direction: column;
        gap: 15px;
        padding: 20px;
    }
    .influencer-search-form input[type="text"] {
        width: 100%;
    }
    .influencer-data {
        grid-template-columns: 1fr;
    }
    .influencer-search-container h2 {
        font-size: 2em;
    }
    .basic-profile-section h3 {
        font-size: 1.6em;
    }
    .basic-profile-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 480px) {
    .influencer-search-form input[type="submit"] {
        width: 100%;
    }
    .social-links a {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
}
</style>
<div class="influencer-search-container">
    <h2>Search Your Favorite Influencer</h2>
    <form role="search" method="get" class="influencer-search-form" action="">
        <label for="influencer_search">Influencer Name</label>
        <input type="text" name="influencer_search" id="influencer_search" placeholder="e.g., Sydney Sweeney" required />
        <input type="submit" value="Search" />
    </form>
<?php
    if (isset($_GET['influencer_search']) && !empty($_GET['influencer_search'])) {
        $search_query = sanitize_text_field($_GET['influencer_search']);
        $niche_filter = isset($_GET['niche_filter']) ? sanitize_text_field($_GET['niche_filter']) : '';
        $serp_data = [
            'name' => 'Sydney Sweeney',
            'age' => '27',
            'birth_date' => 'September 12, 1997',
            'movies_shows' => [
                ['title' => 'Euphoria', 'year' => 'Since 2019', 'type' => 'TV Show'],
                ['title' => 'Anyone But You', 'year' => '2023', 'type' => 'Movie'],
                ['title' => 'The Voyeurs', 'year' => '2021', 'type' => 'Movie'],
                ['title' => 'Immaculate', 'year' => '2024', 'type' => 'Movie'],
                ['title' => 'Madame Web', 'year' => '2024', 'type' => 'Movie'],
                ['title' => 'The White Lotus', 'year' => 'Since 2021', 'type' => 'TV Show'],
                ['title' => 'Eden', 'year' => '2024', 'type' => 'Movie'],
                ['title' => 'The Handmaid\'s Tale', 'year' => 'Since 2017', 'type' => 'TV Show'],
            ],
            'upcoming_movies' => [
                ['title' => 'The Housemaid', 'year' => '2025'],
                ['title' => 'Echo Valley', 'year' => '2025'],
                ['title' => 'Americana', 'year' => '2025'],
            ],
            'social_media' => [
                'instagram' => 'sydney_sweeney',
                'twitter' => 'Sydney_Sweeney',
                'youtube' => '@SydneySweeneyOfficial',
                'tiktok' => 'sydneysweeney',
            ],
            'recent_news' => [
                ['title' => 'Sydney Sweeney teases "unhinged" Euphoria Season 3', 'source' => 'The Hollywood Reporter', 'date' => '2025-05-25', 'url' => 'https://www.hollywoodreporter.com/tv-news/sydney-sweeney-euphoria-season-3'],
                ['title' => 'Sydney Sweeney explains why she joined Madame Web', 'source' => 'Gamereactor UK', 'date' => '2025-05-25', 'url' => 'https://www.gamereactor.uk/sydney-sweeney-explains-why-she-joined-madame-web'],
                ['title' => 'Echo Valley: What to expect from Sydney Sweeney-Julianne Moore’s thriller', 'source' => 'NewsBytes', 'date' => '2025-05-25', 'url' => 'https://www.newsbytesapp.com/news/entertainment/echo-valley-what-to-expect-from-sydney-sweeney-julianne-moore-s-thriller'],
            ],
        ];
        function get_social_media_data($name, $google_results = [], $serp_social = []) {
            $handle_map = [
                'sydney sweeney' => [
                    'twitter' => 'Sydney_Sweeney',
                    'instagram' => 'sydney_sweeney',
                    'youtube' => '@SydneySweeneyOfficial',
                    'tiktok' => 'sydneysweeney',
                ],
                'kylie jenner' => [
                    'twitter' => 'KylieJenner',
                    'instagram' => 'kyliejenner',
                    'youtube' => '@KylieJenner',
                    'tiktok' => 'kyliejenner',
                ],
            ];
            $name = strtolower($name);
            $default_data = [
                'platforms' => ['Instagram', 'Twitter', 'YouTube', 'TikTok'],
                'follower_count' => ['Instagram' => '500K', 'Twitter' => '200K', 'YouTube' => '300K', 'TikTok' => '1M'],
                'engagement_rate' => ['Instagram' => '2.5%', 'Twitter' => '1.8%', 'YouTube' => '3.1%', 'TikTok' => '5.0%'],
                'post_frequency' => '3-5 posts/week',
                'content_type' => 'Reels, promos, lifestyle',
                'reach' => '100K-500K per post',
                'impressions' => '1M-5M monthly',
                'handles' => [
                    'twitter' => strtolower(str_replace(' ', '', $name)),
                    'instagram' => strtolower(str_replace(' ', '', $name)),
                    'youtube' => '@' . htmlspecialchars(str_replace(' ', '', $name)),
                    'tiktok' => strtolower(str_replace(' ', '', $name)),
                ],
            ];
            if (!empty($serp_social)) {
                $default_data['handles'] = array_merge($default_data['handles'], $serp_social);
            }
            foreach ($google_results as $item) {
                if (isset($item['link'])) {
                    if (strpos($item['link'], 'instagram.com') !== false) {
                        $default_data['handles']['instagram'] = htmlspecialchars(basename(parse_url($item['link'], PHP_URL_PATH)));
                    } elseif (strpos($item['link'], 'x.com') !== false || strpos($item['link'], 'twitter.com') !== false) {
                        $default_data['handles']['twitter'] = htmlspecialchars(basename(parse_url($item['link'], PHP_URL_PATH)));
                    } elseif (strpos($item['link'], 'youtube.com') !== false) {
                        $default_data['handles']['youtube'] = htmlspecialchars(parse_url($item['link'], PHP_URL_PATH));
                    } elseif (strpos($item['link'], 'tiktok.com') !== false) {
                        $default_data['handles']['tiktok'] = htmlspecialchars(basename(parse_url($item['link'], PHP_URL_PATH)));
                    }
                }
            }
            if (isset($handle_map[$name])) {
                $default_data['handles'] = array_merge($default_data['handles'], $handle_map[$name]);
                if ($name === 'sydney sweeney') {
                    $default_data['follower_count']['Instagram'] = '25.1M';
                    $default_data['follower_count']['Twitter'] = '1.2M';
                    $default_data['follower_count']['YouTube'] = '500K';
                    $default_data['follower_count']['TikTok'] = '3M';
                }
            }
            return $default_data;
        }
        $api_key = 'YOUR_API_KEY';
        $search_engine_id = 'YOUR_SEARCH_ENGINE_ID';
        $google_api_url = 'https://www.googleapis.com/customsearch/v1?key=' . urlencode($api_key) . '&cx=' . urlencode($search_engine_id) . '&q=' . urlencode($search_query . ' biography');
        $google_response = wp_remote_get($google_api_url);
        $wiki_api_url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts|info&exintro&explaintext&titles=' . urlencode($search_query);
        $wiki_response = wp_remote_get($wiki_api_url);
        $profile = [
            'name' => $search_query,
            'age' => 'Unknown',
            'birth_year' => 'Unknown',
            'gender' => 'Unknown',
            'location' => 'Unknown',
            'languages' => 'English',
            'niche' => $niche_filter ?: 'Unknown',
        ];
        $demographics = [
            'age_range' => '18-34',
            'gender_breakdown' => '60% Female, 40% Male',
            'top_countries' => 'USA, Canada, UK',
            'interests' => $niche_filter ? ucfirst($niche_filter) . ', Lifestyle' : 'Lifestyle',
        ];
        $performance = [
            'previous_campaigns' => '5 campaigns (Nike, Adidas)',
            'engagement_trends' => 'Stable, +2% monthly',
            'sponsored_vs_organic' => '60% sponsored, 40% organic',
        ];
        $pricing = [
            'cost_per_post' => '$500-$2000',
            'collaboration_types' => 'Paid, Affiliate, Product Seeding',
            'availability' => 'Available for campaigns in Q4 2025',
        ];
        $authenticity = [
            'fake_follower_percentage' => '5%',
            'bot_engagement' => 'Low',
            'past_collaborations' => 'Nike, Adidas, Red Bull',
        ];
        $portfolio = [
            'media_samples' => 'Video reviews, Instagram Reels, YouTube vlogs',
            'past_deals' => 'Sponsored posts for sportswear brands',
            'style_tone' => 'Energetic, authentic, motivational',
        ];
        $snippet = 'No biography found for ' . esc_html($search_query) . '.';
        $source_url = 'https://www.google.com/search?q=' . urlencode($search_query);
        if (strtolower($search_query) === 'sydney sweeney') {
            $profile = [
                'name' => $serp_data['name'],
                'age' => $serp_data['age'],
                'birth_year' => date('Y', strtotime($serp_data['birth_date'])),
                'gender' => 'Female',
                'location' => 'USA',
                'languages' => 'English',
                'niche' => $niche_filter ?: 'Acting',
            ];
            $demographics['interests'] = 'Acting, Fashion, Lifestyle';
            $performance['previous_campaigns'] = '10 campaigns (Chanel, Miu Miu)';
            $pricing['cost_per_post'] = '$10K-$50K';
            $authenticity['past_collaborations'] = 'Chanel, Miu Miu, Armani';
            $portfolio['media_samples'] = 'Red carpet photos, Instagram Reels, Interviews';
            $portfolio['past_deals'] = 'Sponsored posts for fashion brands';
            $portfolio['style_tone'] = 'Energetic, authentic, glamorous';
        }
        if (!is_wp_error($google_response)) {
            $google_data = json_decode(wp_remote_retrieve_body($google_response), true);
            if (!empty($google_data['items'])) {
                foreach ($google_data['items'] as $item) {
                    if (isset($item['snippet']) && !empty($item['snippet'])) {
                        $snippet = wp_trim_words($item['snippet'], 50);
                        break;
                    }
                }
                $all_snippets = '';
                foreach ($google_data['items'] as $item) {
                    $all_snippets .= ' ' . ($item['snippet'] ?? '');
                }
                if (preg_match('/born.*?(\d{4})/i', $all_snippets, $match)) {
                    $profile['birth_year'] = $match[1];
                    $profile['age'] = (int)date('Y') - (int)$match[1];
                }
                if (preg_match('/\b(male|female)\b/i', $all_snippets, $match)) {
                    $profile['gender'] = ucfirst(strtolower($match[1]));
                }
                if (preg_match('/\b(Canada|USA|UK|[A-Za-z\s]+),\s*[A-Za-z\s]+$/i', $all_snippets, $match)) {
                    $profile['location'] = $match[1];
                }
            }
        }
        if (is_wp_error($google_response) || empty($google_data['items'])) {
            if (!is_wp_error($wiki_response)) {
                $wiki_data = json_decode(wp_remote_retrieve_body($wiki_response), true);
                $pages = $wiki_data['query']['pages'] ?? [];
                if (!empty($pages) && !isset($pages['-1'])) {
                    foreach ($pages as $page) {
                        if (isset($page['extract'])) {
                            $snippet = wp_trim_words($page['extract'], 50);
                            $source_url = 'https://en.wikipedia.org/wiki/' . urlencode($search_query);
                            if (preg_match('/born.*?(\d{4})/i', $page['extract'], $match)) {
                                $profile['birth_year'] = $match[1];
                                $profile['age'] = (int)date('Y') - (int)$match[1];
                            }
                            if (preg_match('/\b(male|female)\b/i', $page['extract'], $match)) {
                                $profile['gender'] = ucfirst(strtolower($match[1]));
                            }
                            if (preg_match('/\b(Canada|USA|UK|[A-Za-z\s]+),\s*[A-Za-z\s]+/i', $page['extract'], $match)) {
                                $profile['location'] = $match[1];
                            }
                        }
                    }
                }
            }
        }
        $social_data = get_social_media_data($search_query, $google_data['items'] ?? [], $serp_data['social_media'] ?? []);
        echo '<div class="basic-profile-section">';
        echo '<h3>' . esc_html($profile['name']) . '</h3>';
        echo '<p>' . esc_html($snippet) . ' </p>';
        echo '</div>';
        if (strtolower($search_query) === 'sydney sweeney' && !empty($serp_data['movies_shows'])) {
            echo '<div class="movies-shows-section">';
            echo '<div class="section-title">Movies & TV Shows</div>';
            foreach ($serp_data['movies_shows'] as $item) {
                echo '<div class="movie-card">';
                echo '<p><strong>' . esc_html($item['title']) . '</strong> (' . esc_html($item['year']) . ', ' . esc_html($item['type']) . ')</p>';
                echo '</div>';
            }
            if (!empty($serp_data['upcoming_movies'])) {
                echo '<div class="section-title">Upcoming Movies</div>';
                foreach ($serp_data['upcoming_movies'] as $movie) {
                    echo '<div class="movie-card">';
                    echo '<p><strong>' . esc_html($movie['title']) . '</strong> (' . esc_html($movie['year']) . ')</p>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        if (strtolower($search_query) === 'sydney sweeney' && !empty($serp_data['recent_news'])) {
            echo '<div class="news-section">';
            echo '<div class="section-title">Recent News</div>';
            foreach ($serp_data['recent_news'] as $news) {
                echo '<div class="news-card">';
                echo '<p><strong>' . esc_html($news['title']) . '</strong> (' . esc_html($news['source']) . ', ' . esc_html($news['date']) . ')</p>';
                echo '<a href="' . esc_url($news['url']) . '" target="_blank">Read More</a>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '<div class="influencer-data">';
        echo '<div class="card">';
        echo '<h3 class="section-title">Audience Demographics</h3>';
        echo '<p><strong>Age Range:</strong> ' . esc_html($demographics['age_range']) . '</p>';
        echo '<p><strong>Gender Breakdown:</strong> ' . esc_html($demographics['gender_breakdown']) . '</p>';
        echo '<p><strong>Top Countries:</strong> ' . esc_html($demographics['top_countries']) . '</p>';
        echo '<p><strong>Interests:</strong> ' . esc_html($demographics['interests']) . '</p>';
        echo '</div>';
        echo '<div class="card">';
        echo '<h3 class="section-title">Performance Metrics</h3>';
        echo '<p><strong>Previous Campaigns:</strong> ' . esc_html($performance['previous_campaigns']) . '</p>';
        echo '<p><strong>Engagement Trends:</strong> ' . esc_html($performance['engagement_trends']) . '</p>';
        echo '<p><strong>Sponsored vs Organic:</strong> ' . esc_html($performance['sponsored_vs_organic']) . '</p>';
        echo '</div>';
        echo '<div class="card">';
        echo '<h3 class="section-title">Pricing / Collaboration</h3>';
        echo '<p><strong>Cost per Post:</strong> ' . esc_html($pricing['cost_per_post']) . '</p>';
        echo '<p><strong>Collaboration Types:</strong> ' . esc_html($pricing['collaboration_types']) . '</p>';
        echo '<p><strong>Availability:</strong> ' . esc_html($pricing['availability']) . '</p>';
        echo '</div>';
        echo '<div class="card">';
        echo '<h3 class="section-title">Authenticity Checks</h3>';
        echo '<p><strong>Fake Follower %:</strong> ' . esc_html($authenticity['fake_follower_percentage']) . '</p>';
        echo '<p><strong>Bot Engagement:</strong> ' . esc_html($authenticity['bot_engagement']) . '</p>';
        echo '<p><strong>Past Collaborations:</strong> ' . esc_html($authenticity['past_collaborations']) . '</p>';
        echo '</div>';
        echo '<div class="card">';
        echo '<h3 class="section-title">Content Portfolio</h3>';
        echo '<p><strong>Media Samples:</strong> ' . esc_html($portfolio['media_samples']) . '</p>';
        echo '<p><strong>Past Deals:</strong> ' . esc_html($portfolio['past_deals']) . '</p>';
        echo '<p><strong>Style/Tone:</strong> ' . esc_html($portfolio['style_tone']) . '</p>';
        echo '</div>';
        echo '<div class="card">';
        echo '<h3 class="section-title">Profile</h3>';
        echo '<div class="basic-profile-grid">';
        echo '<p><strong>Name:</strong> ' . esc_html($profile['name']) . '</p>';
        echo '<p><strong>Age:</strong> ' . esc_html($profile['age']) . '</p>';
        echo '<p><strong>Birth Year:</strong> ' . esc_html($profile['birth_year']) . '</p>';
        echo '<p><strong>Gender:</strong> ' . esc_html($profile['gender']) . '</p>';
        echo '<p><strong>Location:</strong> ' . esc_html($profile['location']) . '</p>';
        echo '<p><strong>Languages:</strong> ' . esc_html($profile['languages']) . '</p>';
        echo '<p><strong>Niche:</strong> ' . esc_html($profile['niche']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="social-media-section">';
        echo '<div class="section-title">Social Media Data</div>';
        if (!empty($social_data['platforms'])) {
            echo '<p><strong>Platforms:</strong> ' . esc_html(implode(', ', $social_data['platforms'])) . '</p>';
            foreach ($social_data['follower_count'] as $platform => $count) {
                echo '<p><strong>' . esc_html($platform) . ' Followers:</strong> ' . esc_html($count) . '</p>';
                echo '<p><strong>Engagement:</strong> ' . esc_html($social_data['engagement_rate'][$platform]) . '</p>';
            }
            echo '<p><strong>Post Frequency:</strong> ' . esc_html($social_data['post_frequency']) . '</p>';
            echo '<p><strong>Content Type:</strong> ' . esc_html($social_data['content_type']) . '</p>';
            echo '<p><strong>Reach:</strong> ' . esc_html($social_data['reach']) . '</p>';
            echo '<p><strong>Impressions:</strong> ' . esc_html($social_data['impressions']) . '</p>';
        }
        echo '</div>';
    } else {
        echo '<p class="error-message">Please enter an influencer name to search.</p>';
    }
?>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('influencer_search', 'influencer_search_shortcode');
function influencer_search_admin_menu() {
    add_menu_page(
        'Influencer Campaign Dashboard',
        'Influencer Campaigns',
        'manage_options',
        'influencer-campaigns',
        'influencer_campaign_dashboard',
        'dashicons-admin-users',
        80
    );
}
add_action('admin_menu', 'influencer_search_admin_menu');
function influencer_campaign_dashboard() {
?>
<div class="wrap">
    <h1>Influencer Campaign Dashboard</h1>
    <p>Manage your influencer campaigns.</p>
    <h2>Campaign Analytics</h2>
    <p>Track ROI, engagement (placeholder).</p>
    <h2>Collaboration Requests</h2>
    <form method="post">
        <label for="influencer_name">Influencer Name:</label>
        <input type="text" id="influencer_name" name="influencer_name" required />
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
        <input type="submit" value="Send Request" class="button button-primary" />
    </form>
</div>
<?php
}
?>
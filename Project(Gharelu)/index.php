<?php
// index.php - Complete Paginated Version

// Define Nepal provinces
$provinces = [
    'Bagmati',
    'Gandaki',
    'Lumbini',
    'Sudurpashchim',
    'Karnali',
    'Koshi',
    'Madhesh'
];

// Define properties with province-based locations - EXPANDED WITH ALL PROVINCES
$properties = [
    // Bagmati Province
    [
        'id' => 1,
        'title' => 'Sunset Villa',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Stunning hillside retreat with panoramic city views and a resort-style pool. This luxurious villa features modern architecture, floor-to-ceiling windows, and a spacious terrace perfect for entertaining guests.',
        'rooms' => 4,
        'baths' => 3,
        'price' => 32000,
        'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 2,
        'title' => 'The Urban Loft',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Modern loft in the heart of Thamel, steps from restaurants and shops. This contemporary space features exposed brick walls, wooden flooring, and industrial-style lighting.',
        'rooms' => 2,
        'baths' => 2,
        'price' => 20000,
        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 3,
        'title' => 'Garden Cottage',
        'location' => 'Lalitpur',
        'province' => 'Bagmati',
        'description' => 'A charming craftsman home with a lush backyard garden and a quiet neighborhood feel. This cozy cottage offers a perfect blend of traditional architecture and modern amenities.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 25000,
        'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 4,
        'title' => 'Skyline Penthouse',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Floor-to-ceiling windows with breathtaking city views in the commercial hub. This penthouse offers an expansive living area, modern kitchen with premium appliances, and a private rooftop terrace.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 38000,
        'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 5,
        'title' => 'The Bungalow',
        'location' => 'Lalitpur',
        'province' => 'Bagmati',
        'description' => 'Modern bungalow in the upscale Jhamsikhel neighborhood, close to cafes and boutiques. This single-story home features an open floor plan, large windows for natural light, and a beautifully landscaped front yard.',
        'rooms' => 2,
        'baths' => 1,
        'price' => 22000,
        'image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 6,
        'title' => 'Highland Manor',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'A spacious home tucked away near the famous Boudhanath Stupa, offering peace and serenity. This traditional-style residence features large bedrooms, a modern kitchen, and a meditation room.',
        'rooms' => 4,
        'baths' => 3,
        'price' => 28000,
        'image' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 7,
        'title' => 'Riverside Retreat',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Peaceful apartment overlooking the river with a balcony and abundant natural light. This serene space offers a perfect escape from the city bustle while maintaining proximity to major attractions.',
        'rooms' => 2,
        'baths' => 2,
        'price' => 23000,
        'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 8,
        'title' => 'Heritage Home',
        'location' => 'Lalitpur',
        'province' => 'Bagmati',
        'description' => 'Beautifully restored traditional Newari home with modern amenities and authentic architecture. This heritage property features intricate wood carvings, a central courtyard, and spacious rooms.',
        'rooms' => 3,
        'baths' => 3,
        'price' => 30000,
        'image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 9,
        'title' => 'Green Valley Villa',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Spacious villa with mountain views, a swimming pool, and a well-maintained garden. This property offers the perfect blend of luxury and comfort with a modern design that maximizes the stunning valley views.',
        'rooms' => 5,
        'baths' => 4,
        'price' => 45000,
        'image' => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 10,
        'title' => 'Studio Haven',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Compact and functional studio apartment in the diplomatic quarter, ideal for singles or couples. This modern space features clever storage solutions, a fully equipped kitchenette, and a comfortable living area.',
        'rooms' => 1,
        'baths' => 1,
        'price' => 12000,
        'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 11,
        'title' => 'Mountain View Home',
        'location' => 'Bhaktapur',
        'province' => 'Bagmati',
        'description' => 'Stunning hillside home with unobstructed views of the Himalayan mountain range. This property offers a peaceful escape with its terraced gardens, outdoor seating areas, and large windows that frame the mountain views.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 29000,
        'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 12,
        'title' => 'City Center Apartment',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Modern apartment in the bustling city center with all amenities within walking distance. This well-maintained property features a spacious living room, modern kitchen, and balcony with city views.',
        'rooms' => 2,
        'baths' => 2,
        'price' => 21000,
        'image' => 'https://images.unsplash.com/photo-1583608205776-bfd35f0d9f83?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 13,
        'title' => 'Garden View Duplex',
        'location' => 'Lalitpur',
        'province' => 'Bagmati',
        'description' => 'Contemporary duplex with a private garden terrace and panoramic views. This stylish home features an open-plan living area, modern kitchen with island, and spacious bedrooms with ensuite bathrooms.',
        'rooms' => 3,
        'baths' => 3,
        'price' => 34000,
        'image' => 'https://images.unsplash.com/photo-1531834685032-c34bf0d84c77?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 14,
        'title' => 'Cozy Corner House',
        'location' => 'Lalitpur',
        'province' => 'Bagmati',
        'description' => 'A warm and inviting corner house with a beautiful front yard and friendly neighborhood. This home features a traditional design with a modern twist, including a fireplace, wooden flooring, and a sunroom.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 24000,
        'image' => 'https://images.unsplash.com/photo-1576941089067-2de3c901e126?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 15,
        'title' => 'Executive Suite',
        'location' => 'Kathmandu',
        'province' => 'Bagmati',
        'description' => 'Premium executive suite designed for professionals with a home office and high-speed internet. This sophisticated space includes a dedicated workspace, comfortable living area, and modern kitchen.',
        'rooms' => 2,
        'baths' => 2,
        'price' => 27000,
        'image' => 'https://images.unsplash.com/photo-1554995207-c18c203602cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Gandaki Province
    [
        'id' => 16,
        'title' => 'Lakeside Paradise',
        'location' => 'Pokhara',
        'province' => 'Gandaki',
        'description' => 'Beautiful property overlooking Phewa Lake with stunning mountain views. This peaceful retreat offers a perfect blend of nature and comfort with modern amenities.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 28000,
        'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 17,
        'title' => 'Mountain Lodge',
        'location' => 'Pokhara',
        'province' => 'Gandaki',
        'description' => 'Cozy lodge with direct views of the Annapurna range. This charming property features a fireplace, wooden interiors, and a garden perfect for relaxing after a day of adventure.',
        'rooms' => 2,
        'baths' => 1,
        'price' => 18000,
        'image' => 'https://images.unsplash.com/photo-1558609335-ca2a55a5c2f0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 18,
        'title' => 'Peaceful Valley Home',
        'location' => 'Pokhara',
        'province' => 'Gandaki',
        'description' => 'Spacious family home in a quiet neighborhood with mountain views. This property offers a large garden, modern kitchen, and ample living space for comfortable living.',
        'rooms' => 4,
        'baths' => 3,
        'price' => 35000,
        'image' => 'https://images.unsplash.com/photo-1567496898669-ee935f5f647a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Lumbini Province
    [
        'id' => 19,
        'title' => 'Heritage Haven',
        'location' => 'Lumbini',
        'province' => 'Lumbini',
        'description' => 'Traditional home near the sacred Lumbini gardens. This property combines cultural heritage with modern comfort, featuring spacious rooms and a peaceful courtyard.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 22000,
        'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 20,
        'title' => 'Garden Estate',
        'location' => 'Lumbini',
        'province' => 'Lumbini',
        'description' => 'Luxurious estate with extensive gardens and modern amenities. This property offers a serene environment with all the comforts of contemporary living.',
        'rooms' => 4,
        'baths' => 3,
        'price' => 32000,
        'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Sudurpashchim Province
    [
        'id' => 21,
        'title' => 'Far West Retreat',
        'location' => 'Dhangadhi',
        'province' => 'Sudurpashchim',
        'description' => 'Modern home in the emerging city of Dhangadhi. This property features contemporary design, spacious rooms, and a garden perfect for outdoor living.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 20000,
        'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Karnali Province
    [
        'id' => 22,
        'title' => 'Himalayan Haven',
        'location' => 'Surkhet',
        'province' => 'Karnali',
        'description' => 'Beautiful home in the heart of Surkhet valley. This property offers stunning views, a peaceful environment, and easy access to local amenities.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 20000,
        'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Koshi Province
    [
        'id' => 23,
        'title' => 'Eastern Pearl',
        'location' => 'Biratnagar',
        'province' => 'Koshi',
        'description' => 'Modern apartment in the commercial hub of eastern Nepal. This property features contemporary design, all amenities, and convenient access to shops and restaurants.',
        'rooms' => 2,
        'baths' => 2,
        'price' => 19000,
        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],

    // Madhesh Province
    [
        'id' => 24,
        'title' => 'Terai Comfort',
        'location' => 'Janakpur',
        'province' => 'Madhesh',
        'description' => 'Spacious home in the historic city of Janakpur. This property combines traditional architecture with modern comforts, featuring a large garden and comfortable living spaces.',
        'rooms' => 3,
        'baths' => 2,
        'price' => 18000,
        'image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ]
];

// Get search parameters from URL
$rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 0;
$province = isset($_GET['province']) ? trim($_GET['province']) : '';
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Filter properties based on search
$filtered_properties = array_filter($properties, function($property) use ($rooms, $province, $max_price) {
    $match = true;
    
    if ($rooms > 0 && $property['rooms'] < $rooms) {
        $match = false;
    }
    
    if ($province !== '' && stripos($property['province'], $province) === false) {
        $match = false;
    }
    
    if ($max_price > 0 && $property['price'] > $max_price) {
        $match = false;
    }
    
    return $match;
});

// Apply sorting
if ($sort_by === 'price_low') {
    usort($filtered_properties, function($a, $b) {
        return $a['price'] - $b['price'];
    });
} elseif ($sort_by === 'price_high') {
    usort($filtered_properties, function($a, $b) {
        return $b['price'] - $a['price'];
    });
} elseif ($sort_by === 'rooms') {
    usort($filtered_properties, function($a, $b) {
        return $b['rooms'] - $a['rooms'];
    });
}

// Pagination settings
$items_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($filtered_properties);
$total_pages = ceil($total_items / $items_per_page);
$offset = ($current_page - 1) * $items_per_page;
$paginated_properties = array_slice(array_values($filtered_properties), $offset, $items_per_page);

// Get selected property for detailed view (if any)
$selected_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$selected_property = null;
if ($selected_id > 0) {
    foreach ($properties as $prop) {
        if ($prop['id'] === $selected_id) {
            $selected_property = $prop;
            break;
        }
    }
}

// Determine which section to display
$section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gharelu - Find Your Perfect Home in Nepal</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php?section=home" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-home"></i>
                    <span>Gharelu</span>
                </a>
            </div>
            <div class="nav-links">
                <a href="index.php?section=home" class="<?php echo $section === 'home' ? 'active' : ''; ?>">Home</a>
                <a href="index.php?section=listings" class="<?php echo $section === 'listings' ? 'active' : ''; ?>">Listings</a>
                <a href="index.php?section=about" class="<?php echo $section === 'about' ? 'active' : ''; ?>">About</a>
                <a href="index.php?section=contact" class="<?php echo $section === 'contact' ? 'active' : ''; ?>">Contact</a>
            </div>
            <div class="nav-auth">
                <a href="signin.html" class="btn-outline">Sign In</a>
                <a href="signup.html" class="btn-primary">Sign Up</a>
            </div>
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <?php if ($section === 'home'): ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Finding your next home <br>should feel effortless.</h1>
                <p>Gharelu is your premium rental platform. Discover beautiful spaces, connect with trusted landlords, and move in with confidence.</p>
                
                <!-- Search Form -->
                <form class="search-form" action="index.php" method="GET">
                    <input type="hidden" name="section" value="listings">
                    <div class="search-grid">
                        <div class="search-field">
                            <label for="province_search"><i class="fas fa-map-marker-alt"></i> Province</label>
                            <select name="province" id="province_search" class="search-input">
                                <option value="">All Provinces</option>
                                <?php foreach($provinces as $prov): ?>
                                    <option value="<?php echo htmlspecialchars($prov); ?>" <?php echo ($province == $prov) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prov); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="rooms"><i class="fas fa-door-open"></i> Number of Rooms</label>
                            <select name="rooms" id="rooms" class="search-input">
                                <option value="0">Any Rooms</option>
                                <option value="1" <?php echo ($rooms == 1) ? 'selected' : ''; ?>>1+ Room</option>
                                <option value="2" <?php echo ($rooms == 2) ? 'selected' : ''; ?>>2+ Rooms</option>
                                <option value="3" <?php echo ($rooms == 3) ? 'selected' : ''; ?>>3+ Rooms</option>
                                <option value="4" <?php echo ($rooms == 4) ? 'selected' : ''; ?>>4+ Rooms</option>
                                <option value="5" <?php echo ($rooms == 5) ? 'selected' : ''; ?>>5+ Rooms</option>
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="max_price"><i class="fas fa-rupee-sign"></i> Max Price (NPR)</label>
                            <select name="max_price" id="max_price" class="search-input">
                                <option value="0">Any Price</option>
                                <option value="15000" <?php echo ($max_price == 15000) ? 'selected' : ''; ?>>Up to NPR 15,000</option>
                                <option value="20000" <?php echo ($max_price == 20000) ? 'selected' : ''; ?>>Up to NPR 20,000</option>
                                <option value="25000" <?php echo ($max_price == 25000) ? 'selected' : ''; ?>>Up to NPR 25,000</option>
                                <option value="30000" <?php echo ($max_price == 30000) ? 'selected' : ''; ?>>Up to NPR 30,000</option>
                                <option value="40000" <?php echo ($max_price == 40000) ? 'selected' : ''; ?>>Up to NPR 40,000</option>
                                <option value="50000" <?php echo ($max_price == 50000) ? 'selected' : ''; ?>>Up to NPR 50,000</option>
                            </select>
                        </div>
                        <div class="search-field">
                            <button type="submit" class="btn-search">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <?php if ($section === 'home'): ?>
                <!-- Featured Residences - Only show 6 properties -->
                <div class="featured">
                    <div class="section-header">
                        <h2>Featured Residences</h2>
                        <p>Hand-picked properties that meet our highest standards for quality, design, and location.</p>
                        <div class="result-count">
                            <?php echo count($properties); ?> properties available across Nepal
                        </div>
                    </div>
                    
                    <div class="properties-grid" id="propertiesGrid">
                        <?php 
                        $display_properties = array_slice($properties, 0, 6);
                        foreach($display_properties as $property): ?>
                            <div class="property-card" onclick="window.location.href='index.php?section=listings&view=<?php echo $property['id']; ?>'">
                                <div class="property-image">
                                    <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" loading="lazy">
                                    <span class="property-province"><?php echo htmlspecialchars($property['province']); ?></span>
                                </div>
                                <div class="property-details">
                                    <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['province']); ?>
                                    </div>
                                    <p class="property-description"><?php echo htmlspecialchars(substr($property['description'], 0, 80)) . '...'; ?></p>
                                    <div class="property-features">
                                        <span><i class="fas fa-door-open"></i> <?php echo $property['rooms']; ?> Rooms</span>
                                        <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> Baths</span>
                                        <span class="price"><i class="fas fa-rupee-sign"></i> NPR <?php echo number_format($property['price']); ?>/mo</span>
                                    </div>
                                    <button class="view-details-btn" onclick="event.stopPropagation(); window.location.href='index.php?section=listings&view=<?php echo $property['id']; ?>'">
                                        View Details <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="view-all-container">
                        <a href="index.php?section=listings" class="btn-primary view-all-btn">View All Properties</a>
                    </div>
                </div>

                <!-- How It Works -->
                <section class="how-it-works">
                    <div class="section-header">
                        <h2>How It Works</h2>
                        <p>Your journey to a new home simplified into three easy steps.</p>
                    </div>
                    <div class="steps-grid">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <i class="fas fa-search step-icon"></i>
                            <h3>Browse Listings</h3>
                            <p>Explore our curated collection of premium properties. Filter by province, price, and amenities to find your perfect match.</p>
                        </div>
                        <div class="step-card">
                            <div class="step-number">2</div>
                            <i class="fas fa-heart step-icon"></i>
                            <h3>Express Interest</h3>
                            <p>Save your favorites and directly contact verified landlords to schedule viewings or ask questions about the property.</p>
                        </div>
                        <div class="step-card">
                            <div class="step-number">3</div>
                            <i class="fas fa-key step-icon"></i>
                            <h3>Move In</h3>
                            <p>Finalize the details, sign securely, and step into your beautifully prepared new home.</p>
                        </div>
                    </div>
                </section>

                <!-- Platform Section -->
                <section class="platform">
                    <div class="platform-header">
                        <h2>A Platform Built for Everyone</h2>
                        <p>Gharelu creates a transparent ecosystem where every participant has the tools they need to succeed in the rental market.</p>
                    </div>
                    <div class="users-grid">
                        <div class="user-type">
                            <i class="fas fa-users"></i>
                            <h3>General Users</h3>
                            <p>Browse our entire catalog freely without committing to an account.</p>
                        </div>
                        <div class="user-type">
                            <i class="fas fa-user-check"></i>
                            <h3>Tenants</h3>
                            <p>Save favorites, set up alerts, and send direct interest to landlords.</p>
                        </div>
                        <div class="user-type">
                            <i class="fas fa-building"></i>
                            <h3>Landlords</h3>
                            <p>Post verified listings, manage inquiries, and find trustworthy tenants easily.</p>
                        </div>
                        <div class="user-type">
                            <i class="fas fa-shield-alt"></i>
                            <h3>Administrators</h3>
                            <p>Ensure quality control, verify listings, and maintain platform security.</p>
                        </div>
                    </div>
                </section>

                <!-- Why Choose Section -->
                <section class="why-choose">
                    <div class="section-header">
                        <h2>Why Choose Gharelu</h2>
                    </div>
                    <div class="features-grid">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <h3>Verified Listings Only</h3>
                            <p>Every property on our platform is manually verified to eliminate scams and ensure high quality.</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-star"></i>
                            <h3>Curated Experience</h3>
                            <p>We focus on premium, well-maintained properties that you would actually want to live in.</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-comments"></i>
                            <h3>Direct Communication</h3>
                            <p>Connect directly with landlords—no hidden fees or middleman agencies slowing you down.</p>
                        </div>
                    </div>
                </section>

            <?php elseif ($section === 'listings'): ?>
                <!-- Listings Section with Pagination -->
                <div class="listings-header">
                    <h2>All Properties</h2>
                    <p>Find your perfect home across Nepal's <?php echo count($provinces); ?> provinces</p>
                    
                    <!-- Search Form for Listings -->
                    <form class="search-form listings-search" action="index.php" method="GET">
                        <input type="hidden" name="section" value="listings">
                        <div class="search-grid">
                            <div class="search-field">
                                <label for="province_search"><i class="fas fa-map-marker-alt"></i> Province</label>
                                <select name="province" id="province_search" class="search-input">
                                    <option value="">All Provinces</option>
                                    <?php foreach($provinces as $prov): ?>
                                        <option value="<?php echo htmlspecialchars($prov); ?>" <?php echo ($province == $prov) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prov); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="rooms"><i class="fas fa-door-open"></i> Number of Rooms</label>
                                <select name="rooms" id="rooms" class="search-input">
                                    <option value="0">Any Rooms</option>
                                    <option value="1" <?php echo ($rooms == 1) ? 'selected' : ''; ?>>1+ Room</option>
                                    <option value="2" <?php echo ($rooms == 2) ? 'selected' : ''; ?>>2+ Rooms</option>
                                    <option value="3" <?php echo ($rooms == 3) ? 'selected' : ''; ?>>3+ Rooms</option>
                                    <option value="4" <?php echo ($rooms == 4) ? 'selected' : ''; ?>>4+ Rooms</option>
                                    <option value="5" <?php echo ($rooms == 5) ? 'selected' : ''; ?>>5+ Rooms</option>
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="max_price"><i class="fas fa-rupee-sign"></i> Max Price (NPR)</label>
                                <select name="max_price" id="max_price" class="search-input">
                                    <option value="0">Any Price</option>
                                    <option value="15000" <?php echo ($max_price == 15000) ? 'selected' : ''; ?>>Up to NPR 15,000</option>
                                    <option value="20000" <?php echo ($max_price == 20000) ? 'selected' : ''; ?>>Up to NPR 20,000</option>
                                    <option value="25000" <?php echo ($max_price == 25000) ? 'selected' : ''; ?>>Up to NPR 25,000</option>
                                    <option value="30000" <?php echo ($max_price == 30000) ? 'selected' : ''; ?>>Up to NPR 30,000</option>
                                    <option value="40000" <?php echo ($max_price == 40000) ? 'selected' : ''; ?>>Up to NPR 40,000</option>
                                    <option value="50000" <?php echo ($max_price == 50000) ? 'selected' : ''; ?>>Up to NPR 50,000</option>
                                </select>
                            </div>
                            <div class="search-field">
                                <button type="submit" class="btn-search">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Sort Options -->
                    <div class="sort-container">
                        <label for="sortSelect"><i class="fas fa-sort"></i> Sort by:</label>
                        <select id="sortSelect" name="sort" onchange="this.form.submit()" class="search-input sort-select">
                            <option value="newest" <?php echo ($sort_by === 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="price_low" <?php echo ($sort_by === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo ($sort_by === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rooms" <?php echo ($sort_by === 'rooms') ? 'selected' : ''; ?>>Most Rooms</option>
                        </select>
                    </div>
                </div>

                <?php if(count($paginated_properties) > 0): ?>
                    <div class="result-count">
                        <?php echo $total_items; ?> properties found
                        <?php if($total_pages > 1): ?>
                            <span class="pagination-info-text">(Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="properties-grid">
                        <?php foreach($paginated_properties as $property): ?>
                            <div class="property-card" onclick="window.location.href='index.php?section=listings&view=<?php echo $property['id']; ?>&page=<?php echo $current_page; ?>'">
                                <div class="property-image">
                                    <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" loading="lazy">
                                    <span class="property-province"><?php echo htmlspecialchars($property['province']); ?></span>
                                </div>
                                <div class="property-details">
                                    <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['province']); ?>
                                    </div>
                                    <p class="property-description"><?php echo htmlspecialchars(substr($property['description'], 0, 80)) . '...'; ?></p>
                                    <div class="property-features">
                                        <span><i class="fas fa-door-open"></i> <?php echo $property['rooms']; ?> Rooms</span>
                                        <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> Baths</span>
                                        <span class="price"><i class="fas fa-rupee-sign"></i> NPR <?php echo number_format($property['price']); ?>/mo</span>
                                    </div>
                                    <button class="view-details-btn" onclick="event.stopPropagation(); window.location.href='index.php?section=listings&view=<?php echo $property['id']; ?>&page=<?php echo $current_page; ?>'">
                                        View Details <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-container">
                            <div class="pagination">
                                <?php if ($current_page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="page-link">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                <?php endif; ?>
                                
                                <?php 
                                // Show limited page numbers with ellipsis
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);
                                
                                if ($start_page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="page-link">1</a>
                                    <?php if ($start_page > 2): ?>
                                        <span class="page-ellipsis">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                       class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <span class="page-ellipsis">...</span>
                                    <?php endif; ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="page-link">
                                        <?php echo $total_pages; ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="page-link">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="pagination-info">
                                Showing <?php echo $offset + 1; ?> - <?php echo min($offset + $items_per_page, $total_items); ?> 
                                of <?php echo $total_items; ?> properties
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-home"></i>
                        <h3>No properties found</h3>
                        <p>Try adjusting your search criteria to find more options across Nepal's provinces.</p>
                        <a href="index.php?section=listings" class="btn-primary">Clear Filters</a>
                    </div>
                <?php endif; ?>

            <?php elseif ($section === 'about'): ?>
                <!-- About Section -->
                <div class="about-section">
                    <h2>About Gharelu</h2>
                    <div class="about-content">
                        <div class="about-text">
                            <p>Gharelu is Nepal's premier rental platform dedicated to making the process of finding a home effortless and transparent. We believe that everyone deserves to find a space they can truly call home.</p>
                            <p>Our platform connects tenants with verified landlords across all seven provinces of Nepal, ensuring quality, trust, and convenience in every interaction.</p>
                            <h3>Our Mission</h3>
                            <p>To revolutionize the rental experience in Nepal by providing a secure, transparent, and user-friendly platform that benefits both tenants and landlords.</p>
                            <h3>Our Vision</h3>
                            <p>To become the most trusted rental platform in Nepal, setting the standard for quality, security, and customer satisfaction in the real estate market.</p>
                        </div>
                        <div class="about-image">
                            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Gharelu" loading="lazy">
                        </div>
                    </div>
                    <div class="about-stats">
                        <div class="stat">
                            <span class="stat-number"><?php echo count($properties); ?>+</span>
                            <span class="stat-label">Properties Listed</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number"><?php echo count($provinces); ?></span>
                            <span class="stat-label">Provinces Covered</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Verified Listings</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Customer Support</span>
                        </div>
                    </div>
                </div>

            <?php elseif ($section === 'contact'): ?>
                <!-- Contact Section - WORKING HOURS REMOVED -->
                <div class="contact-section">
                    <h2>Contact Us</h2>
                    <p>Have questions? We're here to help you find your perfect home.</p>
                    <div class="contact-grid">
                        <div class="contact-form-container">
                            <form class="contact-form" action="contact.php" method="POST">
                                <div class="form-group">
                                    <label for="contact_name">Full Name</label>
                                    <input type="text" id="contact_name" name="name" required placeholder="Enter your full name">
                                </div>
                                <div class="form-group">
                                    <label for="contact_email">Email Address</label>
                                    <input type="email" id="contact_email" name="email" required placeholder="Enter your email address">
                                </div>
                                <div class="form-group">
                                    <label for="contact_subject">Subject</label>
                                    <input type="text" id="contact_subject" name="subject" required placeholder="What is this about?">
                                </div>
                                <div class="form-group">
                                    <label for="contact_message">Message</label>
                                    <textarea id="contact_message" name="message" rows="5" required placeholder="Tell us how we can help you..."></textarea>
                                </div>
                                <button type="submit" class="btn-primary contact-submit">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </form>
                        </div>
                        <div class="contact-info">
                            <div class="contact-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h4>Address</h4>
                                    <p>Kathmandu, Nepal</p>
                                </div>
                            </div>
                            <div class="contact-detail">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h4>Phone</h4>
                                    <p>+977-1-555-1234</p>
                                </div>
                            </div>
                            <div class="contact-detail">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h4>Email</h4>
                                    <p>info@gharelu.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Property Modal -->
    <?php if ($selected_property): ?>
    <div id="propertyModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php echo htmlspecialchars($selected_property['title']); ?></h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-image">
                    <img src="<?php echo htmlspecialchars($selected_property['image']); ?>" alt="<?php echo htmlspecialchars($selected_property['title']); ?>">
                </div>
                <div class="modal-details">
                    <div class="modal-location">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($selected_property['location']); ?>, <?php echo htmlspecialchars($selected_property['province']); ?>
                    </div>
                    <div class="modal-description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($selected_property['description']); ?></p>
                    </div>
                    <div class="modal-features">
                        <span><i class="fas fa-door-open"></i> <?php echo $selected_property['rooms']; ?> Rooms</span>
                        <span><i class="fas fa-bath"></i> <?php echo $selected_property['baths']; ?> Bathrooms</span>
                        <span class="modal-price"><i class="fas fa-rupee-sign"></i> NPR <?php echo number_format($selected_property['price']); ?>/month</span>
                    </div>
                    <div class="modal-actions">
                        <a href="contact.html" class="btn-primary">
                            <i class="fas fa-phone-alt"></i> Contact Landlord
                        </a>
                        <button class="btn-outline" onclick="closeModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php?section=home" style="text-decoration:none;color:inherit;">
                        <i class="fas fa-home"></i>
                        <h3>Gharelu</h3>
                    </a>
                    <p>Your premium destination for finding beautiful homes and trustworthy landlords across Nepal. Making renting effortless and reliable.</p>
                </div>
                <div class="footer-links">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="index.php?section=listings">Browse Listings</a></li>
                        <li><a href="index.php?section=about">How It Works</a></li>
                        <li><a href="index.php?section=about">About Us</a></li>
                        <li><a href="index.php?section=contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="index.php?section=about">About Us</a></li>
                        <li><a href="index.php?section=contact">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Connect With Us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Gharelu. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
    // Modal functions
    function closeModal() {
        const url = new URL(window.location.href);
        url.searchParams.delete('view');
        window.location.href = url.toString();
    }

    // Close modal on overlay click
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('propertyModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }
    });
    </script>
</body>
</html>
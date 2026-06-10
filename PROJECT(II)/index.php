<?php

$properties = [
    [
        'id' => 1,
        'title' => 'Sunset Villa',
        'location' => 'Buddhanagar, Kathmandu',
        'description' => 'Stunning hillside retreat with panoramic city views and a resort-style pool.',
        'beds' => 4,
        'baths' => 3,
        'price' => 45000,
        'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 2,
        'title' => 'The Urban Loft',
        'location' => 'Thamel, Kathmandu',
        'description' => 'Modern loft in the heart of Thamel, steps from restaurants and shops.',
        'beds' => 2,
        'baths' => 2,
        'price' => 28000,
        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 3,
        'title' => 'Garden Cottage',
        'location' => 'Lalitpur, Kathmandu',
        'description' => 'A charming craftsman home with a lush backyard garden and a quiet neighborhood feel.',
        'beds' => 3,
        'baths' => 2,
        'price' => 35000,
        'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 4,
        'title' => 'Skyline Penthouse',
        'location' => 'New Baneshwor, Kathmandu',
        'description' => 'Floor-to-ceiling windows with breathtaking city views in the commercial hub.',
        'beds' => 3,
        'baths' => 2,
        'price' => 52000,
        'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 5,
        'title' => 'The Bungalow',
        'location' => 'Jhamsikhel, Lalitpur',
        'description' => 'Modern bungalow in the upscale Jhamsikhel neighborhood, close to cafes and boutiques.',
        'beds' => 2,
        'baths' => 1,
        'price' => 31000,
        'image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 6,
        'title' => 'Highland Manor',
        'location' => 'Boudha, Kathmandu',
        'description' => 'A spacious home tucked away near the famous Boudhanath Stupa, offering peace and serenity.',
        'beds' => 4,
        'baths' => 3,
        'price' => 38000,
        'image' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ]
];

// Get search parameters from URL
$rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 0;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

// Filter properties based on search
$filtered_properties = array_filter($properties, function($property) use ($rooms, $location, $max_price) {
    $match = true;
    
    if ($rooms > 0 && $property['beds'] < $rooms) {
        $match = false;
    }
    
    if ($location !== '' && stripos($property['location'], $location) === false) {
        $match = false;
    }
    
    if ($max_price > 0 && $property['price'] > $max_price) {
        $match = false;
    }
    
    return $match;
});

// Define locations for dropdown
$locations = [
    'Kathmandu', 'Lalitpur', 'Bhaktapur', 'Boudha', 'Thamel', 
    'New Baneshwor', 'Jhamsikhel', 'Buddhanagar'
];

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gharelu - Find Your Perfect Home in Kathmandu</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-home"></i>
                <span>Gharelu</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="#">Listings</a>
                <a href="#">About</a>
                <a href="#">Contact</a>
            </div>
            <div class="nav-auth">
                <button class="btn-outline" id="signInBtn">Sign In</button>
                <button class="btn-primary" id="signUpBtn">Sign Up</button>
            </div>
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Finding your next home <br>should feel effortless.</h1>
                <p>Gharelu is your premium rental platform. Discover beautiful spaces, connect with trusted landlords, and move in with confidence.</p>
                
                <!-- Search Form -->
                <form class="search-form" action="index.php" method="GET">
                    <div class="search-grid">
                        <div class="search-field">
                            <label for="location_search"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <select name="location" id="location_search" class="search-input">
                                <option value="">All Locations</option>
                                <?php foreach($locations as $loc): ?>
                                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo ($location == $loc) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="rooms"><i class="fas fa-bed"></i> Number of Rooms</label>
                            <select name="rooms" id="rooms" class="search-input">
                                <option value="0">Any Rooms</option>
                                <option value="1" <?php echo ($rooms == 1) ? 'selected' : ''; ?>>1+ Room</option>
                                <option value="2" <?php echo ($rooms == 2) ? 'selected' : ''; ?>>2+ Rooms</option>
                                <option value="3" <?php echo ($rooms == 3) ? 'selected' : ''; ?>>3+ Rooms</option>
                                <option value="4" <?php echo ($rooms == 4) ? 'selected' : ''; ?>>4+ Rooms</option>
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="max_price"><i class="fas fa-rupee-sign"></i> Max Price (NPR)</label>
                            <select name="max_price" id="max_price" class="search-input">
                                <option value="0">Any Price</option>
                                <option value="15000" <?php echo ($max_price == 25000) ? 'selected' : ''; ?>>Up to NPR 25,000</option>
                                <option value="25000" <?php echo ($max_price == 35000) ? 'selected' : ''; ?>>Up to NPR 35,000</option>
                                <option value="15000" <?php echo ($max_price == 45000) ? 'selected' : ''; ?>>Up to NPR 45,000</option>
                                <option value="30000" <?php echo ($max_price == 60000) ? 'selected' : ''; ?>>Up to NPR 60,000</option>
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

    <!-- Featured Residences -->
    <section class="featured">
        <div class="container">
            <div class="section-header">
                <h2>Featured Residences</h2>
                <p>Hand-picked properties that meet our highest standards for quality, design, and location.</p>
            </div>
            
            <?php if(count($filtered_properties) > 0): ?>
                <div class="properties-grid" id="propertiesGrid">
                    <?php foreach($filtered_properties as $property): ?>
                        <div class="property-card">
                            <div class="property-image">
                                <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                            </div>
                            <div class="property-details">
                                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?>
                                </div>
                                <p class="property-description"><?php echo htmlspecialchars($property['description']); ?></p>
                                <div class="property-features">
                                    <span><i class="fas fa-bed"></i> <?php echo $property['beds']; ?> Beds</span>
                                    <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> Baths</span>
                                    <span class="price"><i class="fas fa-rupee-sign"></i> NPR <?php echo number_format($property['price']); ?>/mo</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-home"></i>
                    <h3>No properties found</h3>
                    <p>Try adjusting your search criteria to find more options in Kathmandu.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Your journey to a new home simplified into three easy steps.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <i class="fas fa-search step-icon"></i>
                    <h3>Browse Listings</h3>
                    <p>Explore our curated collection of premium properties. Filter by location, price, and amenities to find your perfect match.</p>
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
        </div>
    </section>

    <!-- Platform Section -->
    <section class="platform">
        <div class="container">
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
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="why-choose">
        <div class="container">
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
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <i class="fas fa-home"></i>
                    <h3>Gharelu</h3>
                    <p>Your premium destination for finding beautiful homes and trustworthy landlords. Making renting effortless and reliable.</p>
                </div>
                <div class="footer-links">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="#">Browse Listings</a></li>
                        <li><a href="#">How It Works</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Trust & Safety</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
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
</body>
</html>

<?php include 'footer.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landlord Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="logo"><span>Rent<em>Hub</em></span></div>

  <div class="user-info">
    <div class="av">R</div>
    <div>
      <div class="name">Ram Sharma</div>
      <div class="role">Landlord</div>
    </div>
  </div>

  <nav>
    <button class="nav-link active" onclick="switchTab('overview', this)">📊 Overview</button>
    <button class="nav-link" onclick="switchTab('listings', this)">🏘️ My Listings <span class="n-badge">3</span></button>
    <button class="nav-link" onclick="switchTab('add', this)">➕ Add Property</button>
    <button class="nav-link" onclick="switchTab('requests', this)">📩 Requests <span class="n-badge">2</span></button>
  </nav>

  <div class="sidebar-bottom">
    <button class="nav-link">🚪 Logout</button>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <h2 id="page-title">Overview</h2>
    <div class="topbar-right">
      <span class="notif">🔔 2 pending requests</span>
    </div>
  </header>

  <div class="content">

    <!-- OVERVIEW -->
    <div class="tab-pane active" id="tab-overview">
      <div class="stats">
        <div class="stat">
          <div class="stat-icon ic-o">🏘️</div>
          <div><div class="stat-num">3</div><div class="stat-label">Total Listings</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-g">✅</div>
          <div><div class="stat-num">2</div><div class="stat-label">Available</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-b">🔑</div>
          <div><div class="stat-num">1</div><div class="stat-label">Rented</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-r">📩</div>
          <div><div class="stat-num">2</div><div class="stat-label">Pending Requests</div></div>
        </div>
      </div>

      <div class="two-col">
        <div class="panel">
          <h4>Recent Listings</h4>
          <div class="mini-row"><div><div class="mr-name">2BHK Flat - Lalitpur</div><div class="mr-sub">📍 Lalitpur</div></div><span class="badge badge-available">Available</span></div>
          <div class="mini-row"><div><div class="mr-name">Studio Room - Thamel</div><div class="mr-sub">📍 Kathmandu</div></div><span class="badge badge-rented">Rented</span></div>
          <div class="mini-row"><div><div class="mr-name">3BHK House - Bhaktapur</div><div class="mr-sub">📍 Bhaktapur</div></div><span class="badge badge-available">Available</span></div>
        </div>
        <div class="panel">
          <h4>Recent Requests</h4>
          <div class="mini-row"><div><div class="mr-name">Sita Thapa</div><div class="mr-sub">2BHK Flat - Lalitpur</div></div><span class="badge badge-pending">Pending</span></div>
          <div class="mini-row"><div><div class="mr-name">Bikash Rai</div><div class="mr-sub">3BHK House - Bhaktapur</div></div><span class="badge badge-pending">Pending</span></div>
          <div class="mini-row"><div><div class="mr-name">Anita KC</div><div class="mr-sub">Studio Room - Thamel</div></div><span class="badge badge-accepted">Accepted</span></div>
        </div>
      </div>
    </div>

    <!-- LISTINGS -->
    <div class="tab-pane" id="tab-listings">
      <div class="sh">
        <h3>My Listings</h3>
        <button class="btn btn-primary" onclick="switchTab('add', document.querySelector('[onclick*=add]'))">➕ Add Property</button>
      </div>

      <div class="filters">
        <button class="fbtn active" onclick="filterListings('all', this)">All (3)</button>
        <button class="fbtn" onclick="filterListings('available', this)">Available (2)</button>
        <button class="fbtn" onclick="filterListings('pending', this)">Pending (0)</button>
        <button class="fbtn" onclick="filterListings('rented', this)">Rented (1)</button>
      </div>

      <div class="listings-grid">
        <!-- Card 1 -->
        <div class="prop-card" data-status="available">
          <div class="prop-img">🏠<span class="badge badge-available">Available</span></div>
          <div class="prop-body">
            <div class="prop-title">2BHK Flat – Lalitpur</div>
            <div class="prop-loc">📍 Lalitpur, Kathmandu</div>
            <div class="prop-meta"><span>🛏 2 Bed</span><span>🚿 1 Bath</span></div>
            <div class="prop-price">Rs 25,000 <small>/ month</small></div>
            <div class="prop-footer">
              <select><option>🟢 Available</option><option>🟡 Pending</option><option>🔵 Rented</option></select>
              <div class="prop-actions">
                <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px">✏️</button>
                <button class="btn btn-danger" style="padding:5px 10px;font-size:11px">🗑</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="prop-card" data-status="rented">
          <div class="prop-img">🏢<span class="badge badge-rented">Rented</span></div>
          <div class="prop-body">
            <div class="prop-title">Studio Room – Thamel</div>
            <div class="prop-loc">📍 Thamel, Kathmandu</div>
            <div class="prop-meta"><span>🛏 1 Bed</span><span>🚿 1 Bath</span></div>
            <div class="prop-price">Rs 15,000 <small>/ month</small></div>
            <div class="prop-footer">
              <select><option>🟢 Available</option><option>🟡 Pending</option><option selected>🔵 Rented</option></select>
              <div class="prop-actions">
                <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px">✏️</button>
                <button class="btn btn-danger" style="padding:5px 10px;font-size:11px">🗑</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="prop-card" data-status="available">
          <div class="prop-img">🏡<span class="badge badge-available">Available</span></div>
          <div class="prop-body">
            <div class="prop-title">3BHK House – Bhaktapur</div>
            <div class="prop-loc">📍 Bhaktapur</div>
            <div class="prop-meta"><span>🛏 3 Bed</span><span>🚿 2 Bath</span></div>
            <div class="prop-price">Rs 40,000 <small>/ month</small></div>
            <div class="prop-footer">
              <select><option>🟢 Available</option><option>🟡 Pending</option><option>🔵 Rented</option></select>
              <div class="prop-actions">
                <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px">✏️</button>
                <button class="btn btn-danger" style="padding:5px 10px;font-size:11px">🗑</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ADD PROPERTY -->
    <div class="tab-pane" id="tab-add">
      <div class="sh"><h3>Add New Property</h3></div>
      <div class="form-card">
        <div class="form-grid">
          <div class="fg full"><label>Title *</label><input type="text" placeholder="e.g. 2BHK Apartment in Lalitpur"></div>
          <div class="fg"><label>Location *</label><input type="text" placeholder="e.g. Lalitpur"></div>
          <div class="fg"><label>Type</label>
            <select><option>Apartment</option><option>House</option><option>Room</option><option>Flat</option><option>Studio</option></select>
          </div>
          <div class="fg"><label>Monthly Rent (Rs) *</label><input type="number" placeholder="25000"></div>
          <div class="fg"><label>Image</label><input type="file" accept="image/*"></div>
          <div class="fg"><label>Bedrooms</label><input type="number" placeholder="2" min="0"></div>
          <div class="fg"><label>Bathrooms</label><input type="number" placeholder="1" min="0"></div>
          <div class="fg full"><label>Amenities</label><input type="text" placeholder="WiFi, Parking, Water, Generator"></div>
          <div class="fg full"><label>Description</label><textarea placeholder="Describe the property..."></textarea></div>
        </div>
        <br>
        <button class="btn btn-primary">🏠 Publish Listing</button>
      </div>
    </div>

    <!-- REQUESTS -->
    <div class="tab-pane" id="tab-requests">
      <div class="sh"><h3>Tenant Requests</h3></div>
      <div class="req-list">

        <div class="req-card">
          <div>
            <div class="req-prop">🏠 2BHK Flat – Lalitpur</div>
            <div class="req-name">Sita Thapa</div>
            <div class="req-contact">
              <a href="mailto:sita@email.com">✉️ sita@email.com</a>
              <a href="tel:9841000001">📞 9841000001</a>
            </div>
            <div class="req-msg">"I am interested in renting this property from next month."</div>
            <div class="req-date">🕐 25 May 2026, 10:30 AM</div>
          </div>
          <div class="req-actions">
            <span class="badge badge-pending">Pending</span>
            <button class="btn btn-success">✅ Accept</button>
            <button class="btn btn-danger">❌ Reject</button>
          </div>
        </div>

        <div class="req-card">
          <div>
            <div class="req-prop">🏡 3BHK House – Bhaktapur</div>
            <div class="req-name">Bikash Rai</div>
            <div class="req-contact">
              <a href="mailto:bikash@email.com">✉️ bikash@email.com</a>
              <a href="tel:9841000002">📞 9841000002</a>
            </div>
            <div class="req-msg">"Looking for a family home. Can we schedule a visit?"</div>
            <div class="req-date">🕐 24 May 2026, 03:15 PM</div>
          </div>
          <div class="req-actions">
            <span class="badge badge-pending">Pending</span>
            <button class="btn btn-success">✅ Accept</button>
            <button class="btn btn-danger">❌ Reject</button>
          </div>
        </div>

        <div class="req-card">
          <div>
            <div class="req-prop">🏢 Studio Room – Thamel</div>
            <div class="req-name">Anita KC</div>
            <div class="req-contact">
              <a href="mailto:anita@email.com">✉️ anita@email.com</a>
            </div>
            <div class="req-msg">"Great location for me. I would like to confirm."</div>
            <div class="req-date">🕐 20 May 2026, 11:00 AM</div>
          </div>
          <div class="req-actions">
            <span class="badge badge-accepted">Accepted</span>
            <button class="btn btn-danger">❌ Reject</button>
            <button class="btn btn-ghost">↩ Reset</button>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script src="script.js"></script>
</body>
</html>
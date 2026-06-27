const tabTitles = { overview:'Overview', listings:'My Listings', add:'Add Property', requests:'Tenant Requests' };

function switchTab(name, el) {
  document.querySelectorAll('.tab-pane').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  if (el) el.classList.add('active');
  document.getElementById('page-title').textContent = tabTitles[name];
}

function filterListings(status, btn) {
  document.querySelectorAll('.fbtn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.prop-card').forEach(c => {
    c.style.display = (status === 'all' || c.dataset.status === status) ? '' : 'none';
  });
}
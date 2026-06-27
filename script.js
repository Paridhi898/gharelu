function switchTab(tab, button) {
  const panes = document.querySelectorAll('.tab-pane');
  panes.forEach((pane) => pane.classList.remove('active'));

  const links = document.querySelectorAll('.nav-link');
  links.forEach((link) => link.classList.remove('active'));

  document.getElementById(`tab-${tab}`).classList.add('active');
  button.classList.add('active');

  const title = { overview: 'Overview', listings: 'My Listings', add: 'Add Property', requests: 'Requests' };
  document.getElementById('page-title').textContent = title[tab] || 'Dashboard';
}

function filterListings(filter, button) {
  const cards = document.querySelectorAll('.prop-card');
  cards.forEach((card) => {
    if (filter === 'all' || card.dataset.status === filter) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });

  const buttons = document.querySelectorAll('.fbtn');
  buttons.forEach((btn) => btn.classList.remove('active'));
  button.classList.add('active');
}

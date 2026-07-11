// Category filter for homepage
function filterCategory(el, category) {
    document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.event-card').forEach(card => {
        card.style.display = (!category || card.dataset.category === category) ? 'block' : 'none';
    });
}

// Ticket selection
let selectedPrice = 0;
let selectedLeft  = 0;
let qty = 1;

function selectTicket(el) {
    document.querySelectorAll('.ticket-option').forEach(t => t.classList.remove('selected'));
    el.classList.add('selected');
    selectedPrice = parseFloat(el.dataset.price);
    selectedLeft  = parseInt(el.dataset.left);
    qty = 1;
    document.getElementById('qty-display').textContent = qty;
    if (document.getElementById('selected-ticket-id')) {
        document.getElementById('selected-ticket-id').value = el.dataset.id;
    }
    if (document.getElementById('selected-qty')) {
        document.getElementById('selected-qty').value = qty;
    }
    updateTotal();
    document.getElementById('booking-summary').style.display = 'block';
}

function changeQty(delta) {
    qty = Math.max(1, Math.min(selectedLeft, qty + delta));
    document.getElementById('qty-display').textContent = qty;
    if (document.getElementById('selected-qty')) {
        document.getElementById('selected-qty').value = qty;
    }
    updateTotal();
}

function updateTotal() {
    const total = selectedPrice * qty;
    document.getElementById('total-display').textContent =
        selectedPrice === 0 ? 'Free' : '৳ ' + total.toLocaleString();
}

// ── AJAX LIVE SEARCH WITH ASYNC/AWAIT ──
const icons = {
    Concert:    'fa-music',
    Workshop:   'fa-screwdriver-wrench',
    Conference: 'fa-briefcase',
    Food:       'fa-utensils',
    Sports:     'fa-futbol',
    Comedy:     'fa-face-laugh',
    Tech:       'fa-laptop-code',
    Art:        'fa-palette',
};

// Fetch events from API using async/await
const fetchEvents = async (params = {}) => {
    const query  = new URLSearchParams(params).toString();
    const url    = `/api/v1/events${query ? '?' + query : ''}`;

    const response = await fetch(url);
    if (!response.ok) throw new Error('API request failed');
    const data = await response.json();
    return data.data;
};

// Build event card HTML using DOM manipulation
const buildEventCard = (event) => {
    const icon      = icons[event.category] ?? 'fa-calendar-days';
    const isFree    = parseFloat(event.min_price) === 0;
    const seatsLeft = event.available_seats;
    const soldOut   = seatsLeft <= 0;

    // Create card element using DOM
    const card = document.createElement('div');
    card.className   = 'event-card';
    card.dataset.category = event.category;

    // Badge
    let badgeHtml = '';
    if (soldOut) {
        badgeHtml = '<span class="badge badge-danger">Sold Out</span>';
    } else if (seatsLeft <= 10) {
        badgeHtml = `<span class="badge badge-warning">Only ${seatsLeft} left</span>`;
    } else {
        badgeHtml = `<span class="badge badge-success">${seatsLeft} seats</span>`;
    }

    // Price
    const priceHtml = isFree
        ? '<span class="price-tag free"><i class="fa-solid fa-gift"></i> Free</span>'
        : `<span class="price-tag"><i class="fa-solid fa-tag"></i> ৳${Number(event.min_price).toLocaleString()}</span>`;

    // Button
    const btnHtml = soldOut
        ? '<button class="btn btn-sm" disabled style="opacity:0.5;">Sold Out</button>'
        : `<a href="/events/${event.id}" class="btn btn-primary btn-sm"><i class="fa-solid fa-ticket"></i> Book Now</a>`;

    card.innerHTML = `
        <div class="card-img">
            ${event.banner_image
                ? `<img src="/images/${event.banner_image}" style="width:100%;height:100%;object-fit:cover;" alt="${event.title}">`
                : `<i class="fa-solid ${icon}"></i>`
            }
        </div>
        <div class="card-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
                <div class="card-category">
                    <i class="fa-solid ${icon}"></i> ${event.category}
                </div>
                ${badgeHtml}
            </div>
            <div class="card-title">${event.title}</div>
            <div class="card-meta">
                <span><i class="fa-regular fa-calendar"></i> ${event.event_date_human}</span>
                <span><i class="fa-solid fa-location-dot"></i> ${event.venue}, ${event.city}</span>
                <span><i class="fa-regular fa-user"></i> By ${event.organizer}</span>
            </div>
            <div class="card-footer">
                ${priceHtml}
                ${btnHtml}
            </div>
        </div>
    `;

    return card;
};

// Render events into grid using DOM
const renderEvents = (events, container) => {
    // DOM manipulation — clear existing cards
    container.innerHTML = '';

    if (!events || events.length === 0) {
        container.innerHTML = `
            <div style="text-align:center; padding:3rem; color:var(--text-muted); grid-column:1/-1;">
                <i class="fa-solid fa-calendar-xmark" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
                <p>No events found. Try a different search.</p>
            </div>`;
        return;
    }

    // DOM manipulation — append each card
    events.forEach(event => container.appendChild(buildEventCard(event)));
};

// Debounce — prevents too many API calls while typing
const debounce = (fn, delay) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};

// Initialize live search on events page
const initLiveSearch = () => {
    const searchInput = document.getElementById('live-search-input');
    const grid        = document.getElementById('live-events-grid');
    const counter     = document.getElementById('events-counter');
    const loader      = document.getElementById('search-loader');

    if (!searchInput || !grid) return;

    const doSearch = debounce(async () => {
        const search   = searchInput.value.trim();
        const category = document.getElementById('live-category')?.value || '';
        const city     = document.getElementById('live-city')?.value || '';

        // Show loader — DOM manipulation
        if (loader) loader.style.display = 'inline-block';

        try {
            // AJAX call using async/await — Event Loop in action
            const events = await fetchEvents({ search, category, city });

            // Update DOM with results
            renderEvents(events, grid);

            // Update counter — DOM manipulation
            if (counter) counter.textContent = `${events.length} event${events.length !== 1 ? 's' : ''} found`;

        } catch (error) {
            console.error('Search failed:', error);
            grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; color:var(--danger); padding:2rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Search failed. Please try again.
            </div>`;
        } finally {
            // Hide loader — DOM manipulation
            if (loader) loader.style.display = 'none';
        }
    }, 400);

    // Event listeners
    searchInput.addEventListener('input', doSearch);
    document.getElementById('live-category')?.addEventListener('change', doSearch);
    document.getElementById('live-city')?.addEventListener('change', doSearch);
};

// Run when DOM is ready — Event Loop
document.addEventListener('DOMContentLoaded', () => {
    initLiveSearch();
});
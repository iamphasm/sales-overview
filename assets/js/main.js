document.addEventListener('DOMContentLoaded', function () {

    // ── Scrape Info ───────────────────────────────────────────────
    const scrapeBtn = document.getElementById('scrape-btn');
    if (scrapeBtn) {
        scrapeBtn.addEventListener('click', async function () {
            const url = document.getElementById('link').value.trim();
            if (!url) { alert('Please enter an auction URL first.'); return; }

            const loading = document.getElementById('scrape-loading');
            loading.classList.remove('hidden');
            scrapeBtn.disabled = true;

            try {
                const res  = await fetch('api/scrape.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ url }),
                });
                const data = await res.json();
                if (data.success && data.data) {
                    const d = data.data;
                    if (d.title)           document.getElementById('title').value = d.title;
                    if (d.production_year) document.getElementById('production_year').value = d.production_year;
                } else if (data.error) {
                    alert('Could not scrape: ' + data.error + '\nPlease fill in manually.');
                }
            } catch {
                alert('Network error while scraping. Please fill in manually.');
            } finally {
                loading.classList.add('hidden');
                scrapeBtn.disabled = false;
            }
        });
    }

    // ── Add sale form ─────────────────────────────────────────────
    const addForm = document.getElementById('add-auction-form');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const payload = {
                link:            document.getElementById('link').value,
                production_year: document.getElementById('production_year').value,
                title:           document.getElementById('title').value,
                category:        document.getElementById('category').value,
                date_added:      document.getElementById('date_added').value,
                finish_date:     document.getElementById('finish_date').value,
                investment_cost: document.getElementById('investment_cost').value,
                sold:            document.getElementById('sold')?.checked ?? false,
            };

            const msg = document.getElementById('form-message');
            const btn = addForm.querySelector('button[type="submit"]');
            btn.disabled = true;

            try {
                const res  = await fetch('api/add_auction.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    msg.className = 'form-message success';
                    msg.textContent = 'Sale added! Redirecting…';
                    msg.classList.remove('hidden');
                    const dest = payload.sold ? '?page=finished' : '?page=live';
                    setTimeout(() => { window.location.href = dest; }, 1200);
                } else {
                    msg.className = 'form-message error';
                    msg.textContent = data.error || 'Failed to add sale.';
                    msg.classList.remove('hidden');
                    btn.disabled = false;
                }
            } catch {
                msg.className = 'form-message error';
                msg.textContent = 'Network error. Please try again.';
                msg.classList.remove('hidden');
                btn.disabled = false;
            }
        });
    }

    // ── Final price modal (Finished Sales) ────────────────────────
    const modal = document.getElementById('price-modal');
    if (modal) {
        function openModal(id, price) {
            document.getElementById('modal-auction-id').value = id;
            document.getElementById('modal-price').value = price || '';
            modal.classList.remove('hidden');
            document.getElementById('modal-price').focus();
        }

        function closeModal() { modal.classList.add('hidden'); }

        document.querySelectorAll('.add-price-btn, .edit-price').forEach(btn => {
            btn.addEventListener('click', function () {
                openModal(this.dataset.id, this.dataset.price || '');
            });
        });

        document.getElementById('modal-cancel').addEventListener('click', closeModal);
        modal.querySelector('.modal-backdrop').addEventListener('click', closeModal);

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });

        document.getElementById('modal-save').addEventListener('click', async function () {
            const id    = document.getElementById('modal-auction-id').value;
            const price = document.getElementById('modal-price').value;

            if (price === '' || isNaN(parseFloat(price)) || parseFloat(price) < 0) {
                alert('Please enter a valid price.');
                return;
            }

            this.disabled = true;
            try {
                const res  = await fetch('api/update_sale.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, final_price: parseFloat(price) }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to save price. Please try again.');
                    this.disabled = false;
                }
            } catch {
                alert('Network error. Please try again.');
                this.disabled = false;
            }
        });
    }

    // ── Statistics chart ──────────────────────────────────────────
    const canvas = document.getElementById('earningsChart');
    if (canvas && typeof auctionData !== 'undefined') {
        let chart = null;

        function buildChart(period) {
            const labels  = [];
            const roiData = [];
            const revenue = [];
            const now     = new Date();

            function monthKey(d) {
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            }

            function monthLabel(d) {
                return d.toLocaleString('nb-NO', { month: 'short', year: '2-digit' });
            }

            function sumForKey(key, type) {
                const items = auctionData.filter(a => {
                    if (!a.finish_date || !a.final_price) return false;
                    return type === 'day'
                        ? a.finish_date === key
                        : a.finish_date.startsWith(key);
                });
                if (type === 'roi')
                    return items.reduce((s, a) => s + (parseFloat(a.final_price) - parseFloat(a.investment_cost)), 0);
                return items.reduce((s, a) => s + parseFloat(a.final_price), 0);
            }

            if (period === '30d') {
                for (let i = 29; i >= 0; i--) {
                    const d = new Date(now); d.setDate(d.getDate() - i);
                    const key = d.toISOString().split('T')[0];
                    labels.push(key.slice(5));
                    roiData.push(sumForKey(key, 'roi'));
                    revenue.push(sumForKey(key, 'day'));
                }
            } else {
                const months = period === '6m' ? 5 : period === '1y' ? 11 : null;

                if (months !== null) {
                    for (let i = months; i >= 0; i--) {
                        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        const key = monthKey(d);
                        labels.push(monthLabel(d));
                        roiData.push(sumForKey(key, 'roi'));
                        revenue.push(sumForKey(key, 'month'));
                    }
                } else {
                    const keys = [...new Set(
                        auctionData
                            .filter(a => a.finish_date && a.final_price)
                            .map(a => a.finish_date.substring(0, 7))
                    )].sort();

                    if (keys.length === 0) keys.push(monthKey(now));

                    for (const key of keys) {
                        const [y, m] = key.split('-');
                        const d = new Date(parseInt(y), parseInt(m) - 1, 1);
                        labels.push(monthLabel(d));
                        roiData.push(sumForKey(key, 'roi'));
                        revenue.push(sumForKey(key, 'month'));
                    }
                }
            }

            if (chart) chart.destroy();

            chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Revenue (kr)',
                            data: revenue,
                            backgroundColor: 'rgba(79,142,247,0.55)',
                            borderColor: 'rgba(79,142,247,1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            order: 2,
                        },
                        {
                            label: 'ROI (kr)',
                            data: roiData,
                            type: 'line',
                            borderColor: 'rgba(39,174,96,1)',
                            backgroundColor: 'rgba(39,174,96,0.12)',
                            borderWidth: 2,
                            pointRadius: 4,
                            fill: false,
                            tension: 0.3,
                            order: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: ctx =>
                                    ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('nb-NO')} kr`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => v.toLocaleString('nb-NO') + ' kr' },
                        },
                    },
                },
            });
        }

        buildChart('30d');

        document.querySelectorAll('.period-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                buildChart(this.dataset.period);
            });
        });
    }

    // ── Edit sale modal (Live Sales) ──────────────────────────────
    const editModal = document.getElementById('edit-modal');
    if (editModal) {
        function openEditModal(btn) {
            document.getElementById('edit-id').value              = btn.dataset.id;
            document.getElementById('edit-link').value            = btn.dataset.link;
            document.getElementById('edit-brand').value           = btn.dataset.brand;
            document.getElementById('edit-model').value           = btn.dataset.model;
            document.getElementById('edit-production_year').value = btn.dataset.production_year;
            document.getElementById('edit-title').value           = btn.dataset.title;
            document.getElementById('edit-category').value        = btn.dataset.category;
            document.getElementById('edit-date_added').value      = btn.dataset.date_added;
            document.getElementById('edit-finish_date').value     = btn.dataset.finish_date;
            document.getElementById('edit-investment_cost').value = btn.dataset.investment_cost;
            document.getElementById('edit-sold').checked          = false;
            editModal.classList.remove('hidden');
        }

        function closeEditModal() { editModal.classList.add('hidden'); }

        document.querySelectorAll('.edit-sale-btn').forEach(btn => {
            btn.addEventListener('click', function () { openEditModal(this); });
        });

        document.getElementById('edit-cancel').addEventListener('click', closeEditModal);
        editModal.querySelector('.modal-backdrop').addEventListener('click', closeEditModal);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });

        // ── Finish sale (move to Finished Sales) ──────────────────
        document.querySelectorAll('.finish-sale-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const title = this.dataset.title || 'this sale';
                if (!confirm(`Move "${title}" to Finished Sales?`)) return;

                this.disabled = true;
                try {
                    const res  = await fetch('api/edit_auction.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: this.dataset.id, sold: true }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.href = '?page=finished';
                    } else {
                        alert('Failed to update. Please try again.');
                        this.disabled = false;
                    }
                } catch {
                    alert('Network error. Please try again.');
                    this.disabled = false;
                }
            });
        });

        document.getElementById('edit-save').addEventListener('click', async function () {
            this.disabled = true;
            const payload = {
                id:              document.getElementById('edit-id').value,
                link:            document.getElementById('edit-link').value,
                brand:           document.getElementById('edit-brand').value,
                model:           document.getElementById('edit-model').value,
                production_year: document.getElementById('edit-production_year').value,
                title:           document.getElementById('edit-title').value,
                category:        document.getElementById('edit-category').value,
                date_added:      document.getElementById('edit-date_added').value,
                finish_date:     document.getElementById('edit-finish_date').value,
                investment_cost: document.getElementById('edit-investment_cost').value,
                sold:            document.getElementById('edit-sold').checked,
            };

            try {
                const res  = await fetch('api/edit_auction.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to save changes. Please try again.');
                    this.disabled = false;
                }
            } catch {
                alert('Network error. Please try again.');
                this.disabled = false;
            }
        });
    }

    // ── Change password (Settings page) ──────────────────────────
    const pwForm = document.getElementById('change-password-form');
    if (pwForm) {
        pwForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const msg = document.getElementById('settings-message');
            const btn = pwForm.querySelector('button[type="submit"]');
            btn.disabled = true;

            const payload = {
                current_password:  document.getElementById('current_password').value,
                new_password:      document.getElementById('new_password').value,
                confirm_password:  document.getElementById('confirm_password').value,
            };

            try {
                const res  = await fetch('api/change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    msg.className = 'form-message success';
                    msg.textContent = 'Password changed successfully!';
                    msg.classList.remove('hidden');
                    pwForm.reset();
                } else {
                    msg.className = 'form-message error';
                    msg.textContent = data.error || 'Failed to change password.';
                    msg.classList.remove('hidden');
                }
            } catch {
                msg.className = 'form-message error';
                msg.textContent = 'Network error. Please try again.';
                msg.classList.remove('hidden');
            } finally {
                btn.disabled = false;
            }
        });
    }

    // ── Delete sale (Live & Finished pages) ───────────────────────
    document.querySelectorAll('.delete-sale-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const title = this.dataset.title || 'this sale';
            if (!confirm(`Delete "${title}"? This cannot be undone.`)) return;

            this.disabled = true;
            const id = this.dataset.id;
            try {
                const res  = await fetch('api/delete_auction.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to delete. Please try again.');
                    this.disabled = false;
                }
            } catch {
                alert('Network error. Please try again.');
                this.disabled = false;
            }
        });
    });

});

<?php
$auctions = get_auctions();
$live = array_filter($auctions, fn($a) => $a['status'] === 'live');
$finished = array_filter($auctions, fn($a) => $a['status'] === 'finished');
$sold = array_filter($finished, fn($a) => !empty($a['final_price']));

$total_invested = array_sum(array_column($auctions, 'investment_cost'));
$sold_invested = array_sum(array_column(array_values($sold), 'investment_cost'));
$total_earned = array_sum(array_column(array_values($sold), 'final_price'));
$roi = $total_earned - $sold_invested;

usort($auctions, fn($a, $b) => strcmp($b['date_added'], $a['date_added']));
$recent = array_slice($auctions, 0, 5);
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <span class="date-badge"><?= date('d. F Y') ?></span>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-gavel"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= count($live) ?></span>
            <span class="stat-label">Live Auctions</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= count($sold) ?></span>
            <span class="stat-label">Products Sold</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= number_format($total_invested, 0, ',', ' ') ?> kr</span>
            <span class="stat-label">Total Invested</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon <?= $roi >= 0 ? 'green' : 'red' ?>">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= ($roi >= 0 ? '+' : '') . number_format($roi, 0, ',', ' ') ?> kr</span>
            <span class="stat-label">Total ROI</span>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Recent Auctions</h2>
        <a href="?page=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
    </div>
    <?php if (empty($recent)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>No auctions yet. <a href="?page=add">Add your first auction</a>.</p>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Added</th>
                    <th>Finish Date</th>
                    <th>Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['date_added']) ?></td>
                    <td><?= htmlspecialchars($a['finish_date']) ?></td>
                    <td><?= htmlspecialchars($a['title'] ?? '') ?></td>
                    <td>
                        <span class="badge <?= $a['status'] === 'live' ? 'badge-live' : 'badge-finished' ?>">
                            <?= $a['status'] === 'live' ? 'Live' : 'Finished' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

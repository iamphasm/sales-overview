<?php
$auctions = get_auctions();
$finished = array_filter($auctions, fn($a) => $a['status'] === 'finished');
$sold = array_filter($finished, fn($a) => !empty($a['final_price']));
$not_sold = array_filter($finished, fn($a) => empty($a['final_price']));

$total_invested = array_sum(array_column($auctions, 'investment_cost'));
$sold_invested = array_sum(array_column(array_values($sold), 'investment_cost'));
$total_earned = array_sum(array_column(array_values($sold), 'final_price'));
$roi_total = $total_earned - $sold_invested;
$total_income = $total_earned;
?>

<div class="page-header">
    <h1>Statistics</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= number_format($total_invested, 0, ',', ' ') ?> kr</span>
            <span class="stat-label">Invested Total</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon <?= $roi_total >= 0 ? 'green' : 'red' ?>">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= ($roi_total >= 0 ? '+' : '') . number_format($roi_total, 0, ',', ' ') ?> kr</span>
            <span class="stat-label">ROI Total</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-handshake"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= count($sold) ?></span>
            <span class="stat-label">Total Products Sold</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= number_format($total_income, 0, ',', ' ') ?> kr</span>
            <span class="stat-label">Total Income</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <span class="stat-value"><?= count($not_sold) ?></span>
            <span class="stat-label">Total Products Not Sold</span>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Earnings Over Time</h2>
        <div class="period-tabs">
            <button class="period-tab active" data-period="30d">Last 30 Days</button>
            <button class="period-tab" data-period="6m">Last 6 Months</button>
            <button class="period-tab" data-period="1y">Over 1 Year</button>
            <button class="period-tab" data-period="all">All Time</button>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="earningsChart"></canvas>
    </div>
</div>

<script>
const auctionData = <?= json_encode(array_values($sold), JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>

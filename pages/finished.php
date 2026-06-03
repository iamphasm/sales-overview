<?php
$auctions = get_finished_auctions();
usort($auctions, fn($a, $b) => strcmp($b['finish_date'], $a['finish_date']));
?>

<div class="page-header">
    <h1>Finished Auctions</h1>
</div>

<div class="section">
    <?php if (empty($auctions)): ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <p>No finished auctions yet.</p>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Finish Date</th>
                    <th>Title</th>
                    <th>Brand / Model</th>
                    <th>Year</th>
                    <th>Invested</th>
                    <th>Final Price</th>
                    <th>ROI</th>
                    <th>Link</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auctions as $a):
                    $fp = !empty($a['final_price']) ? (float)$a['final_price'] : null;
                    $roi = $fp !== null ? $fp - (float)($a['investment_cost'] ?? 0) : null;
                ?>
                <tr>
                    <td><?= htmlspecialchars($a['finish_date']) ?></td>
                    <td><?= htmlspecialchars($a['title'] ?? '') ?></td>
                    <td class="text-muted"><?= htmlspecialchars(trim(($a['brand'] ?? '') . ' ' . ($a['model'] ?? ''))) ?: '—' ?></td>
                    <td><?= htmlspecialchars($a['production_year'] ?? '—') ?></td>
                    <td><?= number_format((float)($a['investment_cost'] ?? 0), 0, ',', ' ') ?> kr</td>
                    <td>
                        <div class="final-price-cell">
                            <?php if ($fp !== null): ?>
                                <span class="price-display"><?= number_format($fp, 0, ',', ' ') ?> kr</span>
                                <button class="btn-icon edit-price" data-id="<?= htmlspecialchars($a['id']) ?>" data-price="<?= (int)$fp ?>" title="Edit price">
                                    <i class="fas fa-edit"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline add-price-btn" data-id="<?= htmlspecialchars($a['id']) ?>">
                                    <i class="fas fa-plus"></i> Add price
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($roi !== null): ?>
                            <span class="<?= $roi >= 0 ? 'text-green' : 'text-red' ?>">
                                <?= ($roi >= 0 ? '+' : '') . number_format($roi, 0, ',', ' ') ?> kr
                            </span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $link_scheme = strtolower(parse_url($a['link'] ?? '', PHP_URL_SCHEME) ?? '');
                        if (in_array($link_scheme, ['http', 'https'], true)):
                        ?>
                        <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank" rel="noopener noreferrer" class="icon-link" title="Open auction">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-sale-btn"
                            data-id="<?= htmlspecialchars($a['id']) ?>"
                            data-title="<?= htmlspecialchars($a['title'] ?? '') ?>"
                            title="Delete sale">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div id="price-modal" class="modal hidden">
    <div class="modal-backdrop"></div>
    <div class="modal-box">
        <h3>Set Final Sale Price</h3>
        <input type="hidden" id="modal-auction-id">
        <div class="form-group">
            <label for="modal-price">Final Sale Price (kr)</label>
            <input type="number" id="modal-price" min="0" step="1" placeholder="0">
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" id="modal-cancel">Cancel</button>
            <button class="btn btn-primary" id="modal-save">Save</button>
        </div>
    </div>
</div>

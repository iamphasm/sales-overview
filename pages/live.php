<?php
$auctions = get_live_auctions();
usort($auctions, fn($a, $b) => strcmp($b['date_added'], $a['date_added']));
?>

<div class="page-header">
    <h1>Live Sales</h1>
    <a href="?page=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
</div>

<div class="section">
    <?php if (empty($auctions)): ?>
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <p>No live sales right now. <a href="?page=add">Add one</a>.</p>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Added</th>
                    <th>Finish Date</th>
                    <th>Link</th>
                    <th>Title</th>
                    <th>Production Year</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auctions as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['date_added']) ?></td>
                    <td><?= htmlspecialchars($a['finish_date']) ?></td>
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
                    <td><?= htmlspecialchars($a['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($a['production_year'] ?? '—') ?></td>
                    <td class="row-actions">
                        <button class="btn btn-sm btn-outline edit-sale-btn"
                            data-id="<?= htmlspecialchars($a['id']) ?>"
                            data-link="<?= htmlspecialchars($a['link'] ?? '') ?>"
                            data-brand="<?= htmlspecialchars($a['brand'] ?? '') ?>"
                            data-model="<?= htmlspecialchars($a['model'] ?? '') ?>"
                            data-production_year="<?= htmlspecialchars($a['production_year'] ?? '') ?>"
                            data-title="<?= htmlspecialchars($a['title'] ?? '') ?>"
                            data-category="<?= htmlspecialchars($a['category'] ?? '') ?>"
                            data-date_added="<?= htmlspecialchars($a['date_added'] ?? '') ?>"
                            data-finish_date="<?= htmlspecialchars($a['finish_date'] ?? '') ?>"
                            data-investment_cost="<?= htmlspecialchars($a['investment_cost'] ?? '') ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-success finish-sale-btn"
                            data-id="<?= htmlspecialchars($a['id']) ?>"
                            data-title="<?= htmlspecialchars($a['title'] ?? '') ?>"
                            title="Move to Finished Sales">
                            <i class="fas fa-check"></i>
                        </button>
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

<!-- Edit modal -->
<div id="edit-modal" class="modal hidden">
    <div class="modal-backdrop"></div>
    <div class="modal-box modal-box-wide">
        <h3>Edit Sale</h3>
        <input type="hidden" id="edit-id">

        <div class="form-row">
            <div class="form-group flex-2">
                <label for="edit-link">Link to Sale</label>
                <input type="url" id="edit-link" placeholder="https://...">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="edit-brand">Brand</label>
                <input type="text" id="edit-brand">
            </div>
            <div class="form-group">
                <label for="edit-model">Model</label>
                <input type="text" id="edit-model">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="edit-production_year">Production Year</label>
                <input type="number" id="edit-production_year" min="1900" max="<?= date('Y') + 1 ?>">
            </div>
            <div class="form-group">
                <label for="edit-category">Category</label>
                <input type="text" id="edit-category">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group flex-2">
                <label for="edit-title">Title</label>
                <input type="text" id="edit-title">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="edit-date_added">Date Added</label>
                <input type="date" id="edit-date_added">
            </div>
            <div class="form-group">
                <label for="edit-finish_date">Finish Date</label>
                <input type="date" id="edit-finish_date">
                <label class="checkbox-label">
                    <input type="checkbox" id="edit-sold"> Mark as sold — move to Finished Sales
                </label>
            </div>
            <div class="form-group">
                <label for="edit-investment_cost">Investment Cost (kr)</label>
                <input type="number" id="edit-investment_cost" min="0" step="1">
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn btn-outline" id="edit-cancel">Cancel</button>
            <button class="btn btn-primary" id="edit-save"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>
